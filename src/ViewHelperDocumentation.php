<?php
namespace TYPO3\FluidSchemaGenerator;

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
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
     * @var RenderingContextInterface|null
     */
    protected $renderingContext;

    /**
     * @var ViewHelperInterface
     */
    protected $viewHelper;

    /**
     * @param string $className
     */
    public function __construct($classNameOrInstance, RenderingContextInterface $renderingContext = null)
    {
        $this->renderingContext = $renderingContext;
        if ($classNameOrInstance instanceof ViewHelperInterface) {
            $this->className = get_class($classNameOrInstance);
            $this->instance = $classNameOrInstance;
        } else {
            $this->className = $classNameOrInstance;
            $this->instance = $this->renderingContext ? $this->renderingContext
                ->getViewHelperResolver()
                ->createViewHelperInstanceFromClassName($classNameOrInstance) : new $classNameOrInstance();
        }
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
        return $this->instance->prepareArguments();
    }
}
