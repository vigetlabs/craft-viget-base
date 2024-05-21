<?php

namespace vigetbasetests\functional;

use FunctionalTester;

class TrackingCest
{
    public function testTrack(FunctionalTester $I)
    {
        $I->markTestIncomplete('Twig extensions don\'t load for some reason');
        $I->amOnPage('/tracking-test');
        $I->seeInSource('Basic String|tracking|attributes');
        $I->seeInSource('&quot;Basic String&quot;|with|quote');
        $I->seeInSource('&quot;Basic Array&quot;|with|quote');
    }

    public function testAttrTrack(FunctionalTester $I)
    {
        $I->markTestIncomplete('Twig extensions don\'t load for some reason');
        $I->amOnPage('/tracking-test');
        $I->seeInSource('Attr String|tracking|attributes');
        $I->seeInSource('&amp;quot;Attr String&amp;quot;|with|quote');
        $I->seeInSource('&amp;quot;Attr Array&amp;quot;|with|quote');
    }
}
