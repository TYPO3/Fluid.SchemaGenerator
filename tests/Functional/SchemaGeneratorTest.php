<?php
namespace TYPO3\Fluid\SchemaGenerator\Tests\Functional;
use TYPO3\Fluid\SchemaGenerator\SchemaGenerator;

/**
 * Class SchemaGeneratorTest
 */
class SchemaGeneratorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @test
	 */
	public function testGenerateSchema() {
		$generator = new SchemaGenerator();
		$xml = $generator->generateXsd(
			'TYPO3\\Fluid\\ViewHelpers',
			__DIR__ . '/../../vendor/namelesscoder/fluid/src/ViewHelpers'
		);
		$this->assertStringStartsWith('<?xml', $xml);
		$this->assertStringEndsWith('</xsd:schema>' . PHP_EOL, $xml);
	}

}
