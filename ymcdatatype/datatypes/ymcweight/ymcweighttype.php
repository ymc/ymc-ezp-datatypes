<?php
/**
 * File containing the ymcDatatypeWeight class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Weight
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * 
 * Mostly equal to ymcvolume.
 *
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage Weight
 * @author     ymc-dabe
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */
class ymcDatatypeWeightType extends eZDataType
{
    const DATATYPE_STRING       = "ymcweight";
    const MIN_FIELD             = "data_float1";
    const MIN_VARIABLE          = "_ymcweight_min_weight_value_";
    const MAX_FIELD             = "data_float2";
    const MAX_VARIABLE          = "_ymcweight_max_weight_value_";
    const DEFAULT_FIELD         = "data_float3";
    const DEFAULT_VARIABLE      = "_ymcweight_default_value_";
    const INPUT_STATE_FIELD     = "data_float4";
    const SHOW_IN_VIEW_FIELD    = "data_int1";
    const SHOW_IN_VIEW_VARIABLE = "_ymcweight_show_in_view_";

    const STATUS_NO_MIN_MAX_VALUE   = 0;
    const STATUS_HAS_MIN_VALUE      = 1;
    const STATUS_HAS_MAX_VALUE      = 2;
    const STATUS_HAS_MIN_MAX_VALUE  = 3;

    /**
     * FloatValidator.
     * 
     * @var eZFloatValidator
     */
    private $FloatValidator;

