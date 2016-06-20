<?php

namespace Jiyis\Generator;

use Illuminate\Support\ServiceProvider;
use Jiyis\Generator\Commands\API\APIControllerGeneratorCommand;
use Jiyis\Generator\Commands\API\APIGeneratorCommand;
use Jiyis\Generator\Commands\API\APIRequestsGeneratorCommand;
use Jiyis\Generator\Commands\API\TestsGeneratorCommand;
use Jiyis\Generator\Commands\APIScaffoldGeneratorCommand;
use Jiyis\Generator\Commands\Common\MigrationGeneratorCommand;
use Jiyis\Generator\Commands\Common\ModelGeneratorCommand;
use Jiyis\Generator\Commands\Common\RepositoryGeneratorCommand;
use Jiyis\Generator\Commands\Publish\GeneratorPublishCommand;
use Jiyis\Generator\Commands\Publish\LayoutPublishCommand;
use Jiyis\Generator\Commands\Publish\PublishTemplateCommand;
use Jiyis\Generator\Commands\RollbackGeneratorCommand;
use Jiyis\Generator\Commands\Scaffold\ControllerGeneratorCommand;
use Jiyis\Generator\Commands\Scaffold\RequestsGeneratorCommand;
use Jiyis\Generator\Commands\Scaffold\ScaffoldGeneratorCommand;
use Jiyis\Generator\Commands\Scaffold\ViewsGeneratorCommand;

class JiyisGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $configPath = __DIR__.'/../config/laravel_generator.php';

        $this->publishes([
            $configPath => config_path('jiyis/laravel_generator.php'),
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('jiyis.publish', function ($app) {
            return new GeneratorPublishCommand();
        });

        $this->app->singleton('jiyis.api', function ($app) {
            return new APIGeneratorCommand();
        });

        $this->app->singleton('jiyis.scaffold', function ($app) {
            return new ScaffoldGeneratorCommand();
        });

        $this->app->singleton('jiyis.publish.layout', function ($app) {
            return new LayoutPublishCommand();
        });

        $this->app->singleton('jiyis.publish.templates', function ($app) {
            return new PublishTemplateCommand();
        });

        $this->app->singleton('jiyis.api_scaffold', function ($app) {
            return new APIScaffoldGeneratorCommand();
        });

        $this->app->singleton('jiyis.migration', function ($app) {
            return new MigrationGeneratorCommand();
        });

        $this->app->singleton('jiyis.model', function ($app) {
            return new ModelGeneratorCommand();
        });

        $this->app->singleton('jiyis.repository', function ($app) {
            return new RepositoryGeneratorCommand();
        });

        $this->app->singleton('jiyis.api.controller', function ($app) {
            return new APIControllerGeneratorCommand();
        });

        $this->app->singleton('jiyis.api.requests', function ($app) {
            return new APIRequestsGeneratorCommand();
        });

        $this->app->singleton('jiyis.api.tests', function ($app) {
            return new TestsGeneratorCommand();
        });

        $this->app->singleton('jiyis.scaffold.controller', function ($app) {
            return new ControllerGeneratorCommand();
        });

        $this->app->singleton('jiyis.scaffold.requests', function ($app) {
            return new RequestsGeneratorCommand();
        });

        $this->app->singleton('jiyis.scaffold.views', function ($app) {
            return new ViewsGeneratorCommand();
        });

        $this->app->singleton('jiyis.rollback', function ($app) {
            return new RollbackGeneratorCommand();
        });

        $this->commands([
            'jiyis.publish',
            'jiyis.api',
            'jiyis.scaffold',
            'jiyis.api_scaffold',
            'jiyis.publish.layout',
            'jiyis.publish.templates',
            'jiyis.migration',
            'jiyis.model',
            'jiyis.repository',
            'jiyis.api.controller',
            'jiyis.api.requests',
            'jiyis.api.tests',
            'jiyis.scaffold.controller',
            'jiyis.scaffold.requests',
            'jiyis.scaffold.views',
            'jiyis.rollback',
        ]);
    }
}
