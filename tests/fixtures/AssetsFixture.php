<?php

namespace vigetbasetests\fixtures;

use craft\test\fixtures\elements\AssetFixture;
use vigetbasetests\fixtures\VolumesFixture;
use vigetbasetests\fixtures\VolumesFolderFixture;

class AssetsFixture extends AssetFixture
{
    public $dataFile = __DIR__ . '/data/assets.php';

    public $depends = [VolumesFixture::class, VolumesFolderFixture::class];
}
