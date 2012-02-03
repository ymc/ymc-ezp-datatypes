<?php
/**
 * File containing the ymcDatatypeDateType class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Date
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Datatype for eZP4 to represent a date.
 *
 * The primary purpose for this datatype is backwards compatibility to the
 * ezbirthday datatype. Please use ymcDatatypeDateTime for new projects.
 *
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage Date
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateType extends eZDataType
{
    const DATATYPE_STRING = 'ymcdate';

    /**
     * Where to store, whether to init the Date with now().
     */
    const CLASSATTRIBUTE_DEFAULT = 'data_int1';
    const DEFAULT_EMTPY   = 0;
    const DEFAULT_CURRENT = 1;

    /**
     * Constructs a new ymcDatatypeDate object.
     *
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING, 'ymcDate' );
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
            'ymcDatatypeDateClassForm',
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
     * In the case of ymcDate the user can choose, whether the attribute should
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
            'ymcDatatypeDateClassForm',
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
                $data = ymcDatatypeDate::getStringForToday();
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
     * initialized with an "empty" date or the current date depending on
     * the setting in the class edit dialog.
     *
     * @param mixed $objectAttribute Class eZContentObjectAttribute.
     *
     * @return ymcDatatypeDate
     */
    public function objectAttributeContent( $objectAttribute )
    {
        return ymcDatatypeDate::createFromString(
            $objectAttribute->attribute( 'data_text' )
        );
    }

    /**
     * Validates the input from the object edit form concerning this attribute.
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
            'ymcDatatypeDateObjectForm',
            $attribute->attribute( 'id' )
        );

        if( !$form->isValid() )
        {
            $attribute->setValidationError(
                ezi18n( 'kernel/classes/datatypes',
                'Missing date input.' ) );
            return eZInputValidator::STATE_INVALID;
        }

        try{
            $form->cache = new ymcDatatypeDate(
                array(
                    'day'   => $form->day,
                    'month' => $form->month,
                    'year'  => $form->year
                )
            );
        }
        catch( Exception $e )
        {
            $form->cache = NULL;
            return eZInputValidator::STATE_INVALID;
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Stores the object attribute input in the $contentObjectAttribute.
     *
     * The ymcDatatypeDate object instantiated in
     * validateObjectAttributeHTTPInput is reused.
     *
     * @param mixed  $http      Class eZHTTPTool.
     * @param string $base      Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $attribute Class eZContentObjectAttribute.
     *
     * @return boolean
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $attribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeDateObjectForm',
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
}

eZDataType::register( ymcDatatypeDateType::DATATYPE_STRING, "ymcDatatypeDateType" );
