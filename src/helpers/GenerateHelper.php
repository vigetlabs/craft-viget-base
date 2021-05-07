<?php

namespace viget\base\helpers;

use craft\helpers\FileHelper;

class GenerateHelper
{

    public static function compileTemplate(string $template, array $vars): string
    {
        $patterns = array_map(function ($item) {
            return "/%%$item%%/";
        }, array_values(array_flip($vars)));

        $replacements = array_values($vars);

        return preg_replace($patterns, $replacements, $template);
    }

    public static function parseInput(string $input): array
    {
        $cleanInput = trim($input, DIRECTORY_SEPARATOR);
        $split = explode(DIRECTORY_SEPARATOR, $cleanInput);
        $path = implode(DIRECTORY_SEPARATOR, array_slice($split, 0, -1));
        $filename = self::removeFileExtension(end($split));

        return [
            'path' => FileHelper::normalizePath($path),
            'filename' => FileHelper::sanitizeFilename($filename),
        ];
    }

    public static function removeFileExtension(string $filename): string
    {
        $explode = explode('.', $filename);
        if (count($explode) === 1) {
            return $filename;
        }

        return implode('.', array_slice($explode, 0, -1));
    }
}
