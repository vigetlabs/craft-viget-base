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

    public function testNav()
    {
        Craft::$app->request->setUrl('/parts-kit/button/default');

        $nav = Module::$instance->partsKit->getNav();

        $this->assertEquals([
            'Button' => [
                'items' => [
                    [
                        'title' => 'Blue',
                        'url' => 'http://craft-base.test/parts-kit/button/blue',
                        'isActive' => false,
                    ],
                    [
                        'title' => 'Default',
                        'url' => 'http://craft-base.test/parts-kit/button/default',
                        'isActive' => true,
                    ],
                ],
                'isActive' => true,
            ],
            'Cta block' => [
                'items' => [
                    [
                        'title' => 'Dark theme',
                        'url' => 'http://craft-base.test/parts-kit/cta-block/dark-theme',
                        'isActive' => false,
                    ],
                    [
                        'title' => 'Default',
                        'url' => 'http://craft-base.test/parts-kit/cta-block/default',
                        'isActive' => false,
                    ],
                ],
                'isActive' => false,
            ]
        ], $nav);
    }

    public function testThemeCss()
    {
        $themeCss = Module::$instance->partsKit->getTheme();

        $this->assertEquals('--pk-background: #2c3e50;--pk-main-background: #34495e;--pk-text: white;--pk-nav-icon: #2ecc71;--pk-nav-item-text-hover: white;--pk-nav-item-background-hover: rgba(255, 255, 255, 0.1);--pk-nav-subitem-text-hover: white;--pk-nav-subitem-background-hover: rgba(255, 255, 255, 0.1);--pk-nav-subitem-background-active: #2ecc71;--pk-nav-subitem-text-active: #fff;--pk-controls-text: rgba(255, 255, 255, 0.3);--pk-controls-border: rgba(255, 255, 255, 0.1);', $themeCss);
    }

    public function testGetImage()
    {
        $image = Module::$instance->partsKit->getImage('sample.png');

        $this->assertInstanceOf('craft\elements\Asset', $image);
    }
}
