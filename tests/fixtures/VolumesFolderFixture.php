<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\records\VolumeFolder;
use craft\services\Volumes;
use craft\test\Fixture;

class VolumesFolderFixture extends Fixture
{
    public $modelClass = VolumeFolder::class;

    public $dataFile = __DIR__ . '/data/volumefolders.php';

    public $depends = [VolumesFixture::class];

    public function load()
    {
        parent::load();

        Craft::$app->set('volumes', new Volumes());
    }
}
