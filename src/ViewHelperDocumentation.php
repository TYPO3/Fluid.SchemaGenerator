<?php
namespace TYPO3\FluidSchemaGenerator;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;

/**
 * Class ViewHelperDocumentation
 *
 * Wrapper to consume documentation about ViewHelpers
 * from their source code.
 */
class ViewHelperDocumentation
{

    /**
     * @var string
     */
    protected $className;

    /**
     * @param string $className
     */
    public function __construct($className)
    {
        $this->className = $className;
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
        $docCommentParser = new DocCommentParser();
        return $docCommentParser->parseDocComment($reflectionClass->getDocComment());
    }

    /**
     * @return ArgumentDefinition[]
     */
    public function getArgumentDefinitions()
    {
        $className = $this->className;
        $viewHelper = new $className();
        return $viewHelper->prepareArguments();
    }
}
