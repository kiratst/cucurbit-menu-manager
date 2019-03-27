<?php

namespace Cucurbit\ModuleManager;

use Cucurbit\ModuleManager\Repository\ModuleMenu;
use Cucurbit\ModuleManager\Repository\Modules;

class ModuleManager
{
	/**
	 * @var Modules
	 */
	protected $repository;

	/**
	 * @var ModuleMenu
	 */
	protected $menuRepository;


	/**
	 * @return Modules
	 */
	public function repository(): Modules
	{
		if (!$this->repository instanceof Modules) {
			$this->repository = new Modules();
			$modules          = app('cucurbit')->all();

			$this->repository->initialize($modules);
		}

		return $this->repository;
	}

	/**
	 * get all module's menus
	 *
	 * @return ModuleMenu
	 */
	public function allMenus(): ModuleMenu
	{
		if (!$this->menuRepository instanceof ModuleMenu) {
			$all_menus = collect();
			$this->repository()->each(function (Module $module) use ($all_menus) {
				// get menus
				$menus = $module->get('menus', []);
				if ($menus) {
					$all_menus->put($module->module(), $menus);
				}
			});

			$this->menuRepository = new ModuleMenu();
			$this->menuRepository->initialize($all_menus);
		}

		return $this->menuRepository;
	}

	/**
	 * @param $module
	 *
	 * @return mixed
	 */
	public function get($module)
	{
		return $this->repository()->get(ucfirst($module));
	}

	/**
	 * @param $module
	 *
	 * @return bool
	 */
	public function has($module): bool
	{
		return $this->repository()->has(ucfirst($module));
	}

	/**
	 * 获取指定类型的菜单
	 * @param string $type
	 * @return mixed
	 */
	public function menus($type = ModuleMenu::MENU_TYPE_BACKEND)
	{
		$cache_name = __CLASS__ . '_modules.menus.' . $type;

		return \Cache::rememberForever($cache_name, function () use ($type) {
			$type_menus = [];
			$this->allMenus()->each(function ($menus) use ($type, &$type_menus) {
				if (array_key_exists($type, $menus)) {
					$type_menus[] = $menus[$type];
				}
			});

			return $type_menus;
		});
	}


}