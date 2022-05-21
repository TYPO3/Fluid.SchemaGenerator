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
     * @var \Closure
     */
    protected $classInstancingClosure;

    /**
     * @param class-string $className
     * @param \Closure $classInstancingClosure
     */
    public function __construct(string $className, \Closure $classInstancingClosure)
    {
        $this->className = $className;
        $this->classInstancingClosure = $classInstancingClosure;
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
        $closure = $this->classInstancingClosure;
        $docCommentParser = $closure(DocCommentParser::class);
        return $docCommentParser->parseDocComment($reflectionClass->getDocComment());
    }

    /**
     * @return ArgumentDefinition[]
     */
    public function getArgumentDefinitions(): array
    {
        $className = $this->className;
        $closure = $this->classInstancingClosure;
        $viewHelper = $closure($className);
        return $viewHelper->prepareArguments();
    }
}
