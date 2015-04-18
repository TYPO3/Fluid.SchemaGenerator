Fluid ViewHelper Schema Generator
=================================

[![Build Status](https://img.shields.io/travis/NamelessCoder/TYPO3.Fluid.SchemaGenerator.svg?style=flat-square)](https://travis-ci.org/NamelessCoder/TYPO3.Fluid.SchemaGenerator)
[![Coverage](https://img.shields.io/coveralls/NamelessCoder/TYPO3.Fluid.SchemaGenerator.svg?style=flat-square)](https://coveralls.io/r/NamelessCoder/TYPO3.Fluid.SchemaGenerator)

Generates nice XSD schemas for (X)HTML files which can be used in editors to enable
autocompletion of Fluid template code. Can generate schemas for the official as
well as any of your own packages which provide ViewHelpers.

Installation
------------

```bash
composer require namelesscoder/fluid-schema-generator
```

Usage
-----

```bash
./vendor/bin/generateschema VendorName\\PackageName\\ViewHelpers /path/to/ViewHelpers/ > schema.xsd
```
