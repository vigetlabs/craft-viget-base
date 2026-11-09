<?php

namespace viget\base\jobs;

use Craft;
use craft\queue\BaseJob;
use viget\base\helpers\SearchIndex;
use yii\queue\RetryableJobInterface;

/**
 * Runs SearchIndex::optimize() on a queue worker, so the Queue Manager shows it running and a
 * failure lands there with its error instead of in a cron log.
 *
 * Dispatch it with `php craft viget-searchindex/optimize --queue`.
 */
class OptimizeSearchIndex extends BaseJob implements RetryableJobInterface
{
    /**
     * Seconds the queue reserves the job for. A table rebuild can run far past the queue's default
     * of 300. Once the reservation expires, the next worker to pick the job up drops it because it
     * never retries, so the Queue Manager would stop showing a rebuild that is still running. The
     * cost is that a job whose worker dies stays reserved until this runs out, and is then dropped
     * without being marked failed.
     */
    public const TTR = 6 * 60 * 60;

    /**
     * @var bool Rebuild the table even when its storage engine would not benefit.
     * @see SearchIndex::optimize()
     */
    public bool $full = false;

    /**
     * @inheritdoc
     */
    public function execute($queue): void
    {
        $result = SearchIndex::optimize($this->full);

        // A skip completes the job like a run does, so log it louder to keep a no-op cron visible.
        if ($result->ran) {
            Craft::info($result->message, __METHOD__);
        } else {
            Craft::warning($result->message, __METHOD__);
        }
    }

    /**
     * @inheritdoc
     */
    public function getTtr(): int
    {
        return self::TTR;
    }

    /**
     * Never retries. Repeating a failed rebuild unattended costs another full copy, and the next
     * scheduled run covers whatever this one missed.
     *
     * @inheritdoc
     */
    public function canRetry($attempt, $error): bool
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    protected function defaultDescription(): ?string
    {
        return $this->full ? 'Rebuilding the search index table' : 'Optimizing the search index table';
    }
}
