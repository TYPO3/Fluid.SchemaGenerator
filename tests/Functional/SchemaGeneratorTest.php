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
    protected function tearDown()
    {
        if (file_exists(__DIR__ . '/../Generated/schema.xsd')) {
            unlink(__DIR__ . '/../Generated/schema.xsd');
        }
        if (file_exists(__DIR__ . '/../Generated/phpNamespace.xsd')) {
            unlink(__DIR__ . '/../Generated/phpNamespace.xsd');
        }
        parent::tearDown();
    }

    public function testGenerateSchemaFile()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $this->assertFileExists(__DIR__ . '/../Generated/schema.xsd', 'Could not write namespace file.');
    }

    public function testGenerateSchema()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3Fluid\\Fluid\\ViewHelpers\\' => __DIR__ . '/../../vendor/typo3fluid/fluid/src/ViewHelpers/'
        ]);
        $xml = file_get_contents(__DIR__ . '/../Generated/schema.xsd');
        $this->assertStringStartsWith('<?xml', $xml);
        $this->assertStringEndsWith('</xsd:schema>' . PHP_EOL, $xml);
    }

    public function testNamespaceImportForPHPClass()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $xml = file_get_contents(__DIR__ . '/../Generated/schema.xsd');
        $this->assertStringContainsString('<xsd:import schemaLocation="phpNamespace.xsd" namespace="php/types"/>', $xml);
    }

    public function testNamespacePrefixTag()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $xml = file_get_contents(__DIR__ . '/../Generated/schema.xsd');
        $this->assertStringContainsString('xmlns:php="php/types"', $xml);
    }

    public function testPrefixedType()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $xml = file_get_contents(__DIR__ . '/../Generated/schema.xsd');
        $this->assertStringContainsString('<xsd:attribute type="php:ArrayAccess" name="objects" default="NULL" use="required">', $xml);
    }

    public function testGeneratePHPNamespaceFile()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $this->assertFileExists(__DIR__ . '/../Generated/phpNamespace.xsd');
    }

    public function testNamespaceFormat()
    {
        $generator = new SchemaGenerator(__DIR__ . '/../Generated/schema.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
        $generator->generateXsd([
            'TYPO3\\FluidSchemaGenerator\\Tests\\Fixtures\\' => __DIR__ . '/../Fixtures'
        ]);
        $this->assertFileEquals(__DIR__ . '/../Expected/phpNamespace.xsd', __DIR__ . '/../Generated/phpNamespace.xsd');
    }
}
