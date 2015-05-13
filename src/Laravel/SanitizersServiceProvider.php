<?php namespace Lukzgois\Sanitizer\Laravel;

use Illuminate\Support\ServiceProvider;

class SanitizerServiceProvider extends ServiceProvider
{
	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('command.lukzgois.generate', function ($app) {
			return $app['Lukzgois\Sanitizer\Laravel\Commands\GenerateSanitizerCommand'];
		});
		$this->commands('command.lukzgois.generate');
	}
}
