<?php
namespace TYPO3\FluidSchemaGenerator\Tests\Functional;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3\FluidSchemaGenerator\SchemaGenerator;

/**
 * Class SchemaGeneratorTest
 */
class SchemaGeneratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @test
     */
    public function testGenerateSchema()
    {
        $generator = new SchemaGenerator();
        $xml = $generator->generateXsd(['TYPO3Fluid\\Fluid\\ViewHelpers\\' => __DIR__ . '/../../vendor/typo3fluid/fluid/src/ViewHelpers/']);
        $this->assertStringStartsWith('<?xml', $xml);
        $this->assertStringEndsWith('</xsd:schema>' . PHP_EOL, $xml);
    }
}
