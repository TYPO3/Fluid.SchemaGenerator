<?php
namespace TYPO3\FluidSchemaGenerator\Tests\Unit;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use PHPUnit\Framework\TestCase;
use TYPO3\FluidSchemaGenerator\DocCommentParser;

/**
 * Test case
 */
class DocCommentParserTest extends TestCase
{
    /**
     * @test
     */
    public function returnsDescriptionWithoutTags()
    {
        $subject = new DocCommentParser();
        $expected = implode(PHP_EOL, [
            'Some Description',
            '/',
        ]);

        $result = $subject->parseDocComment(implode(PHP_EOL, [
            '/**',
            ' * Some Description',
            ' * @param string $someParam With some description',
            ' */',
        ]));
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function returnsDescriptionWithoutTagsContainingFurtherAtSign()
    {
        $subject = new DocCommentParser();
        $expected = implode(PHP_EOL, [
            'Some Description',
            '/',
        ]);

        $result = $subject->parseDocComment(implode(PHP_EOL, [
            '/**',
            ' * Some Description',
            ' * @param string $someParam With some description, containing @ sign',
            ' */',
        ]));
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function lineWithinDescriptionWithAtSignIsKept()
    {
        $subject = new DocCommentParser();
        $expected = implode(PHP_EOL, [
            'Some Description containing @ sign',
            '/',
        ]);

        $result = $subject->parseDocComment(implode(PHP_EOL, [
            '/**',
            ' *',
            ' * Some Description containing @ sign',
            ' */',
        ]));
        $this->assertEquals($expected, $result);
    }
}
