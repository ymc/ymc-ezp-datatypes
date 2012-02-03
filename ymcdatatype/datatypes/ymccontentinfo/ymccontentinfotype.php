<?php
/**
 * File containing the ymcDatatypeContentInfoType class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage ContentInfo
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Displays a predefined static text in the object edit dialog.
 *
 * The static text is given in the class edit dialog.
 * 
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage ContentInfo
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeContentInfoType extends eZDataType
{
    const DATATYPE_STRING = "ymccontentinfo";

    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                         "Info Element",
                                         'Datatype name' ) );
    }

    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        return true;
    }

    public function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->contentClassAttribute()->attribute( "data_text5" );
    }

    public function classAttributeContent( $classAttribute )
    {
        return $classAttribute->attribute( "data_text5" );
    }


    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
       if ( $http->hasPostVariable( $base . "_data_text_" . $classAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_text_" . $classAttribute->attribute( "id" ) );
            $classAttribute->setAttribute( "data_text5", $data );
            return true;
        }
        return false;
    }

    public function metaData( $contentObjectAttribute )
    {   
        return $contentObjectAttribute->contentClassAttribute()->attribute( "data_text5" );
    }

    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute
                      ->contentClassAttribute()
                      ->attribute( "data_text5" ) 
                   ) != '';
    }

    public function title( $data_instance, $name = null )
    {
        return $data_instance->attribute( "data_text" );
    }

    public function isIndexable()
    {
        return false;
    }

    public function isInformationCollector()
    {
        return false;
    }
}
eZDataType::register( ymcDatatypeContentInfoType::DATATYPE_STRING, "ymcDatatypeContentInfoType" );
?>
