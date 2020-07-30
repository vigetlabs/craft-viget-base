<?php

namespace viget\base\services;

use Craft;
use craft\helpers\StringHelper;
use craft\helpers\FileHelper;
use craft\helpers\UrlHelper;
use craft\helpers\Template as TemplateHelper;
use craft\elements\Asset;
use Twig\Markup;

/**
 * Functionality for easily dropping in "Storybook" style parts kit
 */
class PartsKit
{
    /**
     * Get navigation of files/folders in parts kit
     *
     * @return array|null
     */
    public static function getNav(): ?array
    {
        $templates = self::_getTemplates();

        if (empty($templates)) return null;

        $items = [];

        foreach ($templates as $dir => $files) {
            natcasesort($files);

            $humanizedDir = self::_formatName($dir);

            $items[$humanizedDir] = [
                'isActive' => false,
                'items' => [],
            ];

            foreach ($files as $file) {
                $url = UrlHelper::siteUrl(implode('/', [
                    'parts-kit',
                    $dir,
                    $file,
                ]));

                $isActive = $url === Craft::$app->request->absoluteUrl;

                $items[$humanizedDir]['items'][] =  [
                    'title' => self::_formatName($file),
                    'url' => $url,
                    'isActive' => $isActive,
                ];

                if ($isActive) {
                    $items[$humanizedDir]['isActive'] = true;
                }
            }
        }

        return $items;
    }

    /**
     * Get templates in parts kit folder
     *
     * @return array
     */
    private static function _getTemplates(): array
    {
        $templates = [];

        $templatesPath = Craft::$app->getPath()->getSiteTemplatesPath();
        $partsPath = $templatesPath . '/parts-kit/';

        if (!is_dir($partsPath)) return [];

        $files = FileHelper::findFiles($partsPath);

        foreach ($files as $file) {
            $file = str_replace($partsPath, '', $file);
            $count = substr_count($file, '/');

            // This doesn't fit the dir/file structure, so ignore
            if ($count !== 1) continue;

            $fileParts = explode('/', $file);
            $dir = $fileParts[0];
            $fileName = $fileParts[1];

            // Key already exists, add to array
            if (array_key_exists($dir, $templates)) {
                $templates[$dir][] = self::_cleanFilename($fileName);
            } else {
                $templates[$dir] = [
                    self::_cleanFilename($fileName),
                ];
            }
        }

        ksort($templates, SORT_NATURAL);

        return $templates;
    }

    /**
     * Remove extension from file name
     *
     * @param string $file
     * @return string
     */
    private static function _cleanFileName(string $file): string
    {
        $extensions = array_map(function($extension) {
            return '.' . $extension;
        }, Craft::$app->config->general->defaultTemplateExtensions);

        return str_replace($extensions, '', $file);
    }

    /**
     * Humanize the string and replace - with space
     *
     * @param string $str
     * @return string
     */
    private static function _formatName(string $str): string
    {
        return str_replace('-', ' ', StringHelper::humanize($str));
    }

    /**
     * Get parts kit asset by file name
     *
     * @param string $name
     * @return Asset|null
     */
    public static function getImage(string $name): ?Asset
    {
        $volume = Craft::$app->getVolumes()->getVolumeByHandle('partsKit');

        if (!$volume) return null;

        return Asset::findOne([
            'filename' => $name,
            'volumeId' => $volume->id,
        ]);
    }

    /**
     * Get random Lorem ipsum text
     *
     * @param integer $words
     * @return string
     */
    public static function getText(int $words = 10): string
    {
        $dictionary = [
            'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit', 'sed', 'do', 'eiusmod' ,'tempor', 'incididunt', 'labore', 'et', 'magna', 'aliqua', 'enim', 'ad', 'minim' ,'veniam', 'quis', 'nostrud', 'exercitation', 'ullamco', 'laboris', 'nisi', 'ut', 'aliquip', 'ex', 'ea' ,'commodo', 'consequat', 'duis', 'aute', 'irure', 'reprehenderit', 'voluptate' ,'velit', 'esse', 'cillum', 'dolore', 'eu', 'fugiat', 'nulla', 'pariatur', 'excepteur', 'sint' ,'occaecat', 'cupidatat', 'non', 'proident', 'sunt', 'in', 'culpa', 'qui', 'officia', 'deserunt' ,'mollit', 'anim', 'id', 'est', 'laborum',
        ];

        $selection = [];

        while(count($selection) < $words) {
            $selection[] = $dictionary[array_rand($dictionary)];
        }

        return StringHelper::humanize(implode(' ', $selection));
    }

