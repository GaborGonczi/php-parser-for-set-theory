# Folder structure
* vendor
    * doxygen (versyon 1.9.7 x64)
        * doxygen.exe
        * doxyindexer.exe
        * doxysearch.cgi.exe
        * doxywizard.exe
        * libclang.dll
    * Graphviz (graphviz-9.0.0)
    * doc (doxygen configuration file)
    * dot.cmd
    * doxygen.cmd
    * jsdoc.cmd (4.0.2 installed as a global NodeJs package)
    * jsdoc.json
    * phpmd.cmd
    * phpmd.phar (version 2.15.0)
    * phpunit.cmd
    * phpunit.phar (version 10.3.2)

dot.cmd

```cmd
@echo off
"%~dp0Graphviz/bin/dot.exe" %* 
```

doxygen.cmd

```cmd
@echo off
"%~dp0doxygen\doxygen.exe" %* 
```

jsdoc.cmd

```cmd
@echo off
jsdoc -c ./vendor/jsdoc.json -r -d ./doc/src/js 
```

jsdoc.json

```json
{
    "plugins": [],
    "recurseDepth": 100,
    "source": {
        "include": ["./src/js/"],
        "exclude": [],
        "includePattern": ".+\\.js(doc|x)?$",
        "excludePattern": "(^|\\/|\\\\)_"
    },
    "sourceType": "module",
    "tags": {
        "allowUnknownTags": true,
        "dictionaries": ["jsdoc","closure"]
    },
    "templates": {
        "cleverLinks": false,
        "monospaceLinks": false
    }
}
```

phpmd.cmd

```cmd
@echo off
php "%~dp0phpmd.phar" %* 
```

phpunit.cmd

```cmd
@echo off
php "%~dp0phpunit.phar" %* 
```
You should add vendor folder path to the PATH system variable

