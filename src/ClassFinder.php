<?php

declare(strict_types=1);

/*
 * This file belongs to the package "TYPO3 FluidSchemaGenerator".
 * See LICENSE.txt that was shipped with this package.
 */

namespace TYPO3\FluidSchemaGenerator;

/**
 * Finds classes in installed Composer packages.
 */
class ClassFinder
{
    /**
     * Get all class names in packages specified by $packagePaths.
     *
     * @param array<string, string> $packagePaths
     * @return array<int, class-string>
     * @throws \RuntimeException
     */
    public function getClassNamesInPackages(array $packagePaths): array
    {
        $classNames = [];
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
     * @return array<int, class-string>
     */
    public function getClassNamesInPackage(string $packagePath, string $phpNamespace): array
    {
        $allViewHelperClassNames = [];
        $affectedViewHelperClassNames = [];

        $packagePath = rtrim($packagePath, '/') . '/';
        $filesInPath = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($packagePath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );
        $packagePathLength = strlen($packagePath);
        foreach ($filesInPath as $filePathAndFilename) {
            /** @var \RecursiveDirectoryIterator $filePathAndFilename */
            $relativePath = substr((string)$filePathAndFilename, $packagePathLength, -4);
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
            if ($classReflection->isAbstract() === false) {
                $affectedViewHelperClassNames[] = $viewHelperClassName;
            }
        }

        sort($affectedViewHelperClassNames);
        return $affectedViewHelperClassNames;
    }
}
