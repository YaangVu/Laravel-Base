<?php

namespace YaangVu\LaravelBase\Provider;

use Illuminate\Support\ServiceProvider;
use YaangVu\LaravelBase\Generator\Console\BaseMakeCommand;
use YaangVu\LaravelBase\Generator\Console\CastMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ChannelMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ConsoleMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ControllerMakeCommand;
use YaangVu\LaravelBase\Generator\Console\DumpCommand;
use YaangVu\LaravelBase\Generator\Console\EventMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ExceptionMakeCommand;
use YaangVu\LaravelBase\Generator\Console\FactoryMakeCommand;
use YaangVu\LaravelBase\Generator\Console\JobMakeCommand;
use YaangVu\LaravelBase\Generator\Console\KeyGenerateCommand;
use YaangVu\LaravelBase\Generator\Console\ListenerMakeCommand;
use YaangVu\LaravelBase\Generator\Console\MailMakeCommand;
use YaangVu\LaravelBase\Generator\Console\MiddlewareMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ModelMakeCommand;
use YaangVu\LaravelBase\Generator\Console\NotificationMakeCommand;
use YaangVu\LaravelBase\Generator\Console\NotificationTableCommand;
use YaangVu\LaravelBase\Generator\Console\PipeMakeCommand;
use YaangVu\LaravelBase\Generator\Console\PolicyMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ProviderMakeCommand;
use YaangVu\LaravelBase\Generator\Console\RequestMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ResourceMakeCommand;
use YaangVu\LaravelBase\Generator\Console\RuleMakeCommand;
use YaangVu\LaravelBase\Generator\Console\SeederMakeCommand;
use YaangVu\LaravelBase\Generator\Console\ServeCommand;
use YaangVu\LaravelBase\Generator\Console\ServiceMakeCommand;
use YaangVu\LaravelBase\Generator\Console\TestMakeCommand;

class GeneratorServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected array $commands
        = [
            // 'FactoryMake' => 'command.factory.yaangvu',
        ];

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected array $devCommands
        = [
            'ConsoleMake'       => 'command.console.yaangvu',
            'ControllerMake' => 'command.controller.yaangvu',
            'ServiceMake'       => 'command.service.yaangvu',
        //*    // 'EventMake'         => 'command.event.yaangvu',
            'ExceptionMake'     => 'command.exception.yaangvu',
            'RequestMake'       => 'command.request.yaangvu',
            'JobMake'           => 'command.job.yaangvu',
        //*    // 'ListenerMake'      => 'command.listener.yaangvu',
            'MailMake'          => 'command.mail.yaangvu',
            'MiddlewareMake' => 'command.middleware.yaangvu',
            // 'PipeMake'          => 'command.pipe.yaangvu',
            'ModelMake'      => 'command.model.yaangvu',
            'PolicyMake'        => 'command.policy.yaangvu',
            'ProviderMake'      => 'command.provider.yaangvu',
            // 'ResourceMake'      => 'command.resource.yaangvu',
            'NotificationMake'  => 'command.notification.yaangvu',
            // 'NotificationTable' => 'command.notification.table',
            'ChannelMake'       => 'command.channel.yaangvu',
            // 'SchemaDump'        => 'command.schema.dump',
            'CastMake'          => 'command.cast.yaangvu',
            'RuleMake'          => 'command.rule.yaangvu',
            'BaseMake'       => 'command.base.yaangvu',
        ];

    /**
     * Register the service provider.
     */
    public function register()
    {
        $this->registerCommands($this->commands);
        $this->registerCommands($this->devCommands);
    }

    /**
     * Register the given commands.
     *
     * @param array $commands
     */
    protected function registerCommands(array $commands)
    {
        foreach (array_keys($commands) as $command) {
            $method = "register{$command}Command";

            call_user_func_array([$this, $method], []);
        }

        $this->commands(array_values($commands));
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides(): array
    {
        if ($this->app->environment('production')) {
            return array_values($this->commands);
        } else {
            return array_merge(array_values($this->commands), array_values($this->devCommands));
        }
    }

    /**
     * Register the command.
     */
    protected function registerConsoleMakeCommand()
    {
        $this->app->singleton('command.console.yaangvu', function ($app) {
            return new ConsoleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerControllerMakeCommand()
    {
        $this->app->singleton('command.controller.yaangvu', function ($app) {
            return new ControllerMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerEventMakeCommand()
    {
        $this->app->singleton('command.event.yaangvu', function ($app) {
            return new EventMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerExceptionMakeCommand()
    {
        $this->app->singleton('command.exception.yaangvu', function ($app) {
            return new ExceptionMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerFactoryMakeCommand()
    {
        $this->app->singleton('command.factory.yaangvu', function ($app) {
            return new FactoryMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerJobMakeCommand()
    {
        $this->app->singleton('command.job.yaangvu', function ($app) {
            return new JobMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerKeyGenerateCommand()
    {
        $this->app->singleton('command.key.generate', function () {
            return new KeyGenerateCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerListenerMakeCommand()
    {
        $this->app->singleton('command.listener.yaangvu', function ($app) {
            return new ListenerMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerMailMakeCommand()
    {
        $this->app->singleton('command.mail.yaangvu', function ($app) {
            return new MailMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerMiddlewareMakeCommand()
    {
        $this->app->singleton('command.middleware.yaangvu', function ($app) {
            return new MiddlewareMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerRequestMakeCommand()
    {
        $this->app->singleton('command.request.yaangvu', function ($app) {
            return new RequestMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerPipeMakeCommand()
    {
        $this->app->singleton('command.pipe.yaangvu', function ($app) {
            return new PipeMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerModelMakeCommand()
    {
        $this->app->singleton('command.model.yaangvu', function ($app) {
            return new ModelMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerProviderMakeCommand()
    {
        $this->app->singleton('command.provider.yaangvu', function ($app) {
            return new ProviderMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerSeederMakeCommand()
    {
        $this->app->singleton('command.seeder.yaangvu', function ($app) {
            return new SeederMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerServeCommand()
    {
        $this->app->singleton('command.serve', function () {
            return new ServeCommand();
        });
    }

    /**
     * Register the command.
     */
    protected function registerTestMakeCommand()
    {
        $this->app->singleton('command.test.yaangvu', function ($app) {
            return new TestMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerResourceMakeCommand()
    {
        $this->app->singleton('command.resource.yaangvu', function ($app) {
            return new ResourceMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerPolicyMakeCommand()
    {
        $this->app->singleton('command.policy.yaangvu', function ($app) {
            return new PolicyMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerNotificationTableCommand()
    {
        $this->app->singleton('command.notification.table', function ($app) {
            return new NotificationTableCommand($app['files'], $app['composer']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerNotificationMakeCommand()
    {
        $this->app->singleton('command.notification.yaangvu', function ($app) {
            return new NotificationMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerChannelMakeCommand()
    {
        $this->app->singleton('command.channel.yaangvu', function ($app) {
            return new ChannelMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerSchemaDumpCommand()
    {
        $this->app->singleton('command.schema.dump', function () {
            return new DumpCommand();
        });
    }

    protected function registerCastMakeCommand()
    {
        $this->app->singleton('command.cast.yaangvu', function ($app) {
            return new CastMakeCommand($app['files']);
        });
    }

    protected function registerRuleMakeCommand()
    {
        $this->app->singleton('command.rule.yaangvu', function ($app) {
            return new RuleMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerServiceMakeCommand()
    {
        $this->app->singleton('command.service.yaangvu', function ($app) {
            return new ServiceMakeCommand($app['files']);
        });
    }

    /**
     * Register the command.
     */
    protected function registerBaseMakeCommand()
    {
        $this->app->singleton('command.base.yaangvu', function ($app) {
            return new BaseMakeCommand($app['files']);
        });
    }
}
