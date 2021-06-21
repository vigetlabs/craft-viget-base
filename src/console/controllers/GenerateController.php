<?php

namespace viget\base\console\controllers;

use Craft;
use craft\console\Controller;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use craft\models\EntryType;
use craft\models\Section;
use craft\models\Section_SiteSettings;
use viget\base\helpers\GenerateHelper;
use viget\base\Module;
use yii\console\ExitCode;
use yii\helpers\Console;

class GenerateController extends Controller
{

    // TODO - Add craft help CLI documentation
    // TODO - Make a "create section" action that takes a section handle and entry types list and
    //        adds to your Craft config.
    // TODO - Use options to pass partial variants instead of using parameter position
    // TODO - make multiple partials at once

    private $templatesDir;
    private $scaffoldTemplatesDir;
    private $partsKitDir;
    private $defaultFileExtension;

    public function init()
    {
        $templatePrefix = self::getConfig('templatePrefix', 'scaffold');
        $this->templatesDir = Craft::$app->path->getSiteTemplatesPath() . $templatePrefix;
        $this->scaffoldTemplatesDir = FileHelper::normalizePath(__dir__ . '/../../templates/_scaffold');
        $this->partsKitDir = self::getConfig('directory');
        $this->defaultFileExtension = '.html';
        parent::init();
    }

    /**
     * Get a config item either the default or from the config file
     *
     * @param string $key
     * @return string|array|null
     */
    public static function getConfig(string $key, string $section = 'partsKit')
    {
        return Module::$config[$section][$key] ?? null;
    }

    public function actionSection(string $handle)
    {
        $section = Craft::$app->sections->getSectionByHandle($handle);

        if ($section) {
            return ExitCode::UNSPECIFIED_ERROR;
        }

        // TODO make entry types
            $primarySite = Craft::$app->getSites()->getPrimarySite();

        $section = new Section([
            'name' => 'News',
            'handle' => 'news',
            'type' => Section::TYPE_CHANNEL,
            'siteSettings' => [
                new Section_SiteSettings([
                    'siteId' => Craft::$app->sites->getPrimarySite()->id,
                    'enabledByDefault' => true,
                    'hasUrls' => true,
                    'uriFormat' => 'foo/{slug}',
                    'template' => 'foo/_entry',
                ]),
            ]
        ]);

        try {
            $success = Craft::$app->sections->saveSection($section);
        } catch(\Throwable $e) {
            $this->stdout("Error: $e");
        }

        if (!$success) {
            $this->stdout("Error:");
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $getSection = Craft::$app->sections->getSectionByHandle($handle);

        if(!$getSection) {
            $this->stdout('Couldn\'t find section');
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    public function actionEntrytypes(string $handle)
    {
        $section = Craft::$app->sections->getSectionByHandle($handle);
        if (!$section) {
            $this->stdout("No section found for $handle" . PHP_EOL, Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $siteId = Craft::$app->sites->getPrimarySite()->id;
        $settings = $section->getSiteSettings()[$siteId] ?? null;

        if (!$settings) {
            $this->stdout("No settings found for section $handle" . PHP_EOL, Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $templateParts = GenerateHelper::parseInput($settings->template);
        $path = $templateParts['path'];

        $templateContent = GenerateHelper::compileTemplate(
            $this->_loadScaffoldTemplate('template.html'),
            [
                'layout' => self::getConfig('layout'),
            ]
        );

        $structureIndex = GenerateHelper::compileTemplate(
            $this->_loadScaffoldTemplate('structure.html'),
            [
                'path' => $templateParts['path'],
            ]
        );

        // Create the index file for our structure
        $this->_writeFile($path . '/index', $structureIndex);

        foreach ($section->entryTypes as $entryType) {
            $this->_writeFile($path . '/' . $entryType->handle, $templateContent);
        }

        return ExitCode::OK;
    }

    public function actionTemplate($templatePath)
    {
        $parts = GenerateHelper::parseInput($templatePath);
        $path = $parts['path'];
        $filename = $parts['filename'];

        $templateContent = GenerateHelper::compileTemplate(
            $this->_loadScaffoldTemplate('template.html'),
            [
                'layout' => self::getConfig('layout'),
            ]
        );

        // Write template file
        // TODO make _elements a config option
        $this->_writeFile('_elements/' . $path . '/' . $filename, $templateContent);

        return ExitCode::OK;
    }

    public function actionPartial($partialPath, ?string $variantsInput = null)
    {
        $parts = GenerateHelper::parseInput($partialPath);
        $path = $parts["path"];
        $filename = $parts['filename'];

        $variants = [
            'default',
        ];

        if ($variantsInput) {
            $variants = array_merge($variants, explode(',', $variantsInput));
        }

        $partialContent = $this->_loadScaffoldTemplate('partial.html');
        $partsKitContent = $this->_loadScaffoldTemplate('partial-parts-kit.html');

        $partsKitContentCompiled = GenerateHelper::compileTemplate($partsKitContent, [
            'partialPath' => $partialPath,
        ]);

        // Create file in _partials dir
        // TODO make _partials a config option
        $this->_writeFile('_partials/' . $path . '/' . $filename, $partialContent);

        foreach ($variants as $variant) {
            $this->_writeFile($this->partsKitDir . '/' . $filename . '/' . $variant, $partsKitContentCompiled);
        }

        return ExitCode::OK;
    }

    private function _loadScaffoldTemplate(string $path)
    {
        return file_get_contents($this->scaffoldTemplatesDir . '/' . $path);
    }

    private function _writeFile(string $path, string $content)
    {
        $fullPath = $this->templatesDir . '/' . $path . $this->defaultFileExtension;

        if (file_exists($fullPath)) {
            $this->stdout("File already exists for $fullPath" . PHP_EOL, Console::FG_RED);
            return;
        }

        FileHelper::writeToFile($fullPath, $content);
    }
}
