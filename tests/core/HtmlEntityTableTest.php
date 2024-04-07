<?php

use \PHPUnit\Framework\TestCase;
use \core\HtmlEntityTable;

class HtmlEntityTableTest extends TestCase
{

    /**
    * @dataProvider entityProvider
    */
    public function testEntity($entity, $expected)
    {
        $this->assertEquals($expected,HtmlEntityTable::TABLE[$entity]);
    }

    public static function entityProvider()
    {
        return [
            ["&isin;", "∈"],
            ["&notin;","∉"],
            ["&sube;","⊆"],
            ["&sub;","⊂"],
            ["&comp;","∁"],
            ["&cup;", "∪"],
            ["&cap;","∩"],
            ["&and;","∧"],
            ["&or;","∨"],
            ["&setminus;","∖"],
            ["&mid;","∣"],
            ["&nmid;","∤"]
        ];
    }
}