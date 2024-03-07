<?php

declare(strict_types=1);

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3\FluidSchemaGenerator\Tests\Unit;

use PHPUnit\Framework\TestCase;
use TYPO3\FluidSchemaGenerator\SchemaGenerator;

class SchemaGeneratorTest extends TestCase
{
    /**
     * @return array<int, array<int, string>>
     */
    public function getTagNameForClassTestValues(): array
    {
        return [
            ['FluidTYPO3\\Vhs\\ViewHelpers\\Content\\RenderViewHelper', 'content.render'],
            ['FluidTYPO3\\Vhs\\ViewHelpers\\Content\\GetViewHelper', 'content.get'],
            ['TYPO3\\Fluid\\ViewHelpers\\IfViewHelper', 'if'],
            ['TYPO3\\Fluid\\ViewHelpers\\Format\\HtmlentitiesViewHelper', 'format.htmlentities'],
        ];
    }

    /**
     * @test
     * @dataProvider getTagNameForClassTestValues
     */
    public function getTagNameForClassReturnsExpectedTag(string $class, string $expected): void
    {
        $instance = new SchemaGenerator();
        $result = $this->callInaccessibleMethod($instance, 'getTagNameForClass', $class);
        self::assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function generateXsdThrowsRuntimeExceptionIfNoViewHeplerIsFound(): void
    {
        $service = $this->getMockBuilder(SchemaGenerator::class)->setMethods(['dummy'])->getMock();
        $this->expectException(\RuntimeException::class);
        $service->generateXsd(['foo' => 'TYPO3Fluid\\SchemaGenerator']);
    }

    /**
     * @return array<int, array<int, string>>
     */
    public function getConvertPhpTypeToXsdTypeTestValues(): array
    {
        return [
            ['', 'xsd:anySimpleType'],
            ['integer', 'xsd:integer'],
            ['float', 'xsd:float'],
            ['double', 'xsd:double'],
            ['boolean', 'xsd:boolean'],
            ['bool', 'xsd:boolean'],
            ['string', 'xsd:string'],
            ['array', 'xsd:anySimpleType'],
            ['mixed', 'xsd:anySimpleType'],
        ];
    }

    /**
     * @test
     * @dataProvider getConvertPhpTypeToXsdTypeTestValues
     */
    public function convertPhpTypeToXsdTypeReturnsExpectedType(string $input, string $expected): void
    {
        $instance = new SchemaGenerator();
        $result = $this->callInaccessibleMethod($instance, 'convertPhpTypeToXsdType', $input);
        self::assertEquals($expected, $result);
    }

    /**
     * @return mixed
     */
    protected function callInaccessibleMethod()
    {
        $arguments = func_get_args();
        $instance = array_shift($arguments);
        $method = array_shift($arguments);
        $method = new \ReflectionMethod($instance, $method);
        $method->setAccessible(true);
        return $method->invokeArgs($instance, $arguments);
    }
}
