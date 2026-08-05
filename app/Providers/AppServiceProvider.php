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
        // Auto-handle SQLite database path & file creation on PaaS server deployment
        if (config('database.default') === 'sqlite') {
            $dbPath = config('database.connections.sqlite.database');
            
            if ($dbPath && $dbPath !== ':memory:') {
                // If the path is a directory (e.g. from Docker volume mount to /database or /storage), append database.sqlite
                if (is_dir($dbPath)) {
                    $dbPath = rtrim($dbPath, '/\\') . DIRECTORY_SEPARATOR . 'database.sqlite';
                    config(['database.connections.sqlite.database' => $dbPath]);
                }

                $dir = dirname($dbPath);
                if (!file_exists($dir)) {
                    @mkdir($dir, 0777, true);
                }

                if (!file_exists($dbPath) && !is_dir($dbPath)) {
                    @touch($dbPath);
                    @chmod($dbPath, 0666);
                }
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
