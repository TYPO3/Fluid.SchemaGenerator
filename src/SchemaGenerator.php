<?php
namespace TYPO3Fluid\SchemaGenerator;

use TYPO3\Fluid\Core\ViewHelper\ArgumentDefinition;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\ViewHelperResolver;

/**
 * @package Schemaker
 * @subpackage Service
 */
class SchemaGenerator
{

    /**
     * @var DocCommentParser
     */
    protected $docCommentParser;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->docCommentParser = new DocCommentParser();
    }

    /**
     * Generate the XML Schema definition for a given namespace.
     * It will generate an XSD file for all view helpers in this namespace.
     * The first provided namespace is used when determining the XSD
     * namespace URL that gets recored in the output schema.
     *
     * Map must be an array of ["php\namespace" => "src/ViewHelpers"]
     * values, e.g. an array of class paths indexed by namespace.
     *
     * @param array $namespaceClassPathMap
     * @return string
     * @throws \Exception
     */
    public function generateXsd(array $namespaceClassPathMap)
    {
        $phpNamespace = key($namespaceClassPathMap);
        $phpNamespace = rtrim($phpNamespace, '\\');
        $xsdNamespace = 'http://typo3.org/ns/' . str_replace('\\', '/', rtrim($phpNamespace, '\\'));
        $xmlRootNode = new \SimpleXMLElement(
            '<?xml version="1.0" encoding="UTF-8"?>
			<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema"
				        targetNamespace="' . $xsdNamespace . '">
			</xsd:schema>'
        );
        $classFinder = new ClassFinder();
        $classNames = $classFinder->getClassNamesInPackages($namespaceClassPathMap);
        foreach ($classNames as $className) {
            $this->generateXmlForClassName(new ViewHelperDocumentation($className), $xmlRootNode);
        }
        return $xmlRootNode->asXML();
    }

    /**
     * Get a tag name for a given ViewHelper class.
     * Example: For the View Helper Tx_Fluid_ViewHelpers_Form_SelectViewHelper, and the
     * namespace prefix Tx_Fluid_ViewHelpers, this method returns "form.select".
     *
     * @param string $className Class name
     * @return string
     */
    protected function getTagNameForClass($className)
    {
        $separator = false !== strpos($className, '\\') ? '\\' : '_';
        $className = substr($className, 0, -10);
        $classNameParts = explode($separator, $className);
        $startPosition = array_search('ViewHelpers', $classNameParts) + 1;
        $classNameParts = array_slice($classNameParts, $startPosition);
        $classNameParts = array_map('lcfirst', $classNameParts);
        $tagName = implode('.', $classNameParts);
        return $tagName;
    }

    /**
     * Add a child node to $parentXmlNode, and wrap the contents inside a CDATA section.
     *
     * @param \SimpleXMLElement $parentXmlNode Parent XML Node to add the child to
     * @param string $childNodeName Name of the child node
     * @param string $childNodeValue Value of the child node. Will be placed inside CDATA.
     * @return \SimpleXMLElement the new element
     */
    protected function addChildWithCData(\SimpleXMLElement $parentXmlNode, $childNodeName, $childNodeValue)
    {
        $parentDomNode = dom_import_simplexml($parentXmlNode);
        $domDocument = new \DOMDocument();
        $childNode = $domDocument->appendChild($domDocument->createElement($childNodeName));
        $childNode->appendChild($domDocument->createCDATASection($childNodeValue));
        $childNodeTarget = $parentDomNode->ownerDocument->importNode($childNode, true);
        $parentDomNode->appendChild($childNodeTarget);
        return simplexml_import_dom($childNodeTarget);
    }

    /**
     * Generate the XML Schema for a given class name.
     *
     * @param ViewHelperDocumentation $documentation Class name to generate the schema for.
     * @param \SimpleXMLElement $xmlRootNode XML root node where the xsd:element is appended.
     * @return void
     */
    protected function generateXmlForClassName(ViewHelperDocumentation $documentation, \SimpleXMLElement $xmlRootNode)
    {
        if ($documentation->isIncluded()) {
            $tagName = $this->getTagNameForClass($documentation->getClass());

            $xsdElement = $xmlRootNode->addChild('xsd:element');
            $xsdElement['name'] = $tagName;

            $this->addDocumentation($documentation->getDescription(), $xsdElement);

            $xsdComplexType = $xsdElement->addChild('xsd:complexType');
            $xsdComplexType['mixed'] = 'true';
            $xsdSequence = $xsdComplexType->addChild('xsd:sequence');
            $xsdAny = $xsdSequence->addChild('xsd:any');
            $xsdAny['minOccurs'] = '0';
            $xsdAny['maxOccurs'] = '1';

            $this->addAttributes($documentation, $xsdComplexType);
        }
    }

    /**
     * Add attribute descriptions to a given tag.
     * Initializes the view helper and its arguments, and then reads out the list of arguments.
     *
     * @param string $className Class name where to add the attribute descriptions
     * @param \SimpleXMLElement $xsdElement XML element to add the attributes to.
     * @return void
     */
    protected function addAttributes(ViewHelperDocumentation $documentation, \SimpleXMLElement $xsdElement)
    {
        foreach ($documentation->getArgumentDefinitions() as $argumentDefinition) {
            $default = $argumentDefinition->getDefaultValue();
            $type = $argumentDefinition->getType();
            $xsdAttribute = $xsdElement->addChild('xsd:attribute');
            $xsdAttribute['type'] = $this->convertPhpTypeToXsdType($type);
            $xsdAttribute['name'] = $argumentDefinition->getName();
            $xsdAttribute['default'] = var_export($default, true);
            if ($argumentDefinition->isRequired()) {
                $xsdAttribute['use'] = 'required';
            }
            $this->addDocumentation($argumentDefinition->getDescription(), $xsdAttribute);
        }
    }

    /**
     * @param string $type
     * @return string
     */
    protected function convertPhpTypeToXsdType($type)
    {
        switch ($type) {
            case 'integer':
                return 'xsd:integer';
            case 'float':
                return 'xsd:float';
            case 'double':
                return 'xsd:double';
            case 'boolean':
                return 'xsd:boolean';
            case 'string':
                return 'xsd:string';
            case 'array':
                return 'xsd:array';
            case 'mixed':
                return 'xsd:mixed';
            default:
                return 'xsd:anySimpleType';
        }
    }

    /**
     * Add documentation XSD to a given XML node
     *
     * @param string $documentation Documentation string to add.
     * @param \SimpleXMLElement $xsdParentNode Node to add the documentation to
     * @return void
     */
    protected function addDocumentation($documentation, \SimpleXMLElement $xsdParentNode)
    {
        $documentation = preg_replace('/[^(\x00-\x7F)]*/', '', $documentation);
        $documentation = preg_replace('/(^\ |$)/m', '', $documentation);
        $xsdAnnotation = $xsdParentNode->addChild('xsd:annotation');
        $this->addChildWithCData($xsdAnnotation, 'xsd:documentation', $documentation);
    }
}
