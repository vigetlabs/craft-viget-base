<?php
namespace vigetbasetests\unit\console;

use Craft;
use craft\helpers\FileHelper;
use craft\test\console\ConsoleTest;
use viget\base\Module;
use vigetbasetests\fixtures\EntryTypesFixture;
use vigetbasetests\fixtures\SectionsFixture;
use yii\base\InvalidConfigException;
use yii\console\ExitCode;

class GenerateControllerTest extends ConsoleTest
{
    private $siteTemplatesPath;
    private $templatesRoot;
    private $partialsRoot;
    private $partsKitRoot;

    public function _fixtures(): array
    {
        return [
            'entryTypes' => [
                'class' => EntryTypesFixture::class,
            ],
        ];
    }

    protected function _before()
    {
        $templatePath = Craft::$app->getPath()->getSiteTemplatesPath();
        $templatePrefix = Module::$config['scaffold']['templatePrefix'];
        $partsKitDir = Module::$config['partsKit']['directory'];
        $this->siteTemplatesPath = $templatePath . $templatePrefix;
        $this->partialsRoot = $this->siteTemplatesPath . '/_partials'; // TODO - config?
        $this->templatesRoot = $this->siteTemplatesPath . '/_elements'; // TODO - config?
        $this->partsKitRoot = $this->siteTemplatesPath . '/' . $partsKitDir;
    }

    protected function _after()
    {
        FileHelper::removeDirectory($this->siteTemplatesPath);
    }

    /**
     * @throws InvalidConfigException
     */
    public function testCreateSection()
    {
        $this->consoleCommand('viget-base/generate/section', [
            'myChannel'
        ])
            ->exitCode(ExitCode::OK)
            ->run();

        $siteId = Craft::$app->sites->getPrimarySite()->id;
        $section = Craft::$app->sections->getSectionByHandle('myChannel');
        $this->assertNotNull($section);
//        $sectionSettings = $section->getSiteSettings()[$siteId];
        $this->assertEquals('My Channel', $section->name);
//        $this->assertEquals('_elements/myChannel', $sectionSettings->template);
    }

    /**
     * @throws InvalidConfigException
     */
    public function testCreatePartial()
    {
        $this->consoleCommand('viget-base/generate/partial', [
            'foo',
        ])
            ->exitCode(ExitCode::OK)
            ->run();

        $this->assertFileExists($this->partialsRoot . '/foo.html');
        $this->assertFileExists($this->partsKitRoot . '/foo/default.html');

        $partsKitContent = file_get_contents($this->partsKitRoot . '/foo/default.html');

        $this->assertStringContainsString(
            '_partials/foo',
            $partsKitContent
        );
    }

    /**
     * @throws InvalidConfigException
     */
    public function testCreateTemplate()
    {
        $this->consoleCommand('viget-base/generate/template', [
            'foo',
        ])
            ->exitCode(ExitCode::OK)
            ->run();

        $this->assertFileExists($this->templatesRoot . '/foo.html');

        $fileContent = file_get_contents($this->templatesRoot . '/foo.html');

        $this->assertStringContainsString(
            Module::$config['partsKit']['layout'],
            $fileContent
        );
    }

    /**
     * @throws InvalidConfigException
     */
    public function testCreateEntryTypes()
    {
        $this->consoleCommand('viget-base/generate/entrytypes', [
            'testSection1',
        ])
            ->exitCode(ExitCode::OK)
            ->run();

        $this->assertFileExists($this->templatesRoot . '/test-section-1/index.html');
        $this->assertFileExists($this->templatesRoot . '/test-section-1/testEntryType1.html');
        $this->assertFileExists($this->templatesRoot . '/test-section-1/testEntryType2.html');

        $indexContent = file_get_contents($this->templatesRoot . '/test-section-1/index.html');
        $entryTypeContent = file_get_contents($this->templatesRoot . '/test-section-1/testEntryType1.html');

        $this->assertStringContainsString(
            '_elements/test-section-1/#{entry.type.handle}',
            $indexContent
        );

        $this->assertStringContainsString(
            Module::$config['partsKit']['layout'],
            $entryTypeContent
        );
    }

}
