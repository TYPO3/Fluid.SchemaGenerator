<?php
declare(strict_types=1);

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3\FluidSchemaGenerator\Tests\Functional;

use PHPUnit\Framework\TestCase;
use TYPO3\FluidSchemaGenerator\SchemaGenerator;

class SchemaGeneratorTest extends TestCase
{
    /**
     * @test
     */
    public function testGenerateSchema(): void
    {
        $generator = new SchemaGenerator();
        $xml = $generator->generateXsd([
            'TYPO3Fluid\\Fluid\\ViewHelpers\\' => __DIR__ . '/../../vendor/typo3fluid/fluid/src/ViewHelpers/'
        ]);
        self::assertStringStartsWith('<?xml', $xml);
        self::assertStringEndsWith('</xsd:schema>' . PHP_EOL, $xml);
    }
}
