<?php
/**
 * File containing the ymcDatatypeDateTimeType class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage DateTime
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Datatype for eZPublish4 representing a PHP DateTime including Timezone.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage DateTime
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateTimeType extends eZDataType
{
    const DATATYPE_STRING = 'ymcdatetime';
    const FORMS_STRING    = '_ymcDateTime';

    /**
     * Where to store, whether to init the Date with now().
     */
    const CLASSATTRIBUTE_DEFAULT = 'data_int1';
    const DEFAULT_EMTPY   = 0;
    const DEFAULT_CURRENT = 1;

    /**
     * Characters to be used for the datetime string building.
     *
     * @var array
     * @see validateObjectAttributeHTTPInput
     */
    private static $fieldSeparators = array(
        'year'   => '-',
        'month'  => '-',
        'day'    => ' ',
        'hour'   => ':',
        'minute' => ':',
        'second' => ''
    );

    /**
     * Announces datatype identifier and human readable name to eZDataType.
     *
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING, 'ymcDateTime' );
    }

    // --------------------------------------
    // Methods concerning the CLASS attribute
    // --------------------------------------

    /**
     * Sets default values for a new class attribute.
     *
     * This attribute supports only one optione, whether a new object
     * attribute should be prefilled with the current date or not.
     *
     * @param mixed $classAttribute Class eZContentClassAttribute.
     *
     * @return void
     */
    public function initializeClassAttribute( $classAttribute )
    {
        if ( NULL === $classAttribute->attribute( self::CLASSATTRIBUTE_DEFAULT ) )
        {
            $classAttribute->setAttribute( self::CLASSATTRIBUTE_DEFAULT,
                                           self::DEFAULT_EMTPY );
        }
    }

    /**
     * Validates the input from the class definition form concerning this attribute.
     *
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_INVALID/STATE_ACCEPTED
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeDateTimeClassForm',
            $classAttribute->attribute( 'id' )
        );

        if( !$form->isValid() )
        {
            return eZInputValidator::STATE_INVALID;
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Handles the input specific for one attribute from the class edit interface.
     *
     * In the case of ymcDateTime the user can choose, whether the attribute should
     * be prefilled with the actual date or not.
     *
     * @param mixed  $http      Class eZHTTPTool.
     * @param string $base      Seems to be always 'ContentClassAttribute'.
     * @param mixed  $attribute Class eZContentClassAttribute.
     *
     * @return void
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $attribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeDateTimeClassForm',
            $attribute->attribute( 'id' )
        );

        if( !$form->isValid() )
        {
            return;
        }

        $attribute->setAttribute(
            self::CLASSATTRIBUTE_DEFAULT,
            $form->default
        );
    }

    // ---------------------------------------
    // Methods concerning the OBJECT attribute
    // ---------------------------------------

    /**
     * Transfers the data from an old to a new version of an object attribute.
     *
     * The default value for an attribute of a totaly new instance of an
     * object is set in $this->objectAttributeContent().
     *
     * @param mixed $attribute         Class eZContentObjectAttribute.
     * @param mixed $version           Should be NULL on initial obj creation.
     * @param mixed $originalAttribute Class eZContentObjectAttribute.
     *
     * @return void
     */
    public function initializeObjectAttribute( $attribute, $version, $originalAttribute )
    {
        // Is this first object creation?
        if ( NULL === $version )
        {
            // This is a brand new object. Look in the class configuratin,
            // whether we should initialize it with now or leave it empty.
            // Note::the attribute about the default value is returned as
            // string from eZP. Therefore the int cast.
            if( self::DEFAULT_EMTPY === (int)$attribute
                    ->contentClassAttribute()
                    ->attribute( self::CLASSATTRIBUTE_DEFAULT ) )
            {
                $data = '';
            }
            else
            {
                $data = (string)new ymcDatatypeDateTime( 'now' );
            }
        }
        else
        {
            // This is a new version, so we copy the content from the old version.
            $data = $originalAttribute->attribute( "data_text" );
        }

        $attribute->setAttribute( "data_text", $data );
    }

    /**
     * Returns the content object of the attribute.
     *
     * If the data of the attribute is empty, then the attribute is
     * initialized with an "empty" datetime or the current date depending on
     * the setting in the class edit dialog.
     *
     * @param mixed $objectAttribute Class eZContentObjectAttribute.
     *
     * @return ymcDatatypeDateTime
     */
    public function objectAttributeContent( $objectAttribute )
    {
        return ymcDatatypeDateTime::createFromString(
            $objectAttribute->attribute( 'data_text' )
        );
    }

    /**
     * Validates the input from the object edit form concerning this attribute.
     *
     * Validation can be done most effective by trying to instantiate a
     * Datetime. The instantiated object is saved in $form->cache to
     * reuse it in fetchObjectAttributeHTTPInput.
     *
     * @param mixed  $http      Class eZHTTPTool.
     * @param string $base      Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $attribute Class eZContentObjectAttribute.
     *
     * @return int eZInputValidator::STATE_INVALID/STATE_ACCEPTED
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $attribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeDateTimeObjectForm',
            $attribute->attribute( 'id' )
        );

        $datestr = '';

        foreach( self::$fieldSeparators as $field => $separator )
        {
            if( !$form->hasValidData( $field ) )
            {
                $attribute->setValidationError(
                    ezi18n( 'kernel/classes/datatypes',
                    'Missing date input.' ) );
                return eZInputValidator::STATE_INVALID;
            }

            $datestr .= $form->$field
                        .$separator;
        }

        if( !$form->hasValidData( 'timezone' ) )
        {
        }

        $form->cache = NULL;

        try
        {
            $tz = new DateTimeZone( $form->timezone );
        }
        catch( Exception $e )
        {
            $attribute->setValidationError(
                ezi18n( 'kernel/classes/datatypes', 'Invalid Timezone.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        try{
            $form->cache = new ymcDatatypeDateTime( $datestr, $tz );
        }
        catch( ymcDatatypeInvalidParamsException $e )
        {
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Stores the object attribute input in the $contentObjectAttribute.
     *
     * The ymcDatatypeDateTime object instantiated in
     * validateObjectAttributeHTTPInput is reused.
     *
     * @param mixed  $http      Class eZHTTPTool.
     * @param string $base      Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $attribute Class eZContentObjectAttribute.
     *
     * @return boolean Whether to save the changes to the db or not.
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $attribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeDateTimeObjectForm',
            $attribute->attribute( 'id' )
        );

        if( NULL === $form->cache )
        {
            return false;
        }

        $attribute->setAttribute(
            'data_text',
            (string)$form->cache 
        );

        return true;
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
        return $contentObjectAttribute
               ->content()
               ->hasContent();
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
        return $contentObjectAttribute
               ->content()
               ->timestamp;
    }

    /**
     * Returns the type of the sortKey.
     *
     * I better use string in favor of integer as I do not know if the integer
     * type used by eZP is big enough.
     *
     * @return string
     */
    public function sortKeyType()
    {
        return 'string';
    }

    /**
     * Returns a MetaData string for the search functionality.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function metaData( $contentObjectAttribute )
    {
        return $contentObjectAttribute
               ->content()
               ->fullInfo;
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
     * Returns a string that could be used for the object title.
     *
     * @param mixed $contentObjectAttribute ContentObjectAttribute.
     * @param mixed $name                   No idea...
     *
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return $contentObjectAttribute
               ->content()
               ->fullInfo;
    }
}

eZDataType::register( ymcDatatypeDateTimeType::DATATYPE_STRING, "ymcDatatypeDateTimeType" );
