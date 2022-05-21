Fluid ViewHelper Schema Generator
=================================

[![Tests](https://github.com/lolli42/Fluid.SchemaGenerator/actions/workflows/tests.yml/badge.svg)](https://github.com/lolli42/Fluid.SchemaGenerator/actions/workflows/tests.yml)

What does it do?
----------------

This package is an add-on for [Fluid](https://github.com/TYPO3/Fluid/). It can be helpful
when writing fluid templates by auto completing view helper arguments.

This package is usually added as an additional composer 'require-dev' dependency
to your existing project.

Its main purpose is to generate XSD schemas for (X)HTML files which can be used in editors
to enable auto completion of Fluid template code. Can generate schemas for the official as
well as any of your own packages which provide ViewHelpers.

Installation
------------

```bash
composer require --dev typo3/fluid-schema-generator
```

Example
-------

Let's say there is a Fluid template file in your extension - `Resource/Private/Templates/MyTemplate.html` that
uses view helpers with two different namespaces:

```xml
<html
    xmlns:f="http://typo3.org/ns/TYPO3/CMS/Fluid/ViewHelpers"
    xmlns:be="http://typo3.org/ns/TYPO3/CMS/Backend/ViewHelpers"
    data-namespace-typo3-fluid="true">

<f:render partial="My/Partial" arguments="{_all}" />
... more HTML and view helper usages

</html>
```

IDE`s usually do not understand the tag "f:render" out of the box and will highlight them as
invalid. Generating an xsd schema for this namespace however will make them understand the
tag and its possible attributes. The example generates schema for these two namespaces into
the root directory of the project:

```bash
./bin/generateschema TYPO3\\CMS\\Fluid TYPO3\\CMS\\Backend > mySchema.xsd
```

This call will search all view helper classes in the given namespaces and creates an according
xsd file that contain target namespaces for "f" and "be". An IDE like PhpStorm then typically
auto detects this file (if not, ALT+Enter onto the xmlns url in the template above and improt
the file) "understands" the tags and allows attribute auto completion.


Usage
-----

```bash
./vendor/bin/generateschema VendorName\\PackageName VendorName\\OptionalSecondPackage > schema.xsd
```

Provide as many package namespaces as desired and all ViewHelper classes in all those
namespaces will be included in the schame. The *first* provided namespace gets used
when determining the XSD namespace URL.


License
-------

MIT License. See LICENSE file.
