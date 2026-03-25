<?php

declare(strict_types=1);

namespace DataShaman\Claude\AgentLaravel;

use DataShaman\Claude\AgentLaravel\Commands\QueryCommand;
use DataShaman\Claude\AgentLaravel\Commands\SessionsListCommand;
use DataShaman\Claude\AgentLaravel\Commands\SessionsShowCommand;
use Illuminate\Support\ServiceProvider;

class ClaudeServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/claude.php', 'claude');

        $this->app->singleton(ClaudeManager::class, function ($app) {
            return new ClaudeManager($app['config']->get('claude', []));
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/claude.php' => config_path('claude.php'),
        ], 'claude-config');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/claude'),
        ], 'claude-views');

        if ($this->app->runningInConsole()) {
            $this->commands([
                QueryCommand::class,
                SessionsListCommand::class,
                SessionsShowCommand::class,
            ]);
        }

        if ($this->app['config']->get('claude.streaming.enabled', true)) {
            $this->loadRoutesFrom(__DIR__.'/../routes/claude.php');
        }

        $this->loadViewsFrom(__DIR__.'/../resources/views', 'claude');

        if (class_exists(\Livewire\Livewire::class)) {
            \Livewire\Livewire::component('claude-chat', Livewire\ClaudeChat::class);
        }
    }
}
