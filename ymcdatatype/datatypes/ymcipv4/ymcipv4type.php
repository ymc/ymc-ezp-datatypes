<?php
/**
 * File containing the ymcDatatypeIpv4Type class.
 *
 * Created on: <12-Dez-2005 12:25:30 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Ipv4
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * A content datatype which handles an ip v4 address.
 *
 * It uses the field data_text in a content object attribute for storing
 * the attribute data.
 * 
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage Ipv4
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeIpv4Type extends eZDataType
{
    const DATATYPE_STRING = "ezipv4";

    /**
     * Validator.
     * 
     * @var eZIntegerValidator
     */
    private $IntegerValidator;

    /**
     * Ctor.
     *
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                           "IP v4",
                                           'Datatype name' ),
                             array( 'serialize_supported' => true,
                                    'object_serialize_map' => array( 'data_text' => 'value' ),
                                    "translation_allowed" => false ) );
        $this->IntegerValidator = new eZIntegerValidator();
    }

    /**
     * Validates the input from the object edit form concerning this attribute.
     *
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return int eZInputValidator::STATE_INVALID/STATE_ACCEPTED
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_ipv4_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_ipv4_" . $contentObjectAttribute->attribute( "id" ) );
            $data = str_replace(" ", "", $data );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            
            if( !$contentObjectAttribute->validateIsRequired() && ( $data == "" ) )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
            
            $ipParts = explode( ".", $data );
            if (count($ipParts) !== 4)
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'No valid IP v4 address' ) );
                return eZInputValidator::STATE_INVALID;
            }
            
            $this->IntegerValidator->setRange( 0, 255 );
            foreach ( $ipParts as $ipPart )
            {
                $state = $this->IntegerValidator->validate( $ipPart );
                if( $state !== 1 )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'No valid IP v4 address' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            return eZInputValidator::STATE_ACCEPTED;
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_INVALID;
    }

    /**
     * Stores the object attribute input in the $contentObjectAttribute.
     *
     * Fetches the http post var ipv4 input and stores it in the data instance.
     *
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean Whether to save the changes to the db or not.
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_ipv4_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_ipv4_" . $contentObjectAttribute->attribute( "id" ) );
            $contentObjectAttribute->setAttribute( "data_text", $data );
            return true;
        }
        return false;
    }

    /**
     * Returns the content object of the attribute.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /**
     * Returns the meta data used for storing search indeces.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /**
     * Returns a string that could be used for the object title.
     *
     * Returns the ipv4 value.
     *
     * @param mixed $contentObjectAttribute ContentObjectAttribute.
     * @param mixed $name                   No idea...
     *
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_text" );
    }

    /**
     * Returns whether the attribute contains data.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean
     */
    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( "data_text" ) ) != '';
    }

    /**
     * IsIndexable.
     *
     * @return boolean
     */
    public function isIndexable()
    {
        return true;
    }

    /**
     * Returns a key to sort attributes.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function sortKey( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /**
     * Returns the type of the sortKey.
     *
     * @return string
     */
    public function sortKeyType()
    {
        return 'string';
    }
}
eZDataType::register( ymcDatatypeIpv4Type::DATATYPE_STRING, "ymcDatatypeIpv4Type" );
?>
