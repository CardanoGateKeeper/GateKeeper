<?php

  namespace App\Sidecar;

  use Hammerstone\Sidecar\LambdaFunction;
  use Hammerstone\Sidecar\Package;

  class SignData extends LambdaFunction
  {
    public function handler(): string
    {
      return 'resources/lambda/signData.validate';
    }

    public function package(): Package
    {
//      return [
//        'lambda/signData.js'
//      ];

      return Package::make()
                    ->setBasePath(resource_path('lambda'))
                    ->include('*');
    }
  }

