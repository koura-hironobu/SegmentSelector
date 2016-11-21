<?php

use PHPUnit\Framework\TestCase;


class SegmentSelectorTest extends TestCase
{
    public function test01() {
        $text = "1.1.1.0/24\n\n\r\n2.2.0.0-2.2.1.0\n\r\n\r\n\r\n\n\n3.0.0.0/8";

        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource($text);

        $result = $selector->evaluate("1.1.1.0");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "1.1.1.0/24");

        $result = $selector->evaluate("1.1.1.1");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "1.1.1.0/24");

        $result = $selector->evaluate("1.1.1.254");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "1.1.1.0/24");

        $result = $selector->evaluate("1.1.1.255");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "1.1.1.0/24");

        $result = $selector->evaluate("1.1.0.0");
        $this->assertNull($result);
        $result = $selector->evaluate("1.1.0.1");
        $this->assertNull($result);
        $result = $selector->evaluate("1.1.0.255");
        $this->assertNull($result);
    }

    public function test02() {
        $text = "1.1.1.0/24\n\n\r\n2.2.0.0-2.2.1.0\n\r\n\r\n\r\n\n\n3.0.0.0/8";

        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource($text);

        $result = $selector->evaluate("2.2.0.0");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "2.2.0.0-2.2.1.0");

        $result = $selector->evaluate("2.2.0.1");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "2.2.0.0-2.2.1.0");

        $result = $selector->evaluate("2.2.0.255");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "2.2.0.0-2.2.1.0");

        $result = $selector->evaluate("2.2.1.0");
        $this->assertNotNull($result);
        $this->assertEquals($result->source(), "2.2.0.0-2.2.1.0");

        $result = $selector->evaluate("2.2.1.1");
        $this->assertNull($result);
        $result = $selector->evaluate("2.2.1.2");
        $this->assertNull($result);
    }

    public function testDumpJSON() {
        $text = "1.1.1.0/24\n\n\r\n2.2.0.0-2.2.1.0\n\r\n\r\n\r\n\n\n3.0.0.0/8";

        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource($text);

        $json_str = '{"entries":[{"range":["1.1.1.0","2.1.0.255"],"source":"1.1.1.0\/24","action":null},{"range":["2.2.0.0","2.2.1.0"],"source":"2.2.0.0-2.2.1.0","action":null},{"range":["3.0.0.0","3.0.0.255"],"source":"3.0.0.0\/8","action":null}]}';
        $this->assertEquals($selector->dumpJSON(), $json_str);
    }

    public function testLoadJSON() {
        $json_str = '{"entries":[{"range":["1.1.1.0","2.1.0.255"],"source":"1.1.1.0\/24","action":null},{"range":["2.2.0.0","2.2.1.0"],"source":"2.2.0.0-2.2.1.0","action":null},{"range":["3.0.0.0","3.0.0.255"],"source":"3.0.0.0\/8","action":null}]}';

        $selector = new SegmentSelector\SegmentSelector();
        $selector->loadJSON($json_str);

        $this->assertEquals($selector->dumpJSON(), $json_str);
    }

    public function testComment() {
        $text = "1.1.1.0/24 #hogehoge\n\n\r\n2.2.0.0-2.2.1.0\n\r\n\r\n# aaa\r\n\n\n3.0.0.0/8";

        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource($text);

        $this->assertEquals(count($selector->entries()), 3);
    }

    public function testX1() {
        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource("111.101.189.150-111.101.189.150");

        $this->assertNotNull($selector->evaluate("111.101.189.150"));

        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource("111.101.189.150/32");

        $this->assertNotNull($selector->evaluate("111.101.189.150"));
    }

    public function testX2() {
        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource("111.101.189.151-111.101.189.151");

        $this->assertNull($selector->evaluate("111.101.189.150"));

        $selector = new SegmentSelector\SegmentSelector();
        $selector->initWithSource("111.101.189.151/32");

        $this->assertNull($selector->evaluate("111.101.189.150"));
    }
}


?>
