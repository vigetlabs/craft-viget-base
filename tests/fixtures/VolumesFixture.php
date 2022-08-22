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
}
