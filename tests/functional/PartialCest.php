<?php

namespace vigetbasetests\functional;

use FunctionalTester;

class PartialCest
{
    public function testTwigFunctionData(FunctionalTester $I)
    {
        $I->amOnPage('/partial-test');
        $I->see('Hello World');
    }

    public function testTwigFunctionNoData(FunctionalTester $I)
    {
        $I->amOnPage('/partial-test');
        $I->dontSee('This text should not be available in the partial');
    }
}
