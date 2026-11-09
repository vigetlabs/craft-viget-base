<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;
use Craft;
use craft\db\Query;
use craft\db\Table;
use viget\base\jobs\OptimizeSearchIndex;

class OptimizeSearchIndexJobTest extends Unit
{
    public function testQueuedJobReservesItsOwnTtrAndNeverRetries(): void
    {
        $queue = Craft::$app->getQueue();
        $job = new OptimizeSearchIndex();
        $id = $queue->push($job);

        try {
            $ttr = (new Query())->select('ttr')->from(Table::QUEUE)->where(['id' => $id])->scalar();
            $this->assertSame(OptimizeSearchIndex::TTR, (int)$ttr);
        } finally {
            $queue->release($id);
        }

        $this->assertFalse($job->canRetry(1, null));
    }

    public function testDescriptionNamesTheKindOfRun(): void
    {
        $this->assertSame('Optimizing the search index table', (new OptimizeSearchIndex())->getDescription());
        $this->assertSame('Rebuilding the search index table', (new OptimizeSearchIndex(['full' => true]))->getDescription());
    }
}
