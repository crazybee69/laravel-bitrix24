<?php

namespace Crazybee47\Laravel\Bitrix24;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BitrixServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->registerRoutes();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(BitrixService::class, function ($app) {
            return new BitrixService(
                $app->config->get('services.bitrix'),
            );
        });
    }

    /**
     * Register the Passport routes.
     *
     * @return void
     */
    protected function registerRoutes()
    {
        Route::group([
            'prefix' => 'bitrix',
            'as' => 'bitrix.',
        ], function () {
            $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
        });
    }
}
