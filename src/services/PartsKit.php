<?php

namespace viget\base\services;

use Craft;
use craft\helpers\StringHelper;
use craft\helpers\FileHelper;
use craft\helpers\Template as TemplateHelper;
use craft\elements\Asset;
use Twig\Markup;
use viget\base\models\NavNode;
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
     * Determine if this is a request to the root of the parts kit
     * @return bool
     */
    public static function isRootRequest(): bool
    {
        $isRoot = count(Craft::$app->request->getSegments()) === 1;
        return $isRoot && self::isRequest();
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
     * @return array
     */
    public static function getNav(): array
    {
        $templatesPath = Craft::$app->getPath()->getSiteTemplatesPath();
        $partsKitFolderName = self::getConfig('directory');
        $partsPath = $templatesPath . '/' . $partsKitFolderName . '/';

        // Combine and sort all files & directories in the parts kit
        $directories = FileHelper::findDirectories($partsPath);
        $files = FileHelper::findFiles($partsPath);

        $templates = [
            ...$directories,
            ...$files
        ];

        $skipPaths = [
            $partsPath . 'index.twig',
            $partsPath . 'index.html',
        ];

        sort($templates);

        /** @var NavNode[] $result */
        $result = [];
        // Loop through all the paths in the folder.
        // Creating a NavNode object and putting the path to each node in the map.
        foreach ($templates as $templatePath) {

            // Skip ignored paths
            if (in_array($templatePath, $skipPaths)) {
                continue;
            }

            $path = str_replace($partsPath, '', $templatePath);
            $pathParts = explode('/', $path);
            $title = self::_formatTitle(end($pathParts));
            $url = is_file($templatePath)
                ? '/' . $partsKitFolderName . '/' . self::_removeExtension($path)
                : null;

            $result[$path] = new NavNode(
                title: $title,
                path: $path,
                url: $url,
            );
        }

        // Reverse the array, so we can build up child nav nodes into their parents.
        // We remove child nodes as they're added.
        $result = array_reverse($result);

        foreach ($result as $node) {
            $pathParts = explode('/', $node->path);
            $parentPath = implode('/', array_slice($pathParts, 0, -1));
            $parentNode = $result[$parentPath] ?? null;

            if (!$parentNode) {
                continue;
            }

            $parentNode->children[] = $node;
            unset($result[$node->path]);
        }

        // Sort by keys so they're in alpha order
        ksort($result);

        return array_values($result);
    }

    /**
     * The JS file for the parts kit UI library. This needs to be added on both the
     * parts kit root page and on each "part" page.
     *
     * unpkg.com respects server. This URL will need to be adjusted when major versions
     * are released.
     *
     * @return string
     */
    public static function getUiScriptUrl(): string
    {
        return 'https://unpkg.com/@viget/parts-kit@^0/lib/parts-kit.js';
    }

    /**
     * Remove extension from file name
     *
     * @param string $file
     * @return string
     */
    private static function _removeExtension(string $file): string
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
    private static function _formatTitle(string $str): string
    {
        $str = self::_removeExtension($str);
        $str = StringHelper::toKebabCase($str);
        $str = StringHelper::humanize($str);
        return str_replace('-', ' ', $str);
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
