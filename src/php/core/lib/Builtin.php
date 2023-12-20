<?php
namespace core\lib;

/**
* A class that defines some built-in constants and names.
*
* This class contains constants for the type of built-in functions and an array of their names.
* It is used to check if an element is a built-in or not.
* @package core\lib;
*/
class Builtin{
    /**
    * The type of built-in elements.
    * @public
    */
    const TYPE="builtin";

    /**
    * The names of built-in elements.
    * @public
    */
    const NAMES=["PointSetDiagram","Venn","add","delete"];
}