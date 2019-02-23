<?php namespace Cucurbit\MenuManager\Repository;

use Cucurbit\MenuManager\Module;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Collection;
use Symfony\Component\Yaml\Yaml;

class Modules extends Collection
{
	/**
	 * @var Filesystem
	 */
	protected $files;

	public function initialize(Collection $modules)
	{
		$cache_name = $this->cacheName();

		$this->items = \Cache::rememberForever($cache_name, function () use ($modules) {
			$this->files = app('files');

			$menus = collect();
			$modules->each(function ($module_info, $module_name) use ($menus) {
				$Module = new Module($module_name);

				$configures = $this->loadConfigures($Module->directory());

				if ($configures->isNotEmpty()) {
					$configures->each(function ($content, $group) use ($Module) {
						$Module->offsetSet($group, $content);
					});
				}

				$menus->put($module_name, $Module);
			});

			return $menus->all();

		});
	}

	/**
	 * 加载配置文件
	 *
	 * @param $directory
	 *
	 * @return Collection
	 */
	protected function loadConfigures($directory): Collection
	{
		$directory = $directory . DIRECTORY_SEPARATOR . 'Config';

		if ($this->files->isDirectory($directory)) {
			$configure = collect();

			$files = collect($this->files->files($directory));

			$files->each(function ($file) use ($configure) {
				$name = basename(realpath($file), '.yaml');

				$extension = strtolower($file->getExtension());
				if (($extension === 'yaml' || $extension === 'yml') && $this->files->isReadable($file)) {
					$configure->put($name, Yaml::parse(file_get_contents($file)));
				}
			});

			return $configure;
		}
		return collect();
	}

	/**
	 * cache name
	 * @return string
	 */
	private function cacheName(): string
	{
		return __CLASS__ . '_modules';
	}
}