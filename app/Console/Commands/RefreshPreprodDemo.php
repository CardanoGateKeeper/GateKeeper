<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class RefreshPreprodDemo extends Command
{
  protected $signature = 'preprod:refresh-demo';
  protected $description = 'Drop and reseed the database for the preprod demo environment.';

  public function handle(): int
  {
    if (! app()->environment('preprod')) {
      $this->error('This command is only allowed in the preprod environment.');
      return self::FAILURE;
    }

    $this->info('Refreshing preprod database (migrate:fresh --seed)...');

    Artisan::call('migrate:fresh', [
      '--seed'  => true,
      '--force' => true,
    ]);

    $this->line(Artisan::output());

    $this->info('Preprod database refresh complete.');
    return self::SUCCESS;
  }
}
