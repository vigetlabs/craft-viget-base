<?php

namespace vigetbasetests\fixtures;

use Craft;
use craft\records\Site;
use craft\services\Sites;
use craft\test\Fixture;

class SitesFixture extends Fixture
{
    /**
     * @inheritdoc
     */
    public $modelClass = Site::class;

    /**
     * @inheritdoc
     */
    public $dataFile = __DIR__ . '/data/sites.php';

    /**
     * @inheritdoc
     */
    public function load()
    {
        parent::load();

        // Because the Sites() class memoizes on initialization we need to set() a new sites class
        // with the updated fixture data
        Craft::$app->set('sites', new Sites());
    }
}
