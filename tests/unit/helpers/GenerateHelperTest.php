<?php

namespace vigetbasetests\unit\helpers;

use Codeception\Test\Unit;
use viget\base\helpers\GenerateHelper;


class GenerateHelperTest extends Unit {

    public function testCompileTemplate() {
        $template = "{{ %%var1%% }} and {% set %%var2%% = \"foo\" %}";
        $compiled = GenerateHelper::compileTemplate(
            $template,
            [
                'var1' => 'foo',
                'var2' => 'bar',
            ]
        );

        $this->assertEquals(
            "{{ foo }} and {% set bar = \"foo\" %}",
            $compiled
        );
    }

    public function testParseInputOfFileNames() {
        $this->assertEquals(
            GenerateHelper::parseInput('file'),
            [
                'path' => '',
                'filename' => 'file',
            ]
        );

        //
        $this->assertEquals(
            GenerateHelper::parseInput('file.html'),
            [
                'path' => '',
                'filename' => 'file',
            ]
        );
    }

    public function testParseInputOfPath() {
        $this->assertEquals(
            GenerateHelper::parseInput('/my/directory/file/'),
            [
                'path' => 'my/directory',
                'filename' => 'file',
            ]
        );

        $this->assertEquals(
            GenerateHelper::parseInput('/my/directory/file.html'),
            [
                'path' => 'my/directory',
                'filename' => 'file',
            ]
        );

        $this->assertEquals(
            GenerateHelper::parseInput('my\directory//file.html'),
            [
                'path' => 'my/directory',
                'filename' => 'file',
            ]
        );
    }

    public function testRemoveFileExtension() {
        $this->assertEquals(
            GenerateHelper::removeFileExtension('file.html'),
            'file'
        );

        $this->assertEquals(
            GenerateHelper::removeFileExtension('file'),
            'file'
        );

        $this->assertEquals(
            GenerateHelper::removeFileExtension('this.is.file.html'),
            'this.is.file'
        );
    }
}
