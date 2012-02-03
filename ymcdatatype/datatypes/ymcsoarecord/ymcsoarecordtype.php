<?php
/**
 * File containing the ymcDatatypeSoaRecordType class.
 *
 * Created on: <12-Dez-2005 12:25:30 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage SoaRecord
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * 
 * 
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage SoaRecord
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeSoaRecordType extends eZDataType
{
    const DATATYPE_STRING         = "ymcsoarecord";
    const NS_VARIABLE             = "_soarecord_ns_";
    const EMAIL_VARIABLE          = "_soarecord_email_";
    const REFRESH_PERIOD_VARIABLE = "_soarecord_refresh_period_";
    const RETRY_INTERVAL_VARIABLE = "_soarecord_retry_interval_";
    const EXPIRE_TIME_VARIABLE    = "_soarecord_expire_time_";
    const DEFAULT_TTL_VARIABLE    = "_soarecord_default_ttl_";

    /**
     * Validator.
     * 
     * @var eZIntegerValidator
     */
    private $IntegerValidator;

    /**
     * Ctor.
     */
    public function __construct()
    {
        $this->eZDataType( self::DATATYPE_STRING,
                           'ymc'.ezi18n( 'kernel/classes/datatypes',
                                         "SOA Record",
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
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $nameserver = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $email = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $refresh_period = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $retry_interval = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $expire_time = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $default_ttl = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            
            $tmp_data = $nameserver.$email.$refresh_period.$retry_interval.$expire_time.$default_ttl;
            
            
            if( !$contentObjectAttribute->validateIsRequired() && ( $tmp_data == "" ) )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }
            
            $this->IntegerValidator->setRange( 0, 9999999 );
            
            
            if ( !ereg( '^[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$', $nameserver) )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The nameserver is not valid' ) );
                return eZInputValidator::STATE_INVALID;
            }
            
            
            if ( trim( $email ) == "" )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The email address is empty.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            $vaild_mail = eZMail::validate( trim( $email ) );
            if ( !$vaild_mail )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The email address is not valid.' ) );
                return eZInputValidator::STATE_INVALID;
            }
            
            
            if( $this->IntegerValidator->validate( $refresh_period ) !== 1 )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The refresh period is not valid' ) );
                return eZInputValidator::STATE_INVALID;
            }

            if( $this->IntegerValidator->validate( $retry_interval ) !== 1 )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The retry interval is not valid' ) );
                return eZInputValidator::STATE_INVALID;
            }
            
            if( $this->IntegerValidator->validate( $expire_time ) !== 1 )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The expire time is not valid' ) );
                return eZInputValidator::STATE_INVALID;
            }
            
            if( $this->IntegerValidator->validate( $default_ttl ) !== 1 )
            {
                $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                     'The default ttl is not valid' ) );
                return eZInputValidator::STATE_INVALID;
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
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) and
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $generate_serial = true;
            $nameserver = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $email = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $refresh_period = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $retry_interval = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $expire_time = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            $default_ttl = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $contentObjectAttribute->attribute( "id" ) );
            
            $contentObject = eZContentObject::fetch( $contentObjectAttribute->attribute('contentobject_id') );
            
            if ( is_object($contentObject) )
            {
                $data_map = $contentObject->attribute('data_map');
                
                if ( count($data_map) > 0 )
                {
                    $classAttribute = $contentObjectAttribute->contentClassAttribute();
                    $identifier = $classAttribute->attribute('identifier');
                    $publishedAttribute = $data_map[$identifier];
                    
                    if ( is_object($publishedAttribute) )
                    {
                        $data_array = explode( " ", $publishedAttribute->attribute('data_text') );
                        $serial = $data_array[2];
                        
                        if ( $serial != "" )
                        {
                            $serial_date = substr( $serial, 0, 8 );
                            $serial_count = substr( $serial, 8, 2 );
                            settype( $serial_count, "integer" );
                            
                            if ( date("Ymd") == $serial_date )
                            {
                                $generate_serial = false;
                                
                                if ( $serial_count < 99 )
                                {
                                    $serial_count++;
                                }
                                else
                                {
                                    $serial_count = 0;
                                }
                            }
                        }
                    }
                }
            }
            
            if ( $generate_serial )
            {
                $serial_date = date("Ymd");
                $serial_count = 0;
            }
            
            if ( $serial_count < 10 )
            {
                $serial_count = "0".$serial_count;
            }
            
            $serial = $serial_date.$serial_count;
            
            $data = $nameserver." ".$email." ".$serial." ".$refresh_period." ".$retry_interval." ".$expire_time." ".$default_ttl;
            
            $tmp_data = $nameserver.$email.$refresh_period.$retry_interval.$expire_time.$default_ttl;
            
            if ( $tmp_data != "" )
            {
                $contentObjectAttribute->setAttribute( "data_text", $data );
            }
            else
            {
                $contentObjectAttribute->setAttribute( "data_text", "" );
            }
            
            return true;
        }
        return false;
    }

    /**
     * Sets the default value.
     * 
     * @param mixed $contentObjectAttribute         Class eZContentObjectAttribute.
     * @param mixed $currentVersion                 Should be NULL on initial obj creation.
     * @param mixed $originalContentObjectAttribute Class eZContentObjectAttribute.         
     *
     * @return void
     */
    public function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            
            $nameserver = $contentClassAttribute->attribute( "data_text1" );
            $email = $contentClassAttribute->attribute( "data_text2" );
            $refresh_period = $contentClassAttribute->attribute( "data_int1" );
            $retry_interval = $contentClassAttribute->attribute( "data_int2" );
            $expire_time = $contentClassAttribute->attribute( "data_int3" );
            $default_ttl = $contentClassAttribute->attribute( "data_int4" );
            $serial = date("Ymd")."00";
            
            $default = $nameserver." ".$email." ".$serial." ".$refresh_period." ".$retry_interval." ".$expire_time." ".$default_ttl;
            
            $contentObjectAttribute->setAttribute( "data_text", $default );
        }
    }

    /**
     * 
     * 
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_...
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $this->IntegerValidator->setRange( 0, 9999999 );
        
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            $nameserver = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $classAttribute->attribute( "id" ) );
            if ( $nameserver != "" )
            {
                if ( !ereg( '^[0-9a-z]([-.]?[0-9a-z])*\.[a-z]{2,4}$', $nameserver) )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            $email = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $classAttribute->attribute( "id" ) );
            if ( $email != "" )
            {
                if ( trim( $email ) == "" )
                {
                    return eZInputValidator::STATE_INVALID;
                }
                
                $vaild_mail = eZMail::validate( trim( $email ) );
                if ( !$vaild_mail )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            $refresh_period = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $classAttribute->attribute( "id" ) );
            
            if ( $refresh_period != "" )
            {
                if( $this->IntegerValidator->validate( $refresh_period ) !== 1 )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            $retry_interval = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $classAttribute->attribute( "id" ) );
            
            if ( $retry_interval != "" )
            {
                if( $this->IntegerValidator->validate( $retry_interval ) !== 1 )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            $expire_time = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $classAttribute->attribute( "id" ) );
            
            if ( $expire_time != "" )
            {
                if( $this->IntegerValidator->validate( $expire_time ) !== 1 )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            $default_ttl = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $classAttribute->attribute( "id" ) );
            
            if ( $default_ttl != "" )
            {
                if( $this->IntegerValidator->validate( $default_ttl ) !== 1 )
                {
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }
        
        return eZInputValidator::STATE_ACCEPTED;
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
        if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $classAttribute->attribute( "id" ) ) or
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $classAttribute->attribute( "id" ) ) or
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $classAttribute->attribute( "id" ) ) or
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $classAttribute->attribute( "id" ) ) or
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $classAttribute->attribute( "id" ) ) or
             $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $classAttribute->attribute( "id" ) ) )
        {
            if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $classAttribute->attribute( "id" ) ) )
            {
                $nameserver = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_NS_VARIABLE . $classAttribute->attribute( "id" ) );
                $classAttribute->setAttribute( "data_text1", $nameserver );
            }
            
            if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $classAttribute->attribute( "id" ) ) )
            {
                $email = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EMAIL_VARIABLE . $classAttribute->attribute( "id" ) );
                $classAttribute->setAttribute( "data_text2", $email );
            }
            
            if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $classAttribute->attribute( "id" ) ) )
            {
                $refresh_period = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_REFRESH_PERIOD_VARIABLE . $classAttribute->attribute( "id" ) );
                $classAttribute->setAttribute( "data_int1", $refresh_period );
            }
            
            if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $classAttribute->attribute( "id" ) ) )
            {
                $retry_interval = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_RETRY_INTERVAL_VARIABLE . $classAttribute->attribute( "id" ) );
                $classAttribute->setAttribute( "data_int2", $retry_interval );
            }
            
            if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $classAttribute->attribute( "id" ) ) )
            {
                $expire_time = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_EXPIRE_TIME_VARIABLE . $classAttribute->attribute( "id" ) );
                $classAttribute->setAttribute( "data_int3", $expire_time );
            }
            
            if ( $http->hasPostVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $classAttribute->attribute( "id" ) ) )
            {
                $default_ttl = $http->postVariable( $base . EZ_DATATYPESTRING_SOA_RECORD_DEFAULT_TTL_VARIABLE . $classAttribute->attribute( "id" ) );
                $classAttribute->setAttribute( "data_int4", $default_ttl );
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
        $data = $contentObjectAttribute->attribute( "data_text" );
        
        $data_array = explode( " ", $data );
        
        if ( count($data_array) == 7 )
        {
            list( $nameserver, $email, $serial, $refresh_period, $retry_interval, $expire_time, $default_ttl ) = explode( " ", $data );
            
            return array( 'soa_string' => $data,
                          'nameserver' => $nameserver,
                          'email' => $email,
                          'serial' => $serial,
                          'refresh_period' => $refresh_period,
                          'retry_interval' => $retry_interval,
                          'expire_time' => $expire_time,
                          'default_ttl' => $default_ttl );
        }
        else
        {
            return null;
        }
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

    /**
     * SerializeContentClassAttribute.
     * 
     * @param mixed $classAttribute          Class eZContentClassAttribute.
     * @param mixed $attributeNode 
     * @param mixed $attributeParametersNode 
     *
     * @return void
     */
    public function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'nameserver-value', $classAttribute->attribute('data_text1') ) );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'email-value', $classAttribute->attribute('data_text2') ) );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'refresh_period-value', $classAttribute->attribute('data_int1') ) );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'retry_interval-value', $classAttribute->attribute('data_int2') ) );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'expire_time-value', $classAttribute->attribute('data_int3') ) );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'default_ttl-value', $classAttribute->attribute('data_int4') ) );
    }

    /**
     * UnserializeContentClassAttribute.
     * 
     * @param mixed $classAttribute          Class eZContentClassAttribute used as in/out parameter.
     * @param mixed $attributeNode 
     * @param mixed $attributeParametersNode 
     *
     * @return void
     */
    public function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $classAttribute->setAttribute( 'data_text1', $attributeParametersNode->elementTextContentByName( 'nameserver-value' ) );
        $classAttribute->setAttribute( 'data_text2', $attributeParametersNode->elementTextContentByName( 'email-value' ) );
        $classAttribute->setAttribute( 'data_int1', $attributeParametersNode->elementTextContentByName( 'refresh_period-value' ) );
        $classAttribute->setAttribute( 'data_int2', $attributeParametersNode->elementTextContentByName( 'retry_interval-value' ) );
        $classAttribute->setAttribute( 'data_int3', $attributeParametersNode->elementTextContentByName( 'expire_time-value' ) );
        $classAttribute->setAttribute( 'data_int4', $attributeParametersNode->elementTextContentByName( 'default_ttl-value' ) );
    }
}
eZDataType::register( ymcDatatypeSoaRecordType::DATATYPE_STRING, "ymcDatatypeSoaRecordType" );
?>
