<?php

declare(strict_types=1);

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3\FluidSchemaGenerator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TYPO3\FluidSchemaGenerator\DocCommentParser;

class DocCommentParserTest extends TestCase
{
    /**
     * @test
     */
    public function returnsDescriptionWithoutTags(): void
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
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function returnsDescriptionWithoutTagsExceptDeprecated(): void
    {
        $subject = new DocCommentParser();
        $expected = implode(PHP_EOL, [
            'Some Description',
            '',
            '..  attention::',
            '     **Deprecated** since v11, will be removed in v12.',
            '/',
        ]);

        $result = $subject->parseDocComment(implode(PHP_EOL, [
            '/**',
            ' * Some Description',
            ' * @param string $someParam With some description',
            ' * @deprecated since v11, will be removed in v12.',
            ' */',
        ]));
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function returnsDescriptionWithoutTagsContainingFurtherAtSign(): void
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
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function lineWithinDescriptionWithAtSignIsKept(): void
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
        self::assertEquals($expected, $result);
    }
}
