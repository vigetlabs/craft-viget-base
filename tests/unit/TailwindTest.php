<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;

use UnitTester;

use viget\base\Module;

class TailwindTest extends Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testGetColors()
    {
        $colors = Module::$instance->tailwind->getColors();

        $this->assertEquals(
            [
                'transparent' => 'transparent',
                'black' => '#000',
                'white' => '#fff',
                'gray-100' => '#f7fafc',
                'gray-200' => '#edf2f7',
                'gray-300' => '#e2e8f0',
                'gray-400' => '#cbd5e0',
                'gray-500' => '#a0aec0',
                'gray-600' => '#718096',
                'gray-700' => '#4a5568',
                'gray-800' => '#2d3748',
                'gray-900' => '#1a202c',
            ],
            $colors
        );
    }
}
