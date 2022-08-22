<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\records\VolumeFolder;
use craft\services\Volumes;
use craft\test\ActiveFixture;

class VolumesFolderFixture extends ActiveFixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = VolumeFolder::class;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/volumefolders.php';

    /**
     * @inheritdoc
     */
    public $depends = [VolumesFixture::class];

    /**
     * @inheritdoc
     */
    public function load(): void
    {
        parent::load();

        Craft::$app->set('volumes', new Volumes());
    }
}
