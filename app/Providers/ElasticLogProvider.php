<?php

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;
use Monolog\Formatter\ElasticsearchFormatter;
use Monolog\Handler\ElasticsearchHandler;

class ElasticLogProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $index = rtrim(config('elastic_log.prefix'), '_') . '_' . config('elastic_log.index');
        $type = config('elastic_log.type');

        $this->app->bind(Client::class, function ($app) {
            return ClientBuilder::create()->setHosts([config('elastic_log.host')])->build();
        });

        $this->app->bind(ElasticsearchFormatter::class, function ($app) use ($index, $type) {
            return new ElasticsearchFormatter($index, $type);
        });

        $this->app->bind(ElasticsearchHandler::class, function ($app) use ($index, $type) {
            return new ElasticsearchHandler($app->make(Client::class), [
                'index'        => $index,
                'type'         => $type,
                'ignore_error' => false,
            ]);
        });
    }
}
