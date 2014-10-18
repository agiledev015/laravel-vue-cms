<?php namespace Modules\Setting\Repositories\Eloquent;


use Modules\Core\Repositories\Eloquent\EloquentBaseRepository;
use Modules\Setting\Repositories\SettingRepository;

class EloquentSettingRepository extends EloquentBaseRepository implements SettingRepository
{
    /**
     * Update a resource
     * @param $id
     * @param $data
     * @return mixed
     */
    public function update($id, $data)
    {
    }

    /**
     * @param mixed $data
     * @return mixed
     */
    public function create($data)
    {
    }

    public function all()
    {
        $rawSettings = parent::all();

        $settings = [];
        foreach ($rawSettings as $setting) {
            $settings[$setting->name] = $setting;
        }

        return $settings;
    }

    /**
     * Create or update the settings
     * @param $settings
     * @return mixed|void
     */
    public function createOrUpdate($settings)
    {
        $this->removeTokenKey($settings);

        foreach ($settings as $settingName => $settingValues) {
            // Check if setting exists
            if ($setting = $this->findByName($settingName)) {

            }
            $this->createForName($settingName, $settingValues);
        }

    }

    /**
     * Remove the token from the input array
     * @param $settings
     */
    private function removeTokenKey(&$settings)
    {
        unset($settings['_token']);
    }

    /**
     * Find a setting by its name
     * @param $settingName
     * @return mixed
     */
    public function findByName($settingName)
    {
        return $this->model->whereHas('translations', function($q) use($settingName)
        {
            $q->where('name', $settingName);
        })->first();
    }

    /**
     * Create a setting with the given name
     * @param $settingName
     * @param $settingValues
     */
    private function createForName($settingName, $settingValues)
    {
        $setting = new $this->model;
        $setting->name = $settingName;
        foreach ($settingValues as $lang => $value) {
            $setting->translate($lang)->value = $value;
        }
        $setting->save();
    }
}
