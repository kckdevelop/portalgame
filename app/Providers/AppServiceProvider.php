<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Auto-create SQLite database file if it does not exist on server deployment
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            if ($dbPath && $dbPath !== ':memory:' && !file_exists($dbPath)) {
                $dir = dirname($dbPath);
                if (!file_exists($dir)) {
                    @mkdir($dir, 0777, true);
                }
                @touch($dbPath);
                @chmod($dbPath, 0666);
            }
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
