<?php

class OptionArrayToXmlTest extends PHPUnit_Framework_Testcase
{
    private $options = array();

    public function setUp()
    {
        $this->options = array (
                0 =>
                array (
                    'id' => '0',
                    'name' => 'Chanson',
                    'identifier' => '1093',
                    'priority' => '1',
                    ),
                1 =>
                array (
                    'id' => '15',
                    'name' => 'Country',
                    'identifier' => '1094',
                    'priority' => '2',
                    ),
                2 =>
                array (
                    'id' => '2',
                    'name' => 'Electro',
                    'identifier' => '1096',
                    'priority' => '3',
                    ),
                3 =>
                    array (
                            'id' => '3',
                            'name' => 'Folk',
                            'identifier' => '1098',
                            'priority' => '4',
                          ),
                4 =>
                    array (
                            'id' => '4',
                            'name' => 'Funk',
                            'identifier' => '1100',
                            'priority' => '5',
                          ),
                5 =>
                    array (
                            'id' => '5',
                            'name' => 'Metal',
                            'identifier' => '1102',
                            'priority' => '6',
                          ),
                6 =>
                    array (
                            'id' => '6',
                            'name' => 'Hip Hop',
                            'identifier' => '1104',
                            'priority' => '7',
                          ),
                7 =>
                    array (
                            'id' => '7',
                            'name' => 'Jazz',
                            'identifier' => '1105',
                            'priority' => '8',
                          ),
                8 =>
                    array (
                            'id' => '10',
                            'name' => 'Pop',
                            'identifier' => '1108',
                            'priority' => '9',
                          ),
                9 =>
                    array (
                            'id' => '14',
                            'name' => 'Punk',
                            'identifier' => '1109',
                            'priority' => '10',
                          ),
                10 =>
                    array (
                            'id' => '8',
                            'name' => 'Reggae',
                            'identifier' => '1110',
                            'priority' => '11',
                          ),
                11 =>
                    array (
                            'id' => '9',
                            'name' => 'Rock',
                            'identifier' => '1111',
                            'priority' => '12',
                          ),
                12 =>
                    array (
                            'id' => '11',
                            'name' => 'Techno',
                            'identifier' => '1112',
                            'priority' => '13',
                          ),
                13 =>
                    array (
                            'id' => '12',
                            'name' => 'World',
                            'identifier' => '1113',
                            'priority' => '14',
                          ),
                14 =>
                    array (
                            'id' => '13',
                            'name' => 'Easy',
                            'identifier' => '1114',
                            'priority' => '15',
                          ),
                15 =>
                    array (
                            'id' => '16',
                            'name' => 'Schlager',
                            'identifier' => '1115',
                            'priority' => '16',
                          ),
                16 =>
                    array (
                            'id' => '17',
                            'name' => 'Volksmusik',
                            'identifier' => '1116',
                            'priority' => '17',
                          ),
                17 =>
                    array (
                            'id' => '18',
                            'name' => 'Film',
                            'identifier' => '1117',
                            'priority' => '18',
                          ),
                18 =>
                    array (
                            'id' => '19',
                            'name' => 'Volkstümlicher Schlager',
                            'identifier' => '1118',
                            'priority' => '19',
                          ),
         );
    }


    public function testIsValidXml()
    {
        $xml = ymcDatatypeEnhancedSelectionClassContent::optionArrayToXml(
                    $this->options );

        $sXml = simplexml_load_string( $xml );
        $instanceOfSimpleXml = $this->isInstanceOf( 'SimpleXMLElement' );
        $this->assertThat( $sXml, $instanceOfSimpleXml );
    }

    public function testContainsGivenData()
    {
        $xml = ymcDatatypeEnhancedSelectionClassContent::optionArrayToXml(
                    $this->options );

        $this->assertContains( 'Metal', $xml );
        $this->assertContains( 'Volksmusik', $xml );
        $this->assertContains( '18', $xml );
        $this->assertContains( '110', $xml );
    }
}
