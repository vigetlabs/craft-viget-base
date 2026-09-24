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
| MySQL, MyISAM | `OPTIMIZE TABLE`, which defragments the data file. |
| MySQL, InnoDB | `OPTIMIZE TABLE` only when the server has `innodb_optimize_fulltext_only` enabled. It then purges deleted documents from the FULLTEXT index, `innodb_ft_num_word_optimize` words per run. Without that setting nothing runs: the table's FULLTEXT index rules out an in-place rebuild, so `OPTIMIZE TABLE` would copy the whole table, block search index writes while it does, and replay the copy on any read replica, and InnoDB's clustered index does not fragment the way MyISAM did. |
| PostgreSQL | `VACUUM ANALYZE`. |

The return value is a `viget\base\models\OptimizeResult` with `ran` and a `message` saying what happened:

```php
use viget\base\helpers\SearchIndex;

$result = SearchIndex::optimize();
$result = SearchIndex::optimize(full: true); // rebuild the table whatever the engine

if (!$result->ran) {
    Craft::info($result->message, __METHOD__);
}
```

Any other database driver throws a `yii\base\NotSupportedException`.

### Console command

```
php craft viget-searchindex/optimize            # run it now and print the outcome
php craft viget-searchindex/optimize --full     # rebuild the table even on InnoDB
php craft viget-searchindex/optimize --queue    # push a viget\base\jobs\OptimizeSearchIndex job instead
```

`--queue` hands the work to a queue worker. The Queue Manager utility shows the job while it runs, and a failure stays there with its error. The job reserves six hours so a long rebuild cannot be handed to a second worker mid-statement, and it never retries; the next scheduled run picks up where it left off. The job logs its outcome at the info level, so run the command without `--queue` when you want to see whether the table was optimized or skipped.

A cron entry for a site with a worker:

```
0 3 * * * cd /var/www/site/current && php craft viget-searchindex/optimize --queue
```

On InnoDB with `innodb_optimize_fulltext_only` enabled, each run processes a bounded number of words, so schedule a few runs a night on a busy site.
