<?php

namespace viget\base\console\controllers;

use Craft;
use craft\console\Controller;
use craft\helpers\FileHelper;
use craft\services\Sections;

use viget\base\Module;
use yii\console\ExitCode;
use yii\helpers\Console;

class GenerateController extends Controller
{

    // TODO - pass variants to make multiple files `default, dark, two-up`
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

    public function actionEntrytypes(string $handle)
    {
        $section = Craft::$app->sections->getSectionByHandle($handle);
        if(!$section) {
            $this->stdout("No section found for $handle" . PHP_EOL, Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $settings = current($section->getSiteSettings()) ?? null;

        if(!$settings) {
            $this->stdout("No settings found for section $handle" . PHP_EOL, Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $templateParts = self::_splitAndSanitizeInput($settings->template);
        $path = $templateParts['path'];

        $templateContent = self::_compileTemplate(
            $this->_loadScaffoldTemplate('template.html'),
            [
                'layout' => self::getConfig('layout'),
            ]
        );

        $structureIndex =  self::_compileTemplate(
            $this->_loadScaffoldTemplate('structure.html'),
            [
                'path' => $templateParts['path'],
            ]
        );

        // Create the index file for our structure
        $this->_writeFile( $path . '/index', $structureIndex);

        foreach($section->entryTypes as $entryType) {
            $this->_writeFile($path . '/' . $entryType->handle, $templateContent);
        }

        return ExitCode::OK;
    }

    public function actionTemplate($templatePath)
    {
        $parts = self::_splitAndSanitizeInput($templatePath);
        $path = $parts['path'];
        $filename = $parts['filename'];

        $templateContent = self::_compileTemplate(
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
        $parts = self::_splitAndSanitizeInput($partialPath);
        $path = $parts["path"];
        $filename = $parts['filename'];

        $variants = [
            'default',
        ];

        if($variantsInput) {
            $variants = array_merge($variants, explode(',', $variantsInput));
        }

        $partialContent = $this->_loadScaffoldTemplate('partial.html');
        $partsKitContent = $this->_loadScaffoldTemplate('partial-parts-kit.html');

        $partsKitContentCompiled = self::_compileTemplate($partsKitContent, [
            'partialPath' => $partialPath,
        ]);

        // Create file in _partials dir
        // TODO make _partials a config option
        $this->_writeFile('_partials/' . $path . '/' . $filename, $partialContent);

        foreach($variants as $variant) {
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

    // TODO - unit test
    private static function _removeFileExtension(string $filename): string
    {
        $explode = explode('.', $filename);
        if(count($explode) === 1) {
            return $filename;
        }

        return implode('.', array_slice($explode, 0, -1));
    }

    // TODO - unit test
    private static function _splitAndSanitizeInput(string $input): array
    {
        $split = explode(DIRECTORY_SEPARATOR, $input);
        $path = implode(DIRECTORY_SEPARATOR, array_slice($split, 0, -1));
        $filename = self::_removeFileExtension(end($split));

        return [
            'path' => FileHelper::normalizePath($path),
            'filename' => FileHelper::sanitizeFilename($filename),
        ];
    }

    // TODO - unit test
    private static function _compileTemplate(string $template, array $vars): string
    {
        $patterns = array_map(function ($item) {
            return "/%%$item%%/";
        }, array_values(array_flip($vars)));

        $replacements = array_values($vars);

        return preg_replace($patterns, $replacements, $template);
    }
}
