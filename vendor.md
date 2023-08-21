# Folder structure
* vendor
    * doxygen (versyon 1.9.7 x64)
        * doxygen.exe
        * doxyindexer.exe
        * doxysearch.cgi.exe
        * doxywizard.exe
        * libclang.dll
    * doc (doxygen configuration file)
    * doxygen.cmd
    * phpunit.cmd
    * phpunit.phar (version 10.3.2)

doxygen.cmd

```cmd
@echo off
"%~dp0doxygen\doxygen.exe" %* 
```

phpunit.cmd

```cmd
@echo off
php "%~dp0phpunit.phar" %* 
```
You should add vendor folder path to the PATH system variable

