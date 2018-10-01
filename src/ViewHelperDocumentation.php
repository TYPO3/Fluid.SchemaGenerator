<?php
namespace TYPO3\FluidSchemaGenerator;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\ArgumentDefinition;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class ViewHelperDocumentation
 */
class ViewHelperDocumentation
{
    /**
     * @var string
     */
    protected $className;

    /**
     * @var \Closure
     */
    protected $classInstancingClosure;

    /**
     * @param string $className
     * @param \Closure $classInstancingClosure
     */
    public function __construct($className, \Closure $classInstancingClosure)
    {
        $this->className = $className;
        $this->classInstancingClosure = $classInstancingClosure;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->className;
    }

    /**
     * Returns TRUE if the class should be included in the schema, FALSE otherwise.
     *
     * @return boolean
     */
    public function isIncluded()
    {
        $reflectionClass = new \ReflectionClass($this->className);
        return $reflectionClass->implementsInterface(ViewHelperInterface::class);
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        $reflectionClass = new \ReflectionClass($this->className);
        $closure = $this->classInstancingClosure;
        $docCommentParser = $closure(DocCommentParser::class);
        return $docCommentParser->parseDocComment($reflectionClass->getDocComment());
    }

    /**
     * @return ArgumentDefinition[]
     */
    public function getArgumentDefinitions()
    {
        $className = $this->className;
        $closure = $this->classInstancingClosure;
        $viewHelper = $closure($className);
        return $viewHelper->prepareArguments();
    }
}
