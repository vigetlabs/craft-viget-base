---
layout: default
title: Helpers
nav_order: 6
---

# Helpers

## Search Index

Craft rewrites rows in its `searchindex` table every time an element is saved, and on a large site the table ends up as the biggest thing in the database. `viget\base\helpers\SearchIndex::optimize()` runs the maintenance statement that suits the table's storage engine, and skips it when a rebuild would cost more than it returns:

| Database | What runs |
| --- | --- |
| MySQL, MyISAM or Aria | `OPTIMIZE TABLE`, which defragments the data file. |
| MySQL, InnoDB | `OPTIMIZE NO_WRITE_TO_BINLOG TABLE` only when the server has `innodb_optimize_fulltext_only` enabled. It then purges deleted documents from the FULLTEXT index, `innodb_ft_num_word_optimize` words per run. Without that setting nothing runs: the table's FULLTEXT index rules out an in-place rebuild, so `OPTIMIZE TABLE` would copy the whole table, block search index writes while it does, and replay the copy on any read replica, and InnoDB's clustered index does not fragment the way MyISAM did. |
| PostgreSQL | `VACUUM ANALYZE`. |

Two things to know before enabling `innodb_optimize_fulltext_only`:

- It is a server-wide setting, and the helper only purges while it is on, so in practice it stays on. While it is on, `OPTIMIZE TABLE` on *every* InnoDB table on the server only processes FULLTEXT indexes and never rebuilds the table. That includes other applications' tables on a shared server.
- The purge is kept out of the binary log, because a replica would apply it with its own setting and run the full copy if the setting is off there. Replicas therefore never have their FULLTEXT index purged.

The return value is a `viget\base\models\OptimizeResult` with `ran` and a `message` saying what happened:

```php
use viget\base\helpers\SearchIndex;

$result = SearchIndex::optimize();
$result = SearchIndex::optimize(full: true); // rebuild the table whatever the engine

if (!$result->ran) {
    Craft::info($result->message, __METHOD__);
}
```

`full: true` runs `ALTER TABLE ... FORCE` and `ANALYZE TABLE` on InnoDB, which copies the table, blocks search index writes, and repeats the copy on read replicas. On PostgreSQL it runs `VACUUM (FULL, ANALYZE)`, which locks the table against reads as well as writes.

Any other database driver or storage engine throws a `yii\base\NotSupportedException`. When MySQL reports an error for the maintenance statement, the helper throws a `yii\db\Exception`.

### Console command

```
php craft viget-searchindex/optimize            # run it now and print the outcome
php craft viget-searchindex/optimize --full     # rebuild the table even on InnoDB
php craft viget-searchindex/optimize --queue    # push a viget\base\jobs\OptimizeSearchIndex job instead
```

`--queue` hands the work to a queue worker, and the Queue Manager utility shows the job while it runs.

**It needs a dedicated queue worker** (`php craft queue/listen` under a process manager, or an equivalent). Do not use it with Craft's web-request queue runner (`runQueueAutomatically`): a rebuild running inside a web request is killed by PHP-FPM or proxy timeouts partway through, and the job's long reservation then holds up every other queued job.

How the job behaves:

- It reserves six hours, so the Queue Manager keeps showing a long rebuild as running.
- It never retries. When the optimization throws, the job is marked failed and the error stays in the Queue Manager. When the worker process dies mid-run, the job stays reserved for up to six hours and is then dropped without a failure record. Either way the next scheduled run starts over.
- Each `--queue` run pushes a new job, without checking for one that is already waiting. With a worker that keeps up, that never matters. If the worker is down, the nightly jobs stack up and run back to back once it returns.
- A completed job looks the same whether it ran or skipped. The outcome is logged at the info level when the table was optimized, and at the warning level when it was skipped, so an InnoDB site without `innodb_optimize_fulltext_only` sees in its logs that the cron does nothing.

A cron entry:

```
0 3 * * * cd /var/www/site/current && php craft viget-searchindex/optimize --queue
```

On InnoDB with `innodb_optimize_fulltext_only` enabled, each run processes a bounded number of words, so schedule a few runs a night on a busy site.
