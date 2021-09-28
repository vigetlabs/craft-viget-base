<?php

namespace vigetbasetests\functional;

use FunctionalTester;

class PartsKitCest
{
    public function testIndexRedirect(FunctionalTester $I)
    {
        $I->amOnPage('/parts-kit');
        $I->seeCurrentUrlEquals('/parts-kit/button/blue');
    }
}
