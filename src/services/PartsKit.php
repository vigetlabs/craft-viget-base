<?php

namespace viget\base\services;

use Craft;
use craft\helpers\StringHelper;
use craft\helpers\FileHelper;
use craft\helpers\UrlHelper;
use craft\helpers\Template as TemplateHelper;
use craft\elements\Asset;
use Twig\Markup;

use viget\base\Module;

/**
 * Functionality for easily dropping in "Storybook" style parts kit
 */
class PartsKit
{
    /**
     * Determine if this is a request to the parts kit
     *
     * @return boolean
     */
    public static function isRequest(): bool
    {
        return (Craft::$app->request->segments[0] ?? null) === self::getConfig('directory');
    }

    /**
     * Get a config item either the default or from the config file
     *
     * @param string $key
     * @return string|array|null
     */
    public static function getConfig(string $key)
    {
        return Module::$config['partsKit'][$key] ?? null;
    }

    /**
     * Get navigation of files/folders in parts kit
     *
     * @return array|null
     */
    public static function getNav(): ?array
    {
        $partsKitDir = self::getConfig('directory');

        $templates = self::_getTemplates($partsKitDir);

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
                    $partsKitDir,
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
     * Get the CSS variables that power the theme
     *
     * @return string
     */
    public static function getTheme(): string
    {
        $themes = [
            'light' => [
                'background' => '#ededed',
                'main-background' => '#fff',
                'text' => '#202020',
                'nav-icon' => '#148bbe',
                'nav-item-text-hover' => '#202020',
                'nav-item-background-hover' => '#dbdbdb',
                'nav-subitem-text-hover' => '#202020',
                'nav-subitem-background-hover' => '#dbdbdb',
                'nav-subitem-background-active' => '#148bbe',
                'nav-subitem-text-active' => '#fff',
                'controls-text' => '#a7a9ac',
                'controls-border' => '#dbdbdb',
            ],

            'dark' => [
                'background' => '#2f2f2f',
                'main-background' => '#333',
                'text' => 'rgba(255, 255, 255, 0.8)',
                'nav-icon' => '#1ea7fd',
                'nav-item-text-hover' => '#fff',
                'nav-item-background-hover' => 'rgba(250, 250, 252, 0.1)',
                'nav-subitem-text-hover' => '#fff',
                'nav-subitem-background-hover' => 'rgba(250, 250, 252, 0.1)',
                'nav-subitem-background-active' => '#1ea7fd',
                'nav-subitem-text-active' => '#fff',
                'controls-text' => '#999',
                'controls-border' => 'rgba(255, 255, 255, 0.1)',
            ],
        ];

        $theme = self::getConfig('theme');

        // If a theme name is passed instead of
        // custom config, select that theme
        if (!is_array($theme)) {
            $theme = $themes[$theme] ?? $themes['light'];
        }

        $css = [];
        foreach ($theme as $property => $value) {
            $css[] = "--pk-{$property}: {$value};";
        }
        return implode('', $css);
    }

    /**
     * Get templates in parts kit folder
     *
     * @param string $partsKitDir
     * @return array
     */
    private static function _getTemplates(string $partsKitDir): array
    {
        $templates = [];

        $templatesPath = Craft::$app->getPath()->getSiteTemplatesPath();
        $partsPath = $templatesPath . '/' . $partsKitDir . '/';

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

            // Don't include templates that are "hidden"
            if ($fileName[0] === '_' || $fileName[0] === '.') continue;

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
        $volume = Craft::$app->getVolumes()->getVolumeByHandle(self::getConfig('volume'));

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
     * @param integer $sentences
     * @return string
     */
    public static function getParagraph(int $sentences = 5): string
    {
        $selection = [];

        while (count($selection) < $sentences) {
            $selection[] = self::getSentence(rand(8, 12));
        }
        return implode(' ', $selection);
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
