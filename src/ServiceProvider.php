<?php

namespace Cucurbit\ModuleManager;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;

class ServiceProvider extends BaseServiceProvider
{

	public function register()
	{
		$this->registerService();
	}

	private function registerService()
	{
		$this->app->singleton('cucurbit.module', function($app) {
			return new ModuleManager();
		});
	}

	public function provides()
	{
		return ['cucurbit.module'];
	}
}