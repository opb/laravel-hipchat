<?php namespace Opb\LaravelHipchat;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
use HipChat\HipChat;

class HipchatNotifierServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->publishes([
			__DIR__.'/../../config/config.php' => config_path('laravel-hipchat.php'),
		]);
		$this->app->bindShared('hipchat-notifier', function($app){
            $options = $app['config']->get('laravel-hipchat.config');

            $token = $options['apiToken'];
            unset($options['apiToken']);
            $room = $options['room'];
            unset($options['room']);

            $hipchat = new HipChat($token);
            $queue = $app['queue'];

            return new HipchatNotifier(
                $hipchat,
                $queue,
                $options,
                $room
            );
        });
	}

    public function boot()
    {
        // $this->package('opb/laravel-hipchat');
        AliasLoader::getInstance()->alias(
            'HipchatNotifier',
            'Opb\LaravelHipchat\Facades\HipchatNotifier'
        );
    }


	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return ['hipchat-notifier'];
	}

}
