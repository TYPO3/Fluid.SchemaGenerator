<?php
namespace TYPO3Fluid\SchemaGenerator;

/**
 * Class ClassFinder
 */
class ClassFinder {

	/**
	 * @param $packagePaths
	 * @param $phpNamespace
	 */
	public function getClassNamesInPackages(array $packagePaths) {
		$classNames = array();
		foreach ($packagePaths as $namespace => $classesPath) {
			$classNames = array_replace($classNames, $this->getClassNamesInPackage($classesPath, $namespace));
			if (count($classNames) === 0) {
				throw new \RuntimeException(sprintf('No ViewHelpers found in path "%s"', $classesPath), 1330029328);
			}
		}
		return $classNames;
	}

	/**
	 * Get all class names inside this namespace and return them as array.
	 * To generate a merged namespace simply provide multiple paths (with
	 * comma as separator) as $packagePaths argument value.
	 *
	 * @param string $packagePath
	 * @param string $phpNamespace
	 * @return array
	 */
	public function getClassNamesInPackage($packagePath, $phpNamespace) {
		$allViewHelperClassNames = array();
		$affectedViewHelperClassNames = array();

		$packagePath = rtrim($packagePath, '/') . '/';
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
		foreach ($allViewHelperClassNames as $viewHelperClassName) {
			$classReflection = new \ReflectionClass($viewHelperClassName);
			if ($classReflection->isAbstract() === FALSE) {
				$affectedViewHelperClassNames[] = $viewHelperClassName;
			}
		}

		sort($affectedViewHelperClassNames);
		return $affectedViewHelperClassNames;
	}

}
