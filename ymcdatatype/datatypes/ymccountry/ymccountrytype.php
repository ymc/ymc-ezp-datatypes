<?php
/**
 * File containing the ymcDatatypeCountryType class.
 *
 * Created on: <16-Jan-2006 12:20:00 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Country
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * A content datatype which handles countries.
 *
 * It allows thesuer to select a country from a list predefined
 * in ymccountry.ini.
 *
 * It uses the field data_text in a content object attribute for storing
 * the attribute data with its ISO 3166-3 code.
 * 
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage Country
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeCountryType extends eZDataType
{
    const DATATYPE_STRING = 'ymccountry';

    /**
     * List of available Countries.
     * 
     * @var array
     */
    private $countries = array();

    private $isoMapping = array();

    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                           'Country',
                                           'Datatype name' ),
                             array( 'serialize_supported'  => true,
                                    'object_serialize_map' => array( 'data_text' => 'text' ) ) );
        $this->initialiseForCurrentLanguage();
    }
    
    public function initialiseForCurrentLanguage()
    {
        $countryList = array();
        $countryINI = eZINI::instance( 'ymccountry.ini');

        $this->isoMapping = $countryINI->variable( 'Default', 'ISOmapping' );

        foreach ( $countryINI->variable( 'Default', 'Countries' ) as $key => $value )
        {
            $countryList[$key] = ezi18n( 'kernel/content/datatypes/ymccountry', $value );
        }
        natcasesort($countryList);
        $this->countries = $countryList;
    }

    public function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $CountryINI = eZINI::instance( 'ymccountry.ini');
            $default = $CountryINI->variable( 'Default', 'DefaultCountry' );
            if ( $default !== "" and $default != "false" )
            {
                $contentObjectAttribute->setAttribute( "data_text", $default );
            }
        }
    }

    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $field = $base . '_ymccountry_iso_3166_3_' . $contentObjectAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $field ) )
        {
            $data = $http->postVariable( $field );
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                if ( $data == "" )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            elseif ( $data == "" )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
            if ( in_array($data, array_keys($this->countries)) )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
            $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                 'Input required.' ) );
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_INVALID;
    }

    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $field = $base . '_ymccountry_iso_3166_3_' . $contentObjectAttribute->attribute( 'id' );

        if ( $http->hasPostVariable( $field ) )
        {
            $contentObjectAttribute->setAttribute( 'data_text', $http->postVariable( $field ) );
            return true;
        }
        return false;
    }

     
    /**
     * Builds an array with the ISO 3166-3 code and a human readable translation of it.
     *
     * @param mixed $contentObjectAttribute No comment.
     *
     * @return array
     */
    public function countryInformation( $contentObjectAttribute )
    {
        $iso_3166_3 = $contentObjectAttribute->attribute( 'data_text' );
        return array( 'iso-3166-3' => $iso_3166_3,
                      'iso-3166-2' => $this->isoMapping[$iso_3166_3],
                      'name'       => $this->countries[$iso_3166_3],
                      'available_countries' => $this->countries );
    }

    public function objectAttributeContent( $contentObjectAttribute )
    {
        return $this->countryInformation( $contentObjectAttribute );
    }

    public function metaData( $contentObjectAttribute )
    {
        $data = $this->countryInformation( $contentObjectAttribute );
        return $data['name'];
    }

    public function title( $contentObjectAttribute, $name = null )
    {
        $data = $this->countryInformation( $contentObjectAttribute );
        return $data['name'];
    }

    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        $data = $this->countryInformation( $contentObjectAttribute );
        return trim( $data['name'] ) != '';
    }

    public function isIndexable()
    {
        return true;
    }

    public function sortKey( $contentObjectAttribute )
    {
        $trans = eZCharTransform::instance();
        $data = $this->countryInformation( $contentObjectAttribute );
        return $trans->transformByGroup( $data['name'], 'lowercase' );
    }

    public function sortKeyType()
    {
        return 'string';
    }
}
eZDataType::register( ymcDatatypeCountryType::DATATYPE_STRING, 'ymcDatatypeCountryType' );
?>
