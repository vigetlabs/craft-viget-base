<?php

namespace viget\base\services;

use Craft;
use craft\elements\Entry;
use Illuminate\Support\Collection;

/**
 * Various utility services that can be accessed by Twig
 */
class Util
{
    /**
    * Query for additional, deduped entries to a fill a number
    * of needed entries
    *
    * @param array|Collection $entries
    * @param array $params
    * @param int $limit
    * @param array $additionalIdsToSkip
    *
    * @return array
    */
    public static function fillInEntries(
        array|Collection $entries = [],
        array $params = [],
        int $limit = 0,
        array $additionalIdsToSkip = []
    ): array
    {
        if ($entries instanceof Collection) {
            $entries = $entries->all();
        }

        if (!$limit) return [];

        $entriesNeeded = $limit - count($entries);

        // Simply return the passed entries
        // if that satisfies the specified limit
        if ($entriesNeeded <= 0) {
            return $entries;
        }

        // Query for entries to fill-in,
        // skipping ids specifically passed
        $params['limit'] = $entriesNeeded;
        $params['id'] = array_merge(['not'], $additionalIdsToSkip);

        // Also skip entries already passed in
        if (!empty($entries)) {
            $existingEntriesIds = array_map(function($entry) {
                return $entry->id;
            }, $entries);

            $params['id'] = array_merge($params['id'], $existingEntriesIds);
        }

        // Combine passed-in entries and the new fill-in entries
        $entries = array_merge($entries, Entry::findAll($params));
        return $entries;
    }
}
