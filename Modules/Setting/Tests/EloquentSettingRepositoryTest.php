<?php

namespace Modules\Setting\Tests;

use Illuminate\Support\Facades\Event;
use Modules\Setting\Events\SettingWasCreated;
use Modules\Setting\Events\SettingWasUpdated;

class EloquentSettingRepositoryTest extends BaseSettingTest
{
    public function setUp()
    {
        parent::setUp();
    }

    /** @test */
    public function it_creates_translated_setting()
    {
        // Prepare
        $data = [
            'core::site-name' => [
                'en' => 'AsgardCMS_en',
                'fr' => 'AsgardCMS_fr',
            ],
        ];

        // Run
        $this->settingRepository->createOrUpdate($data);

        // Assert
        $setting = $this->settingRepository->find(1);
        $this->assertEquals('core::site-name', $setting->name);
        $this->assertEquals('AsgardCMS_en', $setting->translate('en')->value);
        $this->assertEquals('AsgardCMS_fr', $setting->translate('fr')->value);
    }

    /** @test */
    public function it_creates_plain_setting()
    {
        // Prepare
        $data = [
            'core::template' => 'asgard',
        ];

        // Run
        $this->settingRepository->createOrUpdate($data);

        // Assert
        $setting = $this->settingRepository->find(1);
        $this->assertEquals('core::template', $setting->name);
        $this->assertEquals('asgard', $setting->plainValue);
    }

    /** @test */
    public function it_finds_setting_by_name()
    {
        // Prepare
        $data = [
            'core::site-name' => [
                'en' => 'AsgardCMS_en',
                'fr' => 'AsgardCMS_fr',
            ],
        ];

        // Run
        $this->settingRepository->createOrUpdate($data);

        // Assert
        $setting = $this->settingRepository->findByName('core::site-name');
        $this->assertEquals('core::site-name', $setting->name);
        $this->assertEquals('AsgardCMS_en', $setting->translate('en')->value);
        $this->assertEquals('AsgardCMS_fr', $setting->translate('fr')->value);
    }

    /** @test */
    public function it_returns_module_settings()
    {
        // Prepare
        $data = [
            'core::site-name' => [
                'en' => 'AsgardCMS_en',
                'fr' => 'AsgardCMS_fr',
            ],
            'core::template' => 'asgard',
            'blog::posts-per-page' => 10,
        ];

        // Run
        $this->settingRepository->createOrUpdate($data);

        // Assert
        $blogSettings = $this->settingRepository->findByModule('blog');
        $this->assertEquals(1, $blogSettings->count());
        $coreSettings = $this->settingRepository->findByModule('core');
        $this->assertEquals(2, $coreSettings->count());
    }

    /** @test */
    public function it_encodes_array_of_non_translatable_data()
    {
        // Prepare
        $data = [
            'core::locales' => [
                "su",
                "bi",
                "bs",
            ],
        ];

        // Run
        $this->settingRepository->createOrUpdate($data);

        // Assert
        $setting = $this->settingRepository->find(1);
        $this->assertEquals('core::locales', $setting->name);
        $this->assertEquals('["su","bi","bs"]', $setting->plainValue);
    }

    /** @test */
    public function it_triggers_event_when_setting_was_created()
    {
        Event::fake();

        $data = [
            'core::template' => 'asgard',
            'core::site-name' => [
                'en' => 'AsgardCMS_en',
                'fr' => 'AsgardCMS_fr',
            ],
        ];
        $this->settingRepository->createOrUpdate($data);

        Event::assertDispatched(SettingWasCreated::class, function ($e) {
            return $e->setting->name === 'core::template';
        });
    }

    /** @test */
    public function it_triggers_event_when_setting_was_update()
    {
        Event::fake();

        $data = [
            'core::template' => 'asgard',
            'core::site-name' => [
                'en' => 'AsgardCMS_en',
                'fr' => 'AsgardCMS_fr',
            ],
        ];
        $this->settingRepository->createOrUpdate($data);
        $this->settingRepository->createOrUpdate(['core::template' => 'flatly']);

        Event::assertDispatched(SettingWasUpdated::class, function ($e) {
            return $e->setting->name === 'core::template';
        });
    }
}
