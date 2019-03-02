<?php namespace Cucurbit\ModuleManager;

use ArrayAccess;
use Cucurbit\ModuleManager\Traits\AttributeTrait;
use Illuminate\Contracts\Support\Arrayable;

class Module implements Arrayable, ArrayAccess, \JsonSerializable
{
	use AttributeTrait;

	public function __construct($module)
	{
		$this->attributes = [
			'directory' => module_path($module),
			'namespace' => module_class($module),
			'module' => $module,
		];
	}

	public function directory()
	{
		return $this->attributes['directory'];
	}

	public function namespace()
	{
		return $this->attributes['namespace'];
	}

	public function module()
	{
		return $this->attributes['module'];
	}

}