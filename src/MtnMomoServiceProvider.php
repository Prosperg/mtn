<?php

namespace Pkl\Mtn\MomoSdk;

use Illuminate\Support\ServiceProvider;

class MtnMomoServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Configuration de votre package
        $this->mergeConfigFrom(
            __DIR__.'/../config/mtn-momo.php', 'mtn-momo'
        );
    }

    public function boot()
    {
        // Publier les fichiers de configuration seulement si l'application utilise Laravel
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mtn-momo.php' => $this->app->configPath('mtn-momo.php'),
            ], 'mtn-momo-config');
        }
    }
}