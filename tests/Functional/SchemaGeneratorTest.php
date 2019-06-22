<?php
declare(strict_types=1);

namespace TYPO3\FluidSchemaGenerator\Tests\Functional;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use PHPUnit\Framework\TestCase;
use TYPO3\FluidSchemaGenerator\SchemaGenerator;

/**
 * Test case
 */
class SchemaGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function testGenerateSchema()
    {
        $generator = new SchemaGenerator();
        $xml = $generator->generateXsd([
            'TYPO3Fluid\\Fluid\\ViewHelpers\\' => __DIR__ . '/../../vendor/typo3fluid/fluid/src/ViewHelpers/'
        ]);
        $this->assertStringStartsWith('<?xml', $xml);
        $this->assertStringEndsWith('</xsd:schema>' . PHP_EOL, $xml);
    }

    /**
     * @test This test asserts whether an argument of type object
     */
    public function testNamespaceImportForPHPClass()
    {
        $generator = new SchemaGenerator();
        $xml = $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $this->assertStringContainsString('<xsd:import schemaLocation="php.xsd" namespace="php/types"/>', $xml);
    }

    public function testNamespacePrefixTag()
    {

    }

    public function testPrefixedType()
    {
        $generator = new SchemaGenerator();
        $xml = $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $this->assertStringContainsString('<xsd:attribute type="php:ArrayAccess" name="objects" default="NULL" use="required">', $xml);
    }

    public function testGeneratePHPNamespaceFile()
    {

    }

    public function testMatchingTargetNamespaceDeclaration()
    {

    }
}
