<?php

namespace vigetbasetests\fixtures;

use craft\records\Volume;
use craft\test\ActiveFixture;

class VolumesFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Volume::class;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/volumes.php';

    /**
     * @inheritdoc
     */
    public $depends = [FsFixture::class];


    // const BASE_URL = 'http://sample.test/';

    // public $modelClass = Volume::class;

    // public $dataFile = __DIR__ . '/data/volumes.php';

    // public function load():void
    // {
    //     parent::load();

    //     // Create the dirs
    //     foreach ($this->getData() as $data) {
    //         $settings = Json::decodeIfJson($data['settings']);
    //         FileHelper::createDirectory($settings['path']);
    //     }

    //     Craft::$app->set('volumes', new Volumes());
    // }

    // public function unload():void
    // {
    //     // Remove the dirs
    //     foreach ($this->getData() as $data) {
    //         $settings = Json::decodeIfJson($data['settings']);
    //         FileHelper::removeDirectory($settings['path']);
    //     }

    //     parent::unload();
    // }
}
