# stex - Simple Template Extended Xslt

[![Build Status](https://travis-ci.org/racztiborzoltan/stex.svg?branch=master)](https://travis-ci.org/racztiborzoltan/stex)

- extended from PHP built in \XSLTProcessor class.
    - support all \XSLTProcessor features
- [PSR-11](https://www.php-fig.org/psr/psr-11/) container support
- some new small syntax
    - PSR-11 container calls (not static method calls!)
    - using CSS selectors in "match" and "select" XSLT attributes


## Example XML file 

[example.xml](examples/example.xml)


## PSR-11 Container support

```xml
<!-- new xslt function syntax -->
this:container(...)

this:container('CONTAINER_SCALAR_ITEM_NAME')

this:container('CONTAINER_FUNCTION_ITEM_NAME', 'PARAMETER_1', 'PARAMETER_2', '...')

this:container('CONTAINER_OBJECT_ITEM_NAME', 'METHOD_NAME', 'FIRST_PARAMETER', 'SECOND_PARAMETER', '...')
```

Usage example:
```xml
<xsl:value-of select="this:container('scalar_item_name')"/>
<xsl:value-of select="this:container('function_item_name', 'foobar', 123)"/>
<xsl:value-of select="this:container('object_item_name', 'method_name', 'arg_1', 'arg2')"/>
```

### Example code

[psr-11_container_calls.php](examples/container_calls/psr-11_container_calls.php)


## CSS Select Example

### Example code

[css_select.php](examples/css_select/css_select.php)


## other examples

in [examples](examples) directory!

--------------------------------------------------------------------------------

[http://racztiborzoltan.github.io](http://racztiborzoltan.github.io)
