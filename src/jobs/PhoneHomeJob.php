<?php

namespace viget\base\jobs;

use Craft;
use craft\queue\BaseJob;

use viget\base\services\PhoneHome;

/**
 * Job to make the request to the Airtable inventory
 */
class PhoneHomeJob extends BaseJob
{
    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        PhoneHome::makeRequest();
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): ?string
    {
        return 'Phoning home';
    }
}
