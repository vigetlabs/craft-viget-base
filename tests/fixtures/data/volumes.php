<?php

use craft\volumes\Local;
use vigetbasetests\fixtures\VolumesFixture;

return [
    'testVolume' => [
        'id' => '1000',
        'name' => 'Test volume',
        'handle' => 'testVolume',
        'type' => Local::class,
        'url' => null,
        'hasUrls' => true,
        'settings' => json_encode([
            'path' => CRAFT_TESTS_PATH . '/_data/assets/test-volume/',
            'url' => VolumesFixture::BASE_URL
        ]),
        'uid' => 'volume-1000----------------------uid',
    ],
];
