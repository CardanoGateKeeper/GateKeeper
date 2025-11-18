<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Any global seeding goes here, e.g. default roles/permissions/user, etc.

    if (app()->environment('local')) {
      $this->call(DemoEventSeeder::class);
    }
  }
}
