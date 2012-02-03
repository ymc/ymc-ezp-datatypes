<?php
/**
 * File containing the ymcDatatypeDomain class.
 *
 * Created on: <32-Dez-2005 13:02:44 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Domain
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
 * @subpackage Domain
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeDomain extends eZDataType
{
    const DATATYPE_STRING    = "ymcdomain";
    const SUBDOMAIN_FIELD    = "data_int1";
    const SUBDOMAIN_VARIABLE = "_ezdomain_with_subdomain_value_";
    const SUBDOMAIN_SWITCH   = "_ezdomain_with_subdomain_switch_";
    const DOMAIN_REGEXP      = '[0-9a-z]*\.[a-z]{2,4}';
    const SUBDOMAIN_REGEXP   = '[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}';

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
                                           "Domain",
                                           'Datatype name' ),
                              array( 'serialize_supported'  => true,
                                     'object_serialize_map' => array( 'data_text' => 'value' ),
                                     "translation_allowed"  => false ) );
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
        if ( $http->hasPostVariable( $base . "_data_domain_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_domain_" . $contentObjectAttribute->attribute( "id" ) );
            $data = str_replace(" ", "", $data );
            
            if( !$contentObjectAttribute->validateIsRequired() && ( $data == "" ) )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }

            $withSubdomain = $contentObjectAttribute->contentClassAttribute()->attribute( self::SUBDOMAIN_FIELD );
            
            if ( $withSubdomain == 1 )
            {
                if ( !ereg( '^' . self::SUBDOMAIN_REGEXP . '$', $data) )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'No valid domain' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else
            {
                if ( !ereg( '^' . self::DOMAIN_REGEXP . '$', $data) )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'No valid domain' ) );
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
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean Whether to save the changes to the db or not.
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_data_domain_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_domain_" . $contentObjectAttribute->attribute( "id" ) );
            $contentObjectAttribute->setAttribute( "data_text", $data );
            return true;
        }
        return false;
    }

    /**
     * Handles the input specific for one attribute from the class edit interface.
     *
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return boolean
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $withSubDomainName = $base . self::SUBDOMAIN_VARIABLE . $classAttribute->attribute( "id" );
        $withSubDomainSwitch = $base . self::SUBDOMAIN_SWITCH . $classAttribute->attribute( "id" );
        
        if ( $http->hasPostVariable( $withSubDomainSwitch ) )
        {
            if ( $http->hasPostVariable( $withSubDomainName ) )
            {
                $withSubDomainValue = $http->postVariable( $withSubDomainName );
                
                $classAttribute->setAttribute( self::SUBDOMAIN_FIELD, $withSubDomainValue );
            }
            else
            {
                $classAttribute->setAttribute( self::SUBDOMAIN_FIELD, 0 );
            }
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

    public function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $withSubDomainValue = $classAttribute->attribute( self::SUBDOMAIN_FIELD );
        
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'with-subdomain-value', $withSubDomainValue ) );
    }

    public function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $withSubDomainValue = $attributeParametersNode->elementTextContentByName( 'with-subdomain-value' );

        $classAttribute->setAttribute( self::SUBDOMAIN_FIELD, $withSubDomainValue );
    }
}
eZDataType::register( ymcDatatypeDomain::DATATYPE_STRING, "ymcDatatypeDomain" );
?>
