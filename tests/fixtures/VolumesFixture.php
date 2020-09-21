<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\helpers\FileHelper;
use craft\helpers\Json;
use craft\records\Volume;
use craft\services\Volumes;
use craft\test\Fixture;

class VolumesFixture extends Fixture
{
    const BASE_URL = 'http://sample.test/';

    public $modelClass = Volume::class;

    public $dataFile = __DIR__ . '/data/volumes.php';

    public function load()
    {
        parent::load();

        // Create the dirs
        foreach ($this->getData() as $data) {
            $settings = Json::decodeIfJson($data['settings']);
            FileHelper::createDirectory($settings['path']);
        }

        Craft::$app->set('volumes', new Volumes());
    }

    public function unload()
    {
        // Remove the dirs
        foreach ($this->getData() as $data) {
            $settings = Json::decodeIfJson($data['settings']);
            FileHelper::removeDirectory($settings['path']);
        }

        parent::unload();
    }
}
