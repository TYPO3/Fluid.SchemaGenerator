<?php
namespace TYPO3\Fluid\SchemaGenerator;

use TYPO3\Fluid\Core\ViewHelper\ArgumentDefinition;

/**
 * @package Schemaker
 * @subpackage Service
 */
class SchemaGenerator {

	/**
	 * @var DocCommentParser
	 */
	protected $docCommentParser;

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->docCommentParser = new DocCommentParser();
	}

	/**
	 * Get all class names inside this namespace and return them as array.
	 *
	 * @param string $packagePath
	 * @param string $phpNamespace
	 * @return array
	 */
	protected function getClassNamesInPackage($packagePath, $phpNamespace) {
		$allViewHelperClassNames = array();
		$filesInPath = new \RecursiveDirectoryIterator($packagePath, \RecursiveDirectoryIterator::SKIP_DOTS);
		$packagePathLength = strlen($packagePath);
		foreach ($filesInPath as $filePathAndFilename) {
			$relativePath = substr($filePathAndFilename, $packagePathLength, -4);
			$classLocation = str_replace('/', '\\', $relativePath);
			$className = $phpNamespace . $classLocation;
			if (class_exists($className)) {
				$parent = $className;
				while ($parent = get_parent_class($parent)) {
					array_push($allViewHelperClassNames, $className);
				}
			}
		}
		$affectedViewHelperClassNames = array();
		foreach ($allViewHelperClassNames as $viewHelperClassName) {
			$classReflection = new \ReflectionClass($viewHelperClassName);
			if ($classReflection->isAbstract() === FALSE) {
				$affectedViewHelperClassNames[] = $viewHelperClassName;
			}
		}
		sort($affectedViewHelperClassNames);
		return $affectedViewHelperClassNames;
	}

	/**
	 * Get a tag name for a given ViewHelper class.
	 * Example: For the View Helper Tx_Fluid_ViewHelpers_Form_SelectViewHelper, and the
	 * namespace prefix Tx_Fluid_ViewHelpers, this method returns "form.select".
	 *
	 * @param string $className Class name
	 * @return string
	 */
	protected function getTagNameForClass($className) {
		$separator = FALSE !== strpos($className, '\\') ? '\\' : '_';
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
	protected function addChildWithCData(\SimpleXMLElement $parentXmlNode, $childNodeName, $childNodeValue) {
		$parentDomNode = dom_import_simplexml($parentXmlNode);
		$domDocument = new \DOMDocument();
		$childNode = $domDocument->appendChild($domDocument->createElement($childNodeName));
		$childNode->appendChild($domDocument->createCDATASection($childNodeValue));
		$childNodeTarget = $parentDomNode->ownerDocument->importNode($childNode, TRUE);
		$parentDomNode->appendChild($childNodeTarget);
		return simplexml_import_dom($childNodeTarget);
	}

	/**
	 * Generate the XML Schema definition for a given namespace.
	 * It will generate an XSD file for all view helpers in this namespace.
	 *
	 * @param string $phpNamespace
	 * @param string $packagePath
	 * @return string
	 * @throws \Exception
	 */
	public function generateXsd($phpNamespace, $packagePath = 'src/ViewHelpers') {
		$packagePath = rtrim($packagePath, '/') . '/';
		$phpNamespace = rtrim($phpNamespace, '\\') . '\\';
		$xsdNamespace = 'http://typo3.org/ns/' . str_replace('\\', '/', rtrim($phpNamespace, '\\'));
		$classNames = $this->getClassNamesInPackage($packagePath, $phpNamespace);
		if (count($classNames) === 0) {
			throw new \RuntimeException(sprintf('No ViewHelpers found in path "%s"', $packagePath), 1330029328);
		}
		$xmlRootNode = new \SimpleXMLElement(
			'<?xml version="1.0" encoding="UTF-8"?>
			<xsd:schema xmlns:xsd="http://www.w3.org/2001/XMLSchema"
				        xmlns:php="http://www.php.net/"
				        targetNamespace="' . $xsdNamespace . '">
			</xsd:schema>'
		);
		foreach ($classNames as $className) {
			$this->generateXmlForClassName($className, $xmlRootNode);
		}
		return $xmlRootNode->asXML();
	}

	/**
	 * Generate the XML Schema for a given class name.
	 *
	 * @param string $className Class name to generate the schema for.
	 * @param \SimpleXMLElement $xmlRootNode XML root node where the xsd:element is appended.
	 * @return void
	 */
	protected function generateXmlForClassName($className, \SimpleXMLElement $xmlRootNode) {
		$reflectionClass = new \ReflectionClass($className);
		if ($reflectionClass->isSubclassOf('TYPO3\\Fluid\\Core\\ViewHelper\\AbstractViewHelper')) {
			$tagName = $this->getTagNameForClass($className);

			$xsdElement = $xmlRootNode->addChild('xsd:element');
			$xsdElement['name'] = $tagName;
			$this->docCommentParser->parseDocComment($reflectionClass->getDocComment());
			$this->addDocumentation($this->docCommentParser->getDescription(), $xsdElement);

			$xsdComplexType = $xsdElement->addChild('xsd:complexType');
			$xsdComplexType['mixed'] = 'true';
			$xsdSequence = $xsdComplexType->addChild('xsd:sequence');
			$xsdAny = $xsdSequence->addChild('xsd:any');
			$xsdAny['minOccurs'] = '0';
			$xsdAny['maxOccurs'] = '1';

			$this->addAttributes($className, $xsdComplexType);
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
	protected function addAttributes($className, \SimpleXMLElement $xsdElement) {
		$viewHelper = new $className();
		/** @var ArgumentDefinition[] $argumentDefinitions */
		$argumentDefinitions = $viewHelper->prepareArguments();

		foreach ($argumentDefinitions as $argumentDefinition) {
			$default = $argumentDefinition->getDefaultValue();
			$type = $argumentDefinition->getType();
			$xsdAttribute = $xsdElement->addChild('xsd:attribute');
			$xsdAttribute['type'] = $this->convertPhpTypeToXsdType($type);
			$xsdAttribute['name'] = $argumentDefinition->getName();
			$xsdAttribute['default'] = var_export($default, TRUE);
			$xsdAttribute['php:type'] = $type;
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
	protected function convertPhpTypeToXsdType($type) {
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
	protected function addDocumentation($documentation, \SimpleXMLElement $xsdParentNode) {
		$documentation = preg_replace('/[^(\x00-\x7F)]*/', '', $documentation);
		$documentation = preg_replace('/(^\ |$)/m', '', $documentation);
		$xsdAnnotation = $xsdParentNode->addChild('xsd:annotation');
		$this->addChildWithCData($xsdAnnotation, 'xsd:documentation', $documentation);
	}

}
