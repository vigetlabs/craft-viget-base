<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;
use UnitTester;

use viget\base\Module;

class TrackingTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testGtmStrings()
    {
        $this->assertEquals(
            'one|two|three',
            Module::getInstance()->tracking->getGtmAttribute('one', 'two', 'three')
        );
        $this->assertEquals(
            '&quot;one&quot;|two|three',
            Module::getInstance()->tracking->getGtmAttribute('"one"', 'two', 'three')
        );
    }

    public function testGtmArray()
    {
        $this->assertEquals(
            'one|two|three',
            Module::getInstance()->tracking->getGtmAttribute([
                'one',
                'two',
                'three',
            ])
        );

        $this->assertEquals(
            '&quot;one&quot;|two|three',
            Module::getInstance()->tracking->getGtmAttribute([
                '"one"',
                'two',
                'three',
            ])
        );
    }
}
