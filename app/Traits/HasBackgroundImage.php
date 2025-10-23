<?php

  namespace App\Traits;

  use Illuminate\Database\Eloquent\Casts\Attribute;
  use Illuminate\Http\UploadedFile;
  use Illuminate\Support\Facades\Storage;
  use Laravel\Jetstream\Features;

  trait HasBackgroundImage
  {
    /**
     * Update the user's profile photo.
     *
     * @param UploadedFile $photo
     * @param string $storagePath
     * @return void
     */
    public function updateBgImage(UploadedFile $photo, string $storagePath = 'bg-images'): void
    {
      tap($this->bg_image_path, function ($previous) use ($photo, $storagePath) {
        $this->forceFill([
          'bg_image_path' => $photo->storePublicly(
            $storagePath, ['disk' => $this->BgImageDisk()]
          ),
        ])->save();

        if ($previous) {
          Storage::disk($this->BgImageDisk())->delete($previous);
        }
      });
    }

    /**
     * Delete the user's profile photo.
     *
     * @return void
     */
    public function deleteBgImage()
    {
      if (! Features::managesProfilePhotos()) {
        return;
      }

      if (is_null($this->bg_image_path)) {
        return;
      }

      Storage::disk($this->BgImageDisk())->delete($this->bg_image_path);

      $this->forceFill([
        'bg_image_path' => null,
      ])->save();
    }

    /**
     * Get the URL to the user's profile photo.
     *
     * @return Attribute
     */
    public function BgImageUrl(): Attribute
    {
      return Attribute::get(function (): string {
        return $this->bg_image_path
          ? Storage::disk($this->BgImageDisk())->url($this->bg_image_path)
          : $this->defaultBgImageUrl();
      });
    }

    /**
     * Get the default profile photo URL if no profile photo has been uploaded.
     *
     * @return string
     */
    protected function defaultBgImageUrl(): string
    {
      $id = $this->uuid ?? hash('sha256', $this->id);

      return "https://picsum.photos/seed/{$id}/2048/1080";
    }

    /**
     * Get the disk that profile photos should be stored on.
     *
     * @return string
     */
    protected function BgImageDisk(): string
    {
      return isset($_ENV['VAPOR_ARTIFACT_NAME']) ? 's3' : config('jetstream.bg_image_disk', 'public');
    }
  }
