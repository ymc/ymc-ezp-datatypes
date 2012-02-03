<?php
/**
 * File containing the ymcDatatypeUniqueStringType class.
 *
 * Created on: <19-Dez-2007 13:09:00 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage UniqueString
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Copy of eZStringType, but for strings that are unique across all objects.
 *
 * Please refer to eZStringType for more documentation and to the docblock of 
 * self::validateUniqueStringHTTPInput().
 *
 * This class has been copied from eZStringType and adapted instead of
 * extending eZStringType, out of three reasons:
 *
 * - The ctor of eZStringType had to be avoided and so ymcUniqueString called
 *   the ctor of eZDataType directly without calling the ctor of eZStringType.
 *   This is evil!
 *
 * - Extending is slow.
 *
 * - Copying the class and adapting it was fast done with VIM. ;-)
 * 
 * @uses       eZStringType
 * @package    ymcDatatype
 * @subpackage UniqueString
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeUniqueStringType extends eZDataType
{
    const DATATYPE_STRING         = 'ymcuniquestring';
    const MAX_LEN_FIELD           = 'data_int1';
    const MAX_LEN_VARIABLE        = '_ezstring_max_string_length_';
    const DEFAULT_STRING_FIELD    = "data_text1";
    const DEFAULT_STRING_VARIABLE = "_ezstring_default_value_";

    /**
     * MaxLenValidator.
     * 
     * @var eZIntegerValidator
     */
    private $MaxLenValidator;

    /**
     * Initializes with a string id and a description.
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                      'Unique String',
                                      'Datatype name' ),
                              array( 'serialize_supported' => true,
                                     'object_serialize_map' => array( 'data_text' => 'text' ) ) );
        $this->MaxLenValidator = new eZIntegerValidator();
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
            $default = $contentClassAttribute->attribute( "data_text1" );
            if ( $default !== "" )
            {
                $contentObjectAttribute->setAttribute( "data_text", $default );
            }
        }
    }

    /**
     * Validates stringlength and forwards to validateUniqueStringHTTPInput.
     *
     * Called from
     *
     * - validateCollectionAttributeHTTPInput
     * - validateObjectAttributeHTTPInput
     *
     * Private method, only for using inside this class.
     * 
     * @param string $data                   The string to validate.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     * @param mixed  $classAttribute         Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_...
     */
    private function validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute )
    {
        $maxLen = $classAttribute->attribute( self::MAX_LEN_FIELD );
        $textCodec = eZTextCodec::instance( false );
        if ( $textCodec->strlen( $data ) > $maxLen and
             $maxLen > 0 )
        {
            $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                 'The input text is too long. The maximum number of characters allowed is %1.' ),
                                                         $maxLen );
            return eZInputValidator::STATE_INVALID;
        }
        return self::validateUniqueStringHTTPInput( $data, $contentObjectAttribute );
    }

    /**
     * Validates the input from the object edit form concerning this attribute.
     * 
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return int eZInputValidator::STATE_...
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( !$classAttribute->attribute( 'is_information_collector' ) and
                     $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else
            {
                return $this->validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute );
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    public function validateCollectionAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                {
                    return eZInputValidator::STATE_ACCEPTED;
                }
            }
            else
            {
                return $this->validateStringHTTPInput( $data, $contentObjectAttribute, $classAttribute );
            }
        }
        else
        {
            return eZInputValidator::STATE_INVALID;
        }
    }

    /**
     * Fetches the http post var string input and stores it in the data instance.
     *
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ezstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $contentObjectAttribute->setAttribute( 'data_text', $data );
            return true;
        }
        return false;
    }

    public function fetchCollectionAttributeHTTPInput( $collection, $collectionAttribute, $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . "_ezstring_data_text_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $dataText = $http->postVariable( $base . "_ezstring_data_text_" . $contentObjectAttribute->attribute( "id" ) );
            $collectionAttribute->setAttribute( 'data_text', $dataText );
            return true;
        }
        return false;
    }

    /**
     * Does nothing since it uses the data_text field in the content object attribute.
     *
     * See fetchObjectAttributeHTTPInput for the actual storing.
     * 
     * @param mixed $attribute Class eZContentObjectAttribute.
     *
     * @return void
     */
    public function storeObjectAttribute( $attribute )
    {
    }

    public function isSimpleStringInsertionSupported()
    {
        return true;
    }

    public function insertSimpleString( $object, $objectVersion, $objectLanguage, $objectAttribute, $string, &$result )
    {
        $result = array( 'errors' => array(),
                         'require_storage' => true );
        $objectAttribute->setContent( $string );
        $objectAttribute->setAttribute( 'data_text', $string );
        return true;
    }

    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $maxLenValue = str_replace(" ", "", $maxLenValue );
            if( ( $maxLenValue == "" ) ||  ( $maxLenValue == 0 ) )
            {
                $maxLenValue = 0;
                $http->setPostVariable( $maxLenName, $maxLenValue );
                return eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                $this->MaxLenValidator->setRange( 1, false );
                return $this->MaxLenValidator->validate( $maxLenValue );
            }
        }
        return eZInputValidator::STATE_INVALID;
    }

    public function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $this->MaxLenValidator->setRange( 1, false );
            $maxLenValue = $this->MaxLenValidator->fixup( $maxLenValue );
            $http->setPostVariable( $maxLenName, $maxLenValue );
        }
    }

    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        $defaultValueName = $base . self::DEFAULT_STRING_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $classAttribute->setAttribute( self::MAX_LEN_FIELD, $maxLenValue );
        }
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            $classAttribute->setAttribute( self::DEFAULT_STRING_FIELD, $defaultValueValue );
        }
        return true;
    }
    public function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }
    public function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function toString( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /**
     * Writes string in the correct place in contentObjectAttribute.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     * @param mixed $string                 String to write.
     *
     * @return void
     */
    public function fromString( $contentObjectAttribute, $string )
    {
        $contentObjectAttribute->setAttribute( 'data_text', $string );
    }

    /**
     * Returns the content of the string for use as a title.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     * @param mixed $name                   Seems to have no functionality.
     *
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }
    public function isIndexable()
    {
        return true;
    }
    public function isInformationCollector()
    {
        return true;
    }
    public function sortKey( $contentObjectAttribute )
    {
        return eZCharTransform::instance()
               ->transformByGroup( $contentObjectAttribute
                                     ->attribute( 'data_text' ),
                                   'lowercase' );
    }
    public function sortKeyType()
    {
        return 'string';
    }
    public function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $maxLength = $classAttribute->attribute( self::MAX_LEN_FIELD );
        $defaultString = $classAttribute->attribute( self::DEFAULT_STRING_FIELD );
        $dom = $attributeParametersNode->ownerDocument;
        $maxLengthNode = $dom->createElement( 'max-length', $maxLength );
        $attributeParametersNode->appendChild( $maxLengthNode );
        if ( $defaultString )
        {
            $defaultStringNode = $dom->createElement( 'default-string', $defaultString );
        }
        else
        {
            $defaultStringNode = $dom->createElement( 'default-string' );
        }
        $attributeParametersNode->appendChild( $defaultStringNode );
    }
    public function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $maxLength = $attributeParametersNode->getElementsByTagName( 'max-length' )->item( 0 )->textContent;
        $defaultString = $attributeParametersNode->getElementsByTagName( 'default-string' )->item( 0 )->textContent;
        $classAttribute->setAttribute( self::MAX_LEN_FIELD, $maxLength );
        $classAttribute->setAttribute( self::DEFAULT_STRING_FIELD, $defaultString );
    }
    public function diff( $old, $new, $options = false )
    {
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $diffObject = $diff->diff( $old->content(), $new->content() );
        return $diffObject;
    }

    /**
     * Checks, whether a string is unique.
     *
     * This method checks if given string does exist in any content object
     * attributes of the same id, with the exception for those being versions
     * of the same content object. If given string exists anywhere, in published
     * or unpublished versions, drafts, trash, this string will be excluded.
     * Same contentobject is not check to make it possible to make another
     * versions of the same object, multiple occurances should not matter as
     * long as other objects cannot access the same name.
     * 
     * @param mixed $data                   The string to check for uniqueness.
     * @param mixed $contentObjectAttribute This attribute.
     *
     * @return int eZInputValidator::STATE_...
     */
    private function validateUniqueStringHTTPInput( $data, $contentObjectAttribute )
    {
        $contentObjectID = $contentObjectAttribute->ContentObjectID;
        $contentClassAttributeID = $contentObjectAttribute->ContentClassAttributeID;
        $db = eZDB::instance();
        
        $query = "SELECT COUNT(*) AS datacounter
            FROM ezcontentobject_attribute 
            WHERE contentobject_id <> ".$db->escapeString( $contentObjectID )." 
            AND contentclassattribute_id = ".$db->escapeString( $contentClassAttributeID )."  
            AND data_text = '".$db->escapeString( $data )."' ";
        $result = $db->arrayQuery( $query );
        $resultCount = $result[0]['datacounter'];
        
        if( $resultCount )
        {
            $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes', 'Given string already exists in another object of this class in the same attribute.' ) );
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }
}

eZDataType::register( ymcDatatypeUniqueStringType::DATATYPE_STRING, "ymcDatatypeUniqueStringType" );

?>
