<?php

  namespace App\Http\Controllers;

  use Illuminate\Http\Request;
  use Illuminate\Support\Facades\Cache;
  use Illuminate\Support\Facades\Config;

  class ImageController extends Controller
  {

    private string|null $domain;
    private string|null $key;

    public function __construct()
    {
      $this->domain = Config::get('app.nftcdn_domain');
      $this->key = base64_decode(Config::get('app.nftcdn_key'));
    }

    public function show(string $asset_key, Request $request)
    {
      $cache_key = "{$asset_key}_url";

      $url = Cache::get($cache_key);

      if (!$url) {
        $url = $this->get_image_url($asset_key, ['size' => 512]);
        Cache::set($cache_key, $url, 86400);
      }

      return redirect($url, 301);
    }

    private function base64_url_encode($data): string
    {
      $b64 = base64_encode($data);
      $url = strtr($b64, '+/', '-_');

      return rtrim($url, '=');
    }

    private function build_url($token, $uri, $params): string
    {
      $query = http_build_query($params);

      return "https://{$token}.{$this->domain}.nftcdn.io{$uri}?{$query}";
    }

    private function make_hmac($url)
    {
      $url_hash = hash_hmac('sha256', $url, $this->key, true);

      return $this->base64_url_encode($url_hash);
    }

    private function make_url($token, $uri, $params)
    {
      $params['tk'] = '';
      $url = $this->build_url($token, $uri, $params);
      $params['tk'] = $this->make_hmac($url);

      return $this->build_url($token, $uri, $params);
    }

    public function get_image_url($token, $params = []): string
    {

      return $this->make_url($token, '/image', $params);
    }

    public function get_metadata_url($token, $params = []): string
    {

      return $this->make_url($token, '/metadata', $params);
    }

  }
