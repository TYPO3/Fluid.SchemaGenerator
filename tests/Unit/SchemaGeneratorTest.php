<?php
namespace TYPO3\Fluid\SchemaGenerator\Tests\Unit;

use FluidTYPO3\Schemaker\Service\SchemaService;
use TYPO3\Fluid\SchemaGenerator\SchemaGenerator;

/**
 * Class SchemaGeneratorTest
 */
class SchemaGeneratorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function testPerformsInjections() {
		$instance = new SchemaGenerator();
		$this->assertAttributeInstanceOf('TYPO3\\Fluid\\SchemaGenerator\\DocCommentParser', 'docCommentParser', $instance);
	}

	/**
	 * @param string $class
	 * @param string $expected
	 * @test
	 * @dataProvider getTagNameForClassTestValues
	 */
	public function testGetTagNameForClass($class, $expected) {
		$instance = new SchemaGenerator();
		$result = $this->callInaccessibleMethod($instance, 'getTagNameForClass', $class);
		$this->assertEquals($expected, $result);
	}

	/**
	 * @return array
	 */
	public function getTagNameForClassTestValues() {
		return array(
			array('FluidTYPO3\\Vhs\\ViewHelpers\\Content\\RenderViewHelper', 'content.render'),
			array('FluidTYPO3\\Vhs\\ViewHelpers\\Content\\GetViewHelper', 'content.get'),
			array('TYPO3\\Fluid\\ViewHelpers\\IfViewHelper', 'if'),
			array('TYPO3\\Fluid\\ViewHelpers\\Format\\HtmlentitiesViewHelper', 'format.htmlentities'),
		);
	}

	/**
	 * @test
	 */
	public function testGenerateXsdErrorsWhenNoViewHelpersInPackage() {
		$service = $this->getMock('TYPO3\\Fluid\\SchemaGenerator\\SchemaGenerator', array('getClassNamesInPackage'));
		$service->expects($this->once())->method('getClassNamesInPackage')->willReturn(array());
		$this->setExpectedException('RuntimeException');
		$service->generateXsd('FluidTYPO3.Schemaker', 'test');
	}

	/**
	 * @dataProvider getConvertPhpTypeToXsdTypeTestValues
	 * @param string $input
	 * @param string $expected
	 */
	public function testConvertPhpTypeToXsdType($input, $expected) {
		$instance = new SchemaGenerator();
		$result = $this->callInaccessibleMethod($instance, 'convertPhpTypeToXsdType', $input);
		$this->assertEquals($expected, $result);
	}

	public function getConvertPhpTypeToXsdTypeTestValues() {
		return array(
			array('', 'xsd:anySimpleType'),
			array('integer', 'xsd:integer'),
			array('float', 'xsd:float'),
			array('double', 'xsd:double'),
			array('boolean', 'xsd:boolean'),
			array('string', 'xsd:string'),
			array('array', 'xsd:array'),
			array('mixed', 'xsd:mixed'),
		);
	}

	/**
	 * @return mixed
	 */
	protected function callInaccessibleMethod() {
		$arguments = func_get_args();
		$instance = array_shift($arguments);
		$method = array_shift($arguments);
		$method = new \ReflectionMethod($instance, $method);
		$method->setAccessible(TRUE);
		return $method->invokeArgs($instance, $arguments);
	}

}
