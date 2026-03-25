<?php

use DataShaman\Claude\AgentLaravel\Http\Controllers\ClaudeStreamController;
use Illuminate\Support\Facades\Route;

$prefix = config('claude.streaming.route_prefix', 'claude');
$middleware = config('claude.streaming.middleware', ['web']);

Route::middleware($middleware)
    ->prefix($prefix)
    ->group(function () {
        Route::post('/stream', [ClaudeStreamController::class, 'stream'])
            ->name('claude.stream');
    });
