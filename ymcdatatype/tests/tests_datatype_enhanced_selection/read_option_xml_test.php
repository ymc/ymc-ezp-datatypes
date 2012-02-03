<?php

class ReadOptionXmlTest extends PHPUnit_Framework_Testcase
{
    // ClassContentClass ;-)
    const CCC ='ymcDatatypeEnhancedSelectionClassContent' ;

    private $xml = NULL;

    public function setUp()
    {
        $this->xml = file_get_contents( dirname( __FILE__ ).'/data/options.xml' );
    }

    public function testAllOptionAttributesAreRead()
    {
        $options = call_user_func( 
            array( self::CCC, 'optionStringToArray' ),
            $this->xml
        );

        $this->assertType( 'array', $options );
        $this->assertEquals( 19, count( $options ) );

        $option = $options[5];
        $this->assertEquals( '5', ( string ) $option['id'] );
        $this->assertEquals( 'Metal', ( string ) $option['name'] );
        $this->assertEquals( '1102', ( string ) $option['identifier'] );
        $this->assertEquals( '6',( string ) $option['priority'] );
    }
}
