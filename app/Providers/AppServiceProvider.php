<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $repositories = [];

    /**
     * AppServiceProvider constructor.
     * @param $app
     */
    public function __construct($app)
    {
        parent::__construct($app);

        // Init app services
        $this->initServices();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
        $this->registerServices();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    public function initServices()
    {
        $serviceBindings = config('services.bindings');

        foreach ($serviceBindings as $modelClass => $binding) {
            $contract = $binding['contract'] ?? null;
            $repository = $binding['repository'] ?? null;

            $this->repositories[] = [$contract, $repository, $modelClass];
        }
    }

    protected function registerServices()
    {
        // Register repository
        foreach ($this->repositories as $repository) {
            list($contract, $repository, $modelClass) = $repository;
            $this->registerRepository($contract, $repository, $modelClass);
        }
    }

    protected function registerRepository($contract, $repository, $model)
    {
        if (!class_exists($model) || !interface_exists($contract) || !class_exists($repository)) {
            return;
        }

        $this->app->bind($contract, function () use ($repository, $model) {
           return new $repository(new $model);
        });
    }
}
