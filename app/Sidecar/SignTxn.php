<?php

  namespace App\Sidecar;

  use Hammerstone\Sidecar\LambdaFunction;
  use Hammerstone\Sidecar\Package;

  class SignTxn extends LambdaFunction
  {
    public function handler(): string
    {
      return 'resources/lambda/signTxn.validate';
    }

    public function package(): Package
    {
      return Package::make()
                    ->setBasePath(resource_path('lambda'))
                    ->include('*');
    }
  }
