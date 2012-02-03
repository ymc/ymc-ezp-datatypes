<?php
/**
 * File containing the ymcDatatypeNumberAsStringType class.
 *
 * Created on: <01-Apr-2007 14:48:00 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage NumberAsString
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * A content datatype which handles numbers as text lines.
 *
 * It uses the spare field data_text in a content object attribute for storing
 * the attribute data.
 *
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage NumberAsString
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeNumberAsStringType extends eZDataType
{
    const DATATYPE_STRING         = 'ymcnumberasstring';
    const MAX_LEN_FIELD           = 'data_int1';
    const MAX_LEN_VARIABLE        = '_ymcnumberasstring_max_string_length_';
    const DEFAULT_STRING_FIELD    = "data_text1";
    const DEFAULT_STRING_VARIABLE = "_ymcnumberasstring_default_value_";

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
                                     'Number as string',
                                     'Datatype name' ),
                             array( 'serialize_supported' => false ) );
        $this->IntegerValidator = new eZIntegerValidator();
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
        if ( $http->hasPostVariable( $base . '_ymcnumberasstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ymcnumberasstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else
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
                
                $this->IntegerValidator->setRange( false, false );
                $state = $this->IntegerValidator->validate( $data );
                if( $state === eZInputValidator::STATE_INVALID || $state === eZInputValidator::STATE_INTERMEDIATE )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'The input is not a valid integer.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                return eZInputValidator::STATE_ACCEPTED;
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
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
        if ( $http->hasPostVariable( $base . '_ymcnumberasstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ymcnumberasstring_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $contentObjectAttribute->setAttribute( 'data_text', $data );
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

    /**
     * Validates the MaxLenInput.
     * 
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_...
     */
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
                $this->IntegerValidator->setRange( 1, false );
                return $this->IntegerValidator->validate( $maxLenValue );
            }
        }
        return eZInputValidator::STATE_INVALID;
    }

    /**
     * Fixes broken UserInput.
     * 
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return void
     */
    public function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $this->IntegerValidator->setRange( 1, false );
            $maxLenValue = $this->IntegerValidator->fixup( $maxLenValue );
            $http->setPostVariable( $maxLenName, $maxLenValue );
        }
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

    /**
     * Returns the content.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        return $contentObjectAttribute->attribute( 'data_text' );
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
        return $contentObjectAttribute->attribute( 'data_text' );
    }

    /**
     * Returns the content of the string for use as a title.
     * 
     * @param mixed $objectAttribute Class eZContentObjectAttribute.
     * @param mixed $name            Seems to have no functionality.
     *
     * @return string
     */
    public function title( $objectAttribute, $name = null )
    {
        return $objectAttribute->attribute( 'data_text' );
    }

    /**
     * Whether the object has content.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean
     */
    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( 'data_text' ) ) != '';
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
     * IsInformationCollector.
     * 
     * @return boolean
     */
    public function isInformationCollector()
    {
        return false;
    }

    /**
     * SortKey.
     * 
     * @param mixed $contentObjectAttribute No comment.
     *
     * @return string
     */
    public function sortKey( $contentObjectAttribute )
    {
        include_once( 'lib/ezi18n/classes/ezchartransform.php' );
        $trans = eZCharTransform::instance();
        return $trans->transformByGroup( $contentObjectAttribute->attribute( 'data_text' ), 'lowercase' );
    }

    /**
     * SortKeyType.
     * 
     * @return string
     */
    public function sortKeyType()
    {
        return 'string';
    }

    /**
     * Returns a diff object.
     * 
     * @param mixed $old     Some data can be text/xml/....
     * @param mixed $new     Some data.
     * @param mixed $options Some options. No idea...
     *
     * @return eZDiffEngine or extending class.
     */
    public function diff( $old, $new, $options = false )
    {
        include_once( 'lib/ezdiff/classes/ezdiff.php' );
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $diffObject = $diff->diff( $old->content(), $new->content() );
        return $diffObject;
    }

}
eZDataType::register( ymcDatatypeNumberAsStringType::DATATYPE_STRING, 'ymcDatatypeNumberAsStringType' );
?>
