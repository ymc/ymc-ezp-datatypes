<?php

require_once dirname( __FILE__ )."/../../filter_callbacks/int_leading_zero.php";

class TestDatatypeCallbackFilterIntLeadingZero extends PHPUnit_Framework_Testcase
{
    private function minute( $string )
    {
        return ymcDatatypeFilterIntLeadingZero::minute( $string );
    }

    private function day( $string )
    {
        return ymcDatatypeFilterIntLeadingZero::day( $string );
    }

    private function month( $string )
    {
        return ymcDatatypeFilterIntLeadingZero::month( $string );
    }

    private function hour24( $string )
    {
        return ymcDatatypeFilterIntLeadingZero::hour24( $string );
    }

    ////////////////////////////////////////////////////
    // Tests for hour24
    ////////////////////////////////////////////////////

    public function testHour24LowerRangeFail()
    {
        $this->assertNull( $this->hour24( '-1' ) );
    }

    public function testHour24LowerRangePass()
    {
        $this->assertEquals( $this->hour24( '0' ), '' );
    }

    public function testHour24HigherRangeFail()
    {
        $this->assertNull( $this->hour24( '24' ) );
    }

    public function testHour24HigherRangePass()
    {
        $this->assertEquals( $this->hour24( '23' ), '23' );
    }

    public function testHour24InnerRangeLeadingZeros()
    {
        $this->assertEquals( $this->hour24( '09' ), '9' );
    }

    ////////////////////////////////////////////////////
    // Tests for minute
    ////////////////////////////////////////////////////

    public function testMinuteLowerRangeFail()
    {
        $this->assertNull( $this->minute( '-1' ) );
    }

    public function testMinuteLowerRangePass()
    {
        $this->assertEquals( $this->minute( '0' ), '' );
    }

    public function testMinuteHigherRangeFail()
    {
        $this->assertNull( $this->minute( '60' ) );
    }

    public function testMinuteHigherRangePass()
    {
        $this->assertEquals( $this->minute( '59' ), '59' );
    }

    public function testMinuteInnerRangeLeadingZeros()
    {
        $this->assertEquals( $this->minute( '09' ), '9' );
    }

    ////////////////////////////////////////////////////
    // Tests for day
    ////////////////////////////////////////////////////

    public function testDayLowerRangeFail()
    {
        $this->assertNull( $this->day( '0' ) );
    }

    public function testDayLowerRangePass()
    {
        $this->assertEquals( $this->day( '1' ), '1' );
    }

    public function testDayHigherRangeFail()
    {
        $this->assertNull( $this->day( '32' ) );
    }

    public function testDayHigherRangePass()
    {
        $this->assertEquals( $this->day( '31' ), '31' );
    }

    public function testDayInnerRangeLeadingZeros()
    {
        $this->assertEquals( $this->day( '09' ), '9' );
    }

    ////////////////////////////////////////////////////
    // Tests for month
    ////////////////////////////////////////////////////

    public function testMonthLowerRangeFail()
    {
        $this->assertNull( $this->month( '0' ) );
    }

    public function testMonthLowerRangePass()
    {
        $this->assertEquals( $this->month( '1' ), '1' );
    }

    public function testMonthHigherRangeFail()
    {
        $this->assertNull( $this->month( '13' ) );
    }

    public function testMonthHigherRangePass()
    {
        $this->assertEquals( $this->month( '12' ), '12' );
    }

    public function testMonthInnerRangeLeadingZeros()
    {
        $this->assertEquals( $this->month( '09' ), '9' );
    }
}
