<?php namespace Modules\Menu\Repositories\Eloquent;

use Modules\Core\Internationalisation\Helper;
use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Menu\Entities\Menu;
use Modules\Menu\Repositories\MenuRepository;

class EloquentMenuRepository extends EloquentBaseRepository implements MenuRepository
{
    public function create($data)
    {
        $menu = new Menu;
        $menu->name = $data['name'];
        $menu->primary = $data['primary'];

        $translatableData = Helper::separateLanguages($data);
        Helper::updateTranslated($menu, $translatableData);

        return $menu;
    }

    public function update($menu, $data)
    {
        $menu->name = $data['name'];
        $menu->primary = $data['primary'];
        $translatableData = Helper::separateLanguages($data);

        Helper::updateTranslated($menu, $translatableData);

        return $menu;
    }
}
