<?php

namespace vigetbasetests\unit;

use Codeception\Test\Unit;
use Craft;
use craft\db\Query;
use craft\db\Table;
use viget\base\console\controllers\SearchIndexController;
use yii\console\ExitCode;
use yii\queue\PushEvent;
use yii\queue\Queue;

class SearchIndexControllerTest extends Unit
{
    public function testOptimizeExposesFullAndQueueOptions(): void
    {
        $options = $this->controller()->options('optimize');

        $this->assertContains('full', $options);
        $this->assertContains('queue', $options);
    }

    public function testQueueFullPushesARebuildJob(): void
    {
        $controller = $this->controller();
        $controller->queue = true;
        $controller->full = true;

        $this->assertSame(ExitCode::OK, $controller->actionOptimize());

        $id = (new Query())
            ->select('id')
            ->from(Table::QUEUE)
            ->where(['description' => 'Rebuilding the search index table'])
            ->scalar();

        $this->assertNotFalse($id);
        Craft::$app->getQueue()->release((string)$id);
    }

    public function testQueueFailsWhenThePushIsIntercepted(): void
    {
        $queue = Craft::$app->getQueue();
        $handler = fn(PushEvent $event) => $event->handled = true;
        $queue->on(Queue::EVENT_BEFORE_PUSH, $handler);

        $controller = $this->controller();
        $controller->queue = true;

        try {
            $this->assertSame(ExitCode::UNSPECIFIED_ERROR, $controller->actionOptimize());
        } finally {
            $queue->off(Queue::EVENT_BEFORE_PUSH, $handler);
        }
    }

    private function controller(): SearchIndexController
    {
        return new SearchIndexController('viget-searchindex', Craft::$app);
    }
}
