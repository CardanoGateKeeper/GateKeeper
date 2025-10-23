<?php

namespace Tests;

use Laravel\Dusk\TestCase as BaseTestCase;
use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
  use CreatesApplication;

  #[BeforeClass]
  public static function prepare(): void
  {
    if (! static::runningInSail()) {
      static::startChromeDriver(['--port=9515']);
    }
  }

  protected static function chromeDriverBinary()
  {
    return '/usr/local/bin/chromedriver'; // adjust path as needed
  }

  protected function driver(): RemoteWebDriver
  {
    $options = (new ChromeOptions)->addArguments([
      '--window-size=1920,1080',
      '--disable-gpu',
      '--headless',
    ]);

    return RemoteWebDriver::create(
      env('DUSK_DRIVER_URL', 'http://localhost:9515'),
      DesiredCapabilities::chrome()->setCapability(
        ChromeOptions::CAPABILITY, $options
      )
    );
  }
}
