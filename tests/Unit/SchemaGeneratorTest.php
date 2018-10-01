<?php
declare(strict_types=1);
namespace TYPO3\FluidSchemaGenerator\Tests\Unit;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use PHPUnit\Framework\TestCase;
use TYPO3\FluidSchemaGenerator\SchemaGenerator;
use TYPO3\FluidSchemaGenerator\DocCommentParser;

/**
 * Test case
 */
class SchemaGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function docCommentParserIsInjected()
    {
        $instance = new SchemaGenerator();
        $this->assertAttributeInstanceOf(DocCommentParser::class, 'docCommentParser', $instance);
    }

    /**
     * @return array
     */
    public function getTagNameForClassTestValues()
    {
        return [
            ['FluidTYPO3\\Vhs\\ViewHelpers\\Content\\RenderViewHelper', 'content.render'],
            ['FluidTYPO3\\Vhs\\ViewHelpers\\Content\\GetViewHelper', 'content.get'],
            ['TYPO3\\Fluid\\ViewHelpers\\IfViewHelper', 'if'],
            ['TYPO3\\Fluid\\ViewHelpers\\Format\\HtmlentitiesViewHelper', 'format.htmlentities'],
        ];
    }

    /**
     * @param string $class
     * @param string $expected
     * @test
     * @dataProvider getTagNameForClassTestValues
     */
    public function getTagNameForClassReturnsExpectedTag($class, $expected)
    {
        $instance = new SchemaGenerator();
        $result = $this->callInaccessibleMethod($instance, 'getTagNameForClass', $class);
        $this->assertEquals($expected, $result);
    }

    /**
     * @test
     */
    public function generateXsdThrowsRuntimeExceptionIfNoViewHeplerIsFound()
    {
        $service = $this->getMockBuilder(SchemaGenerator::class)->setMethods(['dummy'])->getMock();
        $this->expectException(\RuntimeException::class);
        $service->generateXsd(['TYPO3Fluid\\SchemaGenerator']);
    }

    /**
     * @return array
     */
    public function getConvertPhpTypeToXsdTypeTestValues()
    {
        return [
            ['', 'xsd:anySimpleType'],
            ['integer', 'xsd:integer'],
            ['float', 'xsd:float'],
            ['double', 'xsd:double'],
            ['boolean', 'xsd:boolean'],
            ['string', 'xsd:string'],
            ['array', 'xsd:anySimpleType'],
            ['mixed', 'xsd:anySimpleType'],
        ];
    }

    /**
     * @dataProvider getConvertPhpTypeToXsdTypeTestValues
     * @param string $input
     * @param string $expected
     * @test
     */
    public function convertPhpTypeToXsdTypeReturnsExpectedType($input, $expected)
    {
        $instance = new SchemaGenerator();
        $result = $this->callInaccessibleMethod($instance, 'convertPhpTypeToXsdType', $input);
        $this->assertEquals($expected, $result);
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
