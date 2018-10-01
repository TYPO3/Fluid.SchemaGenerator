Fluid ViewHelper Schema Generator
=================================

[![Build Status](https://travis-ci.org/TYPO3/Fluid.SchemaGenerator.svg?branch=master)](https://travis-ci.org/TYPO3/Fluid.SchemaGenerator)
[![Coverage](https://img.shields.io/coveralls/NamelessCoder/TYPO3.Fluid.SchemaGenerator.svg?branch=master&style=flat-square)](https://coveralls.io/r/NamelessCoder/TYPO3.Fluid.SchemaGenerator)

Generates nice XSD schemas for (X)HTML files which can be used in editors to enable
autocompletion of Fluid template code. Can generate schemas for the official as
well as any of your own packages which provide ViewHelpers.

Installation
------------

```bash
composer require typo3/fluid-schema-generator
```

Usage
-----

```bash
./vendor/bin/generateschema VendorName\\PackageName VendorName\\OptionalSecondPackage > schema.xsd
```

Provide as many package namespaces as desired and all ViewHelper classes in all those
namespaces will be included in the schame. The *first* provided namespace gets used
when determining the XSD namespace URL.
