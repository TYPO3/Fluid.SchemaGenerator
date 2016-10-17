<?php
namespace TYPO3Fluid\SchemaGenerator\Tests\Unit;

use FluidTYPO3\Schemaker\Service\SchemaService;
use TYPO3Fluid\SchemaGenerator\SchemaGenerator;
use TYPO3Fluid\SchemaGenerator\DocCommentParser;

/**
 * Class SchemaGeneratorTest
 */
class SchemaGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testPerformsInjections()
    {
        $instance = new SchemaGenerator();
        $this->assertAttributeInstanceOf(DocCommentParser::class, 'docCommentParser', $instance);
    }

    /**
     * @param string $class
     * @param string $expected
     * @test
     * @dataProvider getTagNameForClassTestValues
     */
    public function testGetTagNameForClass($class, $expected)
    {
        $instance = new SchemaGenerator();
        $result = $this->callInaccessibleMethod($instance, 'getTagNameForClass', $class);
        $this->assertEquals($expected, $result);
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
     * @test
     */
    public function testGenerateXsdErrorsWhenNoViewHelpersInPackage()
    {
        $service = $this->getMock(SchemaGenerator::class, ['dummy']);
        $this->setExpectedException('RuntimeException');
        $service->generateXsd(['TYPO3Fluid\\SchemaGenerator']);
    }

    /**
     * @dataProvider getConvertPhpTypeToXsdTypeTestValues
     * @param string $input
     * @param string $expected
     */
    public function testConvertPhpTypeToXsdType($input, $expected)
    {
        $instance = new SchemaGenerator();
        $result = $this->callInaccessibleMethod($instance, 'convertPhpTypeToXsdType', $input);
        $this->assertEquals($expected, $result);
    }

    public function getConvertPhpTypeToXsdTypeTestValues()
    {
        return [
            ['', 'xsd:anySimpleType'],
            ['integer', 'xsd:integer'],
            ['float', 'xsd:float'],
            ['double', 'xsd:double'],
            ['boolean', 'xsd:boolean'],
            ['string', 'xsd:string'],
            ['array', 'xsd:array'],
            ['mixed', 'xsd:mixed'],
        ];
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