    /**
     * Get random Lorem ipsum title
     *
     * @param integer $words
     * @return string
     */
    public static function getTitle(int $words = 5): string
    {
        return self::getText($words);
    }

    /**
     * Get random Lorem ipsum sentence
     *
     * @param integer $words
     * @return string
     */
    public static function getSentence(int $words = 10): string
    {
        return self::getText($words) . '.';
    }

    /**
     * Get random Lorem ipsum paragraph
     *
     * @return string
     */
    public static function getParagraph(): string
    {
        return implode(' ', [
            self::getSentence(8),
            self::getSentence(11),
            self::getSentence(10),
            self::getSentence(12),
            self::getSentence(7),
        ]);
    }

    /**
     * Get random Lorem ipsum short rich text sample
     *
     * @return Markup
     */
    public static function getRichTextShort(): Markup
    {
        return TemplateHelper::raw(
            '<p>' .
                self::getSentence(8) .
                ' <strong>' . self::getSentence(5). '</strong> ' .
                self::getSentence() .
                ' <b>' . self::getSentence(3). '</b> ' .
            '</p>' .
            '<p>' .
                self::getSentence(14) .
            '</p>' .
            '<p>' .
                '<a href="#">' . self::getSentence(4) . '</a> ' .
                self::getSentence(8) .
                ' <em>' . self::getSentence(5). '</em> ' .
                self::getSentence() .
                ' <i>' . self::getSentence(3). '</i> ' .
            '</p>'
        );
    }

    /**
     * Get random Lorem ipsum kitchen sink rich text example
     *
     * @return Markup
     */
    public static function getRichTextFull(): Markup
    {
        return TemplateHelper::raw(
            '<h2>Level 2 Heading</h2>' .
            self::getRichTextShort() .
            '<hr>
            <h3>Level 3 Heading</h3>
            <p>' . self::getSentence() . '</p>
            <blockquote>
                <p>Blockquote: ' . self::getSentence() . '</p>
                <footer>—Quote Attribution</footer>
            </blockquote>
            <ul>
                <li>
                    <strong>Bold text</strong>
                    Unordered list item
                </li>
                <li>
                    Unordered list item
                    <ul>
                        <li>Nested unordered list item</li>
                        <li>Nested unordered list item</li>
                    </ul>
                </li>
                <li>Unordered list item</li>
            </ul>
            <hr>
            <h4>Level 4 Heading</h4>
            <ol>
                <li>
                    Ordered list item
                </li>
                <li>
                    Ordered list item
                    <ol>
                        <li>Nested ordered list item</li>
                        <li>Nested ordered list item</li>
                    </ol>
                </li>
                <li>Ordered list item</li>
            </ol>
            <p>' . self::getSentence() . '</p>
            <table>
                <thead>
                    <tr>
                        <th scope="col">Table Header Cell</th>
                        <th scope="col">Table Header Cell</th>
                        <th scope="col">Table Header Cell</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Table Header Cell</th>
                        <td>Table Data Cell</td>
                        <td>Table Data Cell</td>
                    </tr>
                    <tr>
                        <td>Table Data Cell</td>
                        <td>Table Data Cell</td>
                        <td>Table Data Cell</td>
                    </tr>
                    <tr>
                        <td>Table Data Cell</td>
                        <td>Table Data Cell</td>
                        <td>Table Data Cell</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <th scope="row">Table Header Cell</th>
                        <td>Table Data Cell</td>
                        <td>Table Data Cell</td>
                    </tr>
                </tfoot>
            </table>'
        );
    }
}
