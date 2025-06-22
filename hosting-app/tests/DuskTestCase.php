<?php

namespace Tests;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Laravel\Dusk\TestCase as BaseTestCase;
use PHPUnit\Framework\Attributes\BeforeClass;

abstract class DuskTestCase extends BaseTestCase
{
    use DatabaseMigrations;

    #[BeforeClass]
    public static function prepare(): void
    {
        if (! static::runningInSail()) {
            static::startChromeDriver(['--port=9515']);
        }
    }
    protected function baseUrl()
    {
        return config('app.url');
    }
    protected function setUp(): void
    {
        parent::setUp();

        \Laravel\Dusk\Browser::$storeScreenshotsAt = base_path('tests/Browser/screenshots');
        // Устанавливаем специальные настройки для Dusk
        config(['session.domain' => parse_url(config('app.url'), PHP_URL_HOST)]);

        // Очищаем куки перед каждым тестом
//        Browser::$storeScreenshotsAt = storage_path('dusk-screenshots');
//        Browser::$storeConsoleLogAt = storage_path('dusk-console');
//        Browser::$storeSourceAt = storage_path('dusk-source');

        static::startChromeDriver();
    }
    /**
     * Create the RemoteWebDriver instance.
     */
    protected function driver(): RemoteWebDriver
    {
        $options = (new ChromeOptions)->addArguments(collect([
            $this->shouldStartMaximized() ? '--start-maximized' : '--window-size=1920,1080',
            '--disable-search-engine-choice-screen',
            '--disable-smooth-scrolling',
        ])->unless($this->hasHeadlessDisabled(), function (Collection $items) {
            return $items->merge([
                '--disable-gpu',
                //                '--headless=new',
            ]);
        })->all());

        return RemoteWebDriver::create(
            $_ENV['DUSK_DRIVER_URL'] ?? env('DUSK_DRIVER_URL') ?? 'http://localhost:9515',
            DesiredCapabilities::chrome()->setCapability(
                ChromeOptions::CAPABILITY, $options
            )
        );
    }
}
