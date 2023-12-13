<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;

use UnitTester;
use Craft;

use viget\base\Module;
use vigetbasetests\fixtures\AssetsFixture;

class PartsKitTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function _fixtures(): array
    {
        return [
            'assets' => [
                'class' => AssetsFixture::class,
            ],
        ];
    }

    public function testGetImage()
    {
        $image = Module::getInstance()->partsKit->getImage('sample.png');

        $this->assertInstanceOf('craft\elements\Asset', $image);
    }
}
