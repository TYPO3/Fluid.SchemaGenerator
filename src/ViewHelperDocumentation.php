<?php

declare(strict_types=1);

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3\FluidSchemaGenerator;

use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class ViewHelperDocumentation
 */
class ViewHelperDocumentation
{
    /**
     * @var class-string
     */
    protected $className;

    /**
     * @param class-string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    public function getClass(): string
    {
        return $this->className;
    }

    /**
     * Returns TRUE if the class should be included in the schema, FALSE otherwise.
     */
    public function isIncluded(): bool
    {
        $reflectionClass = new \ReflectionClass($this->className);
        return $reflectionClass->implementsInterface(ViewHelperInterface::class);
    }

    public function getDescription(): string
    {
        $reflectionClass = new \ReflectionClass($this->className);
        $docComment = $reflectionClass->getDocComment();
        if ($docComment) {
            return (new DocCommentParser())->parseDocComment($docComment);
        }
        return '';
    }

    /**
     * @return ArgumentDefinition[]
     */
    public function getArgumentDefinitions(): array
    {
        $className = $this->className;
        // We are reflecting the class here and instantiate it without calling __construct()
        // to avoid especially DI requirements. This has the advantage that we can simply call
        // VH's prepareArguments() which typically does not use DI, but still fetch arguments
        // from the entire inheritance chain of the VH in question and thus retrieve the full
        // aruments array.
        $viewHelperReflection = new \ReflectionClass($className);
        /** @var ViewHelperInterface $viewHelper */
        $viewHelper = $viewHelperReflection->newInstanceWithoutConstructor();
        return $viewHelper->prepareArguments();
    }
}
