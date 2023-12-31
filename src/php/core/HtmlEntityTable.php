<?php
namespace core;

/**
 * A class that helps to convert htmlentities to the right form.
 *
 * @package core
 */
class HtmlEntityTable
{

    const TABLE = [
        "&isin;"     => "∈",
        "&notin;"    => "∉",
        "&sube;"     => "⊆",
        "&sub;"      => "⊂",
        "&comp;"     => "∁",
        "&cup;"      => "∪",
        "&cap;"      => "∩",
        "&and;"      => "∧",
        "&or;"       => "∨",
        "&setminus;" => "∖",
        "&mid;"      => "∣",
        "&nmid;"     => "∤"
    ];
}