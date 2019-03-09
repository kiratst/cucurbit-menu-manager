<?php namespace Cucurbit\ModuleManager\Repository;

use Illuminate\Support\Collection;

class ModuleMenu extends Collection
{
	const MENU_TYPE_BACKEND = 'backend';
	const MENU_TYPE_DEVELOP = 'develop';
	const MENU_TYPE_WEB     = 'web';

	/**
	 * @param Collection $menus
	 */
	public function initialize(Collection $menus)
	{
		$cache_name = __CLASS__ . '_modules.menus';

		$this->items = \Cache::rememberForever($cache_name, function () use ($menus) {
			if ($menus->isNotEmpty()) {
				return $menus->map(function ($menu) {
					return collect($menu)->map(function ($value, $type) {
						$value['type'] = $type;
						if (isset($value['groups'])) {
							$value['groups'] = $this->parseRoute(collect($value['groups']))->toArray();
							$value['routes'] = collect($value['groups'])->pluck('routes')->flatten()->toArray();
						}

						return $value;
					})->toArray();
				})->toArray();
			}
			return [];
		});
	}

	/**
	 * @param Collection $groups
	 *
	 * @return Collection
	 */
	private function parseRoute(Collection $groups): Collection
	{
		return $groups->map(function ($group) {
			$routes = [];

			$group['children'] = collect($group['children'])->map(function ($route) use (&$routes) {
				if (isset($route['route'])) {
					$route['link'] = route($route['route']);
				}
				$routes[] = $route['route'] ?? '';
				return $route;
			})->all();

			$group['routes'] = $routes;
			return $group;
		});
	}
}