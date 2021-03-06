<?php

namespace Nahid\Talk;

use Illuminate\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;
use Nahid\Talk\Conversations\ConversationRepository;
use Nahid\Talk\Messages\MessageRepository;

class TalkServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
        $this->setupMigrations();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTalk();
    }

    /**
     * Setup the config.
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/talk.php');
        // Check if the application is a Laravel OR Lumen instance to properly merge the configuration file.
        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('talk.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('talk');
        }
        $this->mergeConfigFrom($source, 'talk');
    }

    /**
     * Publish migrations files.
     */
    protected function setupMigrations()
    {
        $this->publishes([
            realpath(__DIR__ . '/../database/migrations/') => database_path('migrations')
        ], 'migrations');
    }

    /**
     * Register Talk class.
     *
     * @return void
     */
    protected function registerTalk()
    {
        $this->app->singleton('talk', function (Container $app) {
            return new Talk($app[ConversationRepository::class], $app[MessageRepository::class]);
        });

        $this->app->alias('talk', Talk::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return ['talk'];
    }
}