    /**
     * Initializes with a string id and a description.
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                           "Weight",
                                           'Datatype name' ),
                             array( 'serialize_supported' => true,
                                    'object_serialize_map' => array( 'data_float' => 'value' ) ) );
        $this->FloatValidator = new eZFloatValidator();
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
//             $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
//             $currentObjectAttribute = eZContentObjectAttribute::fetch( $contentObjectAttributeID,
//                                                                         $currentVersion );
            $dataFloat = $originalContentObjectAttribute->attribute( "data_float" );
            $contentObjectAttribute->setAttribute( "data_float", $dataFloat );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $default = $contentClassAttribute->attribute( "data_float3" );
            if ( $default !== 0 )
            {
                $contentObjectAttribute->setAttribute( "data_float", $default );
            }
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
        if ( $http->hasPostVariable( $base . "_data_float_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_float_" . $contentObjectAttribute->attribute( "id" ) );
            $contentObjectAttribute->setHTTPValue( $data );

            $locale = eZLocale::instance();
            $data = $locale->internalNumber( $data );

            if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "lb" )
            {
                //Pound (lb) -> kg
                $exact_data = $data * 0.45359237;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "Kt" )
            {
                //Karat (metr., Kt) -> kg
                $exact_data = $data * 2.0000E-4;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "dr" )
            {
                //Dram (dr) -> kg
                $exact_data = $data * 0.001771845;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "oz" )
            {
                //ounce (oz) -> kg
                $exact_data = $data * 0.02834952;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "cwt" )
            {
                //hundredweight (cwt) -> kg
                $exact_data = $data * 50.80235;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "scwt" )
            {
                //short cwt -> kg
                $exact_data = $data * 45.35924;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "ston" )
            {
                //short ton -> kg
                $exact_data = $data * 907.1847;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "lton" )
            {
                //long ton -> kg
                $exact_data = $data * 1016.0470;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "mg" )
            {
                //Milligramm (mg) -> kg
                $exact_data = $data * 0.000001;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "g" )
            {
                //Gramm (g) -> kg
                $exact_data = $data * 0.001;
            }
            else if ( $http->postVariable( $base . "_unit_" . $contentObjectAttribute->attribute( "id" ) ) == "t" )
            {
                //Tonne (t) -> kg
                $exact_data = $data * 1000;
            }
            else
            {
                //bereits kg
                $exact_data = $data * 1;
            }
            
            $contentObjectAttribute->setAttribute( "data_float", $exact_data );
            return true;
        }
        return false;
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
        if ( $http->hasPostVariable( $base . "_data_float_" . $contentObjectAttribute->attribute( "id" ) ) )
        {
            $data = $http->postVariable( $base . "_data_float_" . $contentObjectAttribute->attribute( "id" ) );
            $data = str_replace(" ", "", $data );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            $min = $classAttribute->attribute( self::MIN_FIELD );
            $max = $classAttribute->attribute( self::MAX_FIELD );
            $input_state = $classAttribute->attribute( self::INPUT_STATE_FIELD );
            if( ( $classAttribute->attribute( "is_required" ) == false ) &&  ( $data == "" ) )
            {
                return eZInputValidator::STATE_ACCEPTED;
            }

            $locale = eZLocale::instance();
            $data = $locale->internalNumber( $data );

            switch( $input_state )
            {
                case self::STATUS_NO_MIN_MAX_VALUE:
                {
                    $state = $this->FloatValidator->validate( $data );
                    if( $state===1 )
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    else
                    {
                        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                             'The given input is not a floating point number.' ) );
                    }
                }
                break;

                case self::STATUS_HAS_MIN_VALUE:
                {
                    $this->FloatValidator->setRange( $min, false );
                    $state = $this->FloatValidator->validate( $data );
                    if( $state===1 )
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    else
                    {
                        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                             'The input must be greater than %1' ),
                                                                     $min );
                    }
                }
                break;

                case self::STATUS_HAS_MAX_VALUE:
                {
                    $this->FloatValidator->setRange( false, $max );
                    $state = $this->FloatValidator->validate( $data );
                    if( $state===1 )
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    else
                    {
                        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                             'The input must be less than %1' ),
                                                                     $max );
                    }
                }
                break;

                case self::STATUS_HAS_MIN_MAX_VALUE:
                {
                    $this->FloatValidator->setRange( $min, $max );
                    $state = $this->FloatValidator->validate( $data );
                    if( $state===1 )
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    else
                    {
                        $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                             'The input is not in defined range %1 - %2' ),
                                                                     $min, $max );
                    }
                }
                break;
                
                default:
                    // nothing.
                break;
            }
        }
        return eZInputValidator::STATE_INVALID;
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
        $minValueName = $base . self::MIN_VARIABLE . $classAttribute->attribute( "id" );
        $maxValueName = $base . self::MAX_VARIABLE . $classAttribute->attribute( "id" );
        $defaultValueName =  $base . self::DEFAULT_VARIABLE . $classAttribute->attribute( "id" );
        
        $showInViewName = $base . self::SHOW_IN_VIEW_VARIABLE . $classAttribute->attribute( "id" );

        if ( $http->hasPostVariable( $minValueName ) and
             $http->hasPostVariable( $maxValueName ) and
             $http->hasPostVariable( $defaultValueName ) )
        {
            $locale = eZLocale::instance();

            $minValueValue = $http->postVariable( $minValueName );
            $minValueValue = str_replace(" ", "", $minValueValue );
            $minValueValue = $locale->internalNumber( $minValueValue );
            $maxValueValue = $http->postVariable( $maxValueName );
            $maxValueValue = str_replace(" ", "", $maxValueValue );
            $maxValueValue = $locale->internalNumber( $maxValueValue );
            $defaultValueValue = $http->postVariable( $defaultValueName );
            $defaultValueValue = str_replace(" ", "", $defaultValueValue );
            $defaultValueValue = $locale->internalNumber( $defaultValueValue );
            
            $showInViewValue = $http->postVariable( $showInViewName );

            $classAttribute->setAttribute( self::MIN_FIELD, $minValueValue );
            $classAttribute->setAttribute( self::MAX_FIELD, $maxValueValue );
            $classAttribute->setAttribute( self::DEFAULT_FIELD, $defaultValueValue );
            
            $classAttribute->setAttribute( self::SHOW_IN_VIEW_FIELD, $showInViewValue );

            if ( ( $minValueValue == "" ) && ( $maxValueValue == "") ){
                $input_state =  self::STATUS_NO_MIN_MAX_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            else if ( ( $minValueValue == "" ) && ( $maxValueValue !== "") )
            {
                $input_state = self::STATUS_HAS_MAX_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            else if ( ( $minValueValue !== "" ) && ( $maxValueValue == "") )
            {
                $input_state = self::STATUS_HAS_MIN_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            else
            {
                $input_state = self::STATUS_HAS_MIN_MAX_VALUE;
                $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $input_state );
            }
            return true;
        }
        return false;
    }

    /**
     * Validates.
     * 
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_...
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $minValueName = $base . self::MIN_VARIABLE . $classAttribute->attribute( "id" );
        $maxValueName = $base . self::MAX_VARIABLE . $classAttribute->attribute( "id" );
        $defaultValueName =  $base . self::DEFAULT_VARIABLE . $classAttribute->attribute( "id" );

        if ( $http->hasPostVariable( $minValueName ) and
             $http->hasPostVariable( $maxValueName ) and
             $http->hasPostVariable( $defaultValueName ) )
        {
            $locale = eZLocale::instance();

            $minValueValue = $http->postVariable( $minValueName );
            $minValueValue = str_replace(" ", "", $minValueValue );
            $minValueValue = $locale->internalNumber( $minValueValue );
            $maxValueValue = $http->postVariable( $maxValueName );
            $maxValueValue = str_replace(" ", "", $maxValueValue );
            $maxValueValue = $locale->internalNumber( $maxValueValue );
            $defaultValueValue = $http->postVariable( $defaultValueName );
            $defaultValueValue = str_replace(" ", "", $defaultValueValue );
            $defaultValueValue = $locale->internalNumber( $defaultValueValue );

            if ( ( $minValueValue == "" ) && ( $maxValueValue == "") ){
                return  eZInputValidator::STATE_ACCEPTED;
            }
            else if ( ( $minValueValue == "" ) && ( $maxValueValue !== "") )
            {
                $max_state = $this->FloatValidator->validate( $maxValueValue );
                return  $max_state;
            }
            else if ( ( $minValueValue !== "" ) && ( $maxValueValue == "") )
            {
                $min_state = $this->FloatValidator->validate( $minValueValue );
                return  $min_state;
            }
            else
            {
                $min_state = $this->FloatValidator->validate( $minValueValue );
                $max_state = $this->FloatValidator->validate( $maxValueValue );
                if ( ( $min_state == eZInputValidator::STATE_ACCEPTED ) and
                     ( $max_state == eZInputValidator::STATE_ACCEPTED ) )
                {
                    if ($minValueValue <= $maxValueValue)
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    else
                    {
                        $state = eZInputValidator::STATE_INTERMEDIATE;
                        eZDebug::writeNotice( "Integer minimum value great than maximum value." );
                        return $state;
                    }
                }
            }

            if ($defaultValueValue == "")
            {
                $default_state =  eZInputValidator::STATE_ACCEPTED;
            }
            else
            {
                $default_state = $this->FloatValidator->validate( $defaultValueValue );
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
        $minValueName = $base . self::MIN_VARIABLE . $classAttribute->attribute( "id" );
        $maxValueName = $base . self::MAX_VARIABLE . $classAttribute->attribute( "id" );
        if ( $http->hasPostVariable( $minValueName ) and $http->hasPostVariable( $maxValueName ) )
        {
            $locale = eZLocale::instance();

            $minValueValue = $http->postVariable( $minValueName );
            $minValueValue = str_replace(" ", "", $minValueValue );
            $minValueValue = $locale->internalNumber( $minValueValue );
            $maxValueValue = $http->postVariable( $maxValueName );
            $maxValueValue = str_replace(" ", "", $maxValueValue );
            $maxValueValue = $locale->internalNumber( $maxValueValue );

            if ($minValueValue > $maxValueValue)
            {
                $this->FloatValidator->setRange( $minValueValue, false );
                $maxValueValue = $this->FloatValidator->fixup( $maxValueValue );
                $http->setPostVariable( $maxValueName, $maxValueValue );
            }
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
        return $contentObjectAttribute->attribute( "data_float" );
    }

    /**
     * Returns the content.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return array
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        $contentClassAttribute = $contentObjectAttribute->attribute('contentclass_attribute');
        if ( $contentClassAttribute->attribute('data_int1') == 1 )
        {
            $hide_in_content = "";
        }
        else
        {
            $hide_in_content = 1;
        }
        $content = array( 'content' => $contentObjectAttribute->attribute( 'data_float' ),
                          'hide_in_content' => $hide_in_content );
        return $content;
    }

    /**
     * Returns the content of the string for use as a title.
     * 
     * @param mixed $objectAttribute Class eZContentObjectAttribute.
     * @param mixed $name            Seems to have no functionality.
     *
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute->attribute( "data_float" );
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
        return true;
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
        $defaultValue = $classAttribute->attribute( self::DEFAULT_FIELD );
        $minValue = $classAttribute->attribute( self::MIN_FIELD );
        $maxValue = $classAttribute->attribute( self::MAX_FIELD );
        $minMaxState = $classAttribute->attribute( self::INPUT_STATE_FIELD );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'default-value', $defaultValue ) );
        if ( $minMaxState == self::STATUS_HAS_MIN_VALUE or $minMaxState == self::STATUS_HAS_MIN_MAX_VALUE )
        {
            $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'min-value', $minValue ) );
        }
        if ( $minMaxState == self::STATUS_HAS_MAX_VALUE or $minMaxState == self::STATUS_HAS_MIN_MAX_VALUE )
        {
            $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'max-value', $maxValue ) );
        }
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
        $defaultValue = $attributeParametersNode->elementTextContentByName( 'default-value' );
        $minValue = $attributeParametersNode->elementTextContentByName( 'min-value' );
        $maxValue = $attributeParametersNode->elementTextContentByName( 'max-value' );

        if ( strlen( $minValue ) > 0 and strlen( $maxValue ) > 0 )
        {
            $minMaxState = self::STATUS_HAS_MIN_MAX_VALUE;
        }
        else if ( strlen( $minValue ) > 0 )
        {
            $minMaxState = self::STATUS_HAS_MIN_VALUE;
        }
        else if ( strlen( $maxValue ) > 0 )
        {
            $minMaxState = self::STATUS_HAS_MAX_VALUE;
        }
        else
        {
            $minMaxState = self::STATUS_NO_MIN_MAX_VALUE;
        }

        $classAttribute->setAttribute( self::DEFAULT_FIELD, $defaultValue );
        $classAttribute->setAttribute( self::MIN_FIELD, $minValue );
        $classAttribute->setAttribute( self::MAX_FIELD, $maxValue );
        $classAttribute->setAttribute( self::INPUT_STATE_FIELD, $minMaxState );
    }
}
eZDataType::register( ymcDatatypeWeightType::DATATYPE_STRING, "ymcDatatypeWeightType" );
?>
