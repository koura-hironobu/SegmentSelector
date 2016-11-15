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

}


?>
