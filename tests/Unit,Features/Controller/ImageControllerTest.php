<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use App\Models\Policy;

class ImageControllerTest extends TestCase
{
  public function test_show_redirects_to_cached_url_if_present()
  {
    $assetKey = 'test-asset-key';
    $cachedUrl = 'https://cached-url.example.com/image';

    Cache::shouldReceive('get')
      ->once()
      ->with("{$assetKey}_url")
      ->andReturn($cachedUrl);

    Cache::shouldReceive('set')->never();

    $response = $this->get(route('image.show', $assetKey));

    $response->assertStatus(301);
    $response->assertRedirect($cachedUrl);
  }

  public function test_show_caches_and_redirects_when_no_cache()
  {
    $assetKey = 'new-asset-key';

    // Expect Cache get returns null (no cache)
    Cache::shouldReceive('get')
      ->once()
      ->with("{$assetKey}_url")
      ->andReturn(null);

    // We don’t know the exact generated URL, so we'll fake the cache set with any string
    Cache::shouldReceive('set')
      ->once()
      ->withArgs(function ($key, $value, $ttl) use ($assetKey) {
        return $key === "{$assetKey}_url" && is_string($value) && $ttl === 86400;
      });

    // We'll mock the controller method get_image_url to return a dummy url
    $this->partialMock(\App\Http\Controllers\ImageController::class, function ($mock) use ($assetKey) {
      $mock->shouldAllowMockingProtectedMethods()
        ->shouldReceive('get_image_url')
        ->once()
        ->with($assetKey, ['size' => 512])
        ->andReturn('https://generated-url.example.com/image');
    });

    $response = $this->get(route('image.show', $assetKey));

    $response->assertStatus(301);
    $response->assertRedirect('https://generated-url.example.com/image');
  }
}
