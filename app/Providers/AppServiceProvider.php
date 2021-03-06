<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;
use App\Models\Config;
use Illuminate\Support\Facades\Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // get configs
        $cfg = $this->app['config'];
        // check app enverionment
        // if env equae production
        if ($cfg['app.env'] === 'production') {
            // set base url
            app()->get('url')->forceRootUrl($cfg['app.url']);
        }
        
        try {
            DB::getPdo();
            config([
                'global' => Config::all(['name', 'value'])
                    ->keyBy('name')
                    ->transform(function ($config) {
                        return $config->value;
                    })
                    ->toArray()
            ]);
        } catch (\Exception $e) {
            Log::error($e->getMessage());
        }
    }
}
