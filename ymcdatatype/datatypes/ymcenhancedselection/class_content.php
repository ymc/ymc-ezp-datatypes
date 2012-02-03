<?php
/**
 * File containing the ymcDatatypeEnhancedSelectionClassContent class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * This class represents the configuration of a ymcEnhancedSelection datatype. 
 * 
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch> 
 * @license    --ymc-unclear---
 */
class ymcDatatypeEnhancedSelectionClassContent
{
    const OPTIONS_XML_SKELETON =
        '<?xml version="1.0" encoding="UTF-8"?>
        <ezenhancedselection>
          <options/>
        </ezenhancedselection>';

    /**
     * Whether multiple options can be selected in the object or only one.
     * 
     * @var boolean
     */
    public $isMultipleSelection;

    /**
     * Whether a once published selection may be removed afterwards.
     * 
     * @var boolean
     */
    public $isRemovalForbidden;

    /**
     * Delimiter character used in the object view to list multiple options.
     * 
     * @var string
     */
    public $delimiter = '';

    /**
     * Whether the selectbox functions as a node placement selection.
     * 
     * @var boolean
     */
    public $isNodePlacement;

    /**
     * The possible options of the selection.
     *
     * Array of array (
     *              'id' => '0',
     *              'name' => 'Chanson',
     *              'identifier' => '1093',
     *              'priority' => '1',
     *              )
     * 
     * @var array
     */
    public $options = array();

    /**
     * Prevents direct instantiation.
     */
    private function __construct()
    {
    }

    /**
     * Adds an empty option to the end of the options array.
     *
     * @todo Do we need to preserve id values, so that one id refers
     * everytime to the same option? - Not important question.
     *
     * @todo Should the values be prefilled?
     *
     * @return void
     */
    public function addNewOption()
    {
        $id =(string) 1 + max( array_keys( $this->options ) );
        $this->options[$id] = array(
            'id' => $id,
            'name' => '',
            'identifier' => '',
            'priority' => ''
        );
    }

    /**
     * Returns an instance of self with data from the input form.
     * 
     * @param ymcDatatypeEnhancedSelectionClassForm $form The userinput.
     *
     * @todo Do we need to check, if identifiers are unique?
     *
     * @return ymcDatatypeEnhancedSelectionClassContent
     */
    public static function createFromForm( ymcDatatypeEnhancedSelectionClassForm $form )
    {
        $content = new self;
        $content->isMultipleSelection = $form->hasValidData( 'ismultiple_value' );
        $content->isRemovalForbidden  = $form->hasValidData( 'donotallowremoval_value' );
        $content->isNodePlacement     = $form->hasValidData( 'isnodeplacement_value' );
        $content->delimiter           = $form->getDataOrNull( 'delimiter' );

        $button = $form->getPressedButton();
        // Tell the method, if it should return options marked for removal.
        $content->options = self::getOptionsFromForm( 
            $form,
            'removeoption_button' === $button
        );

        switch( $button )
        {
            // removeoption has already been handled in getOptionsFromForm.
            case 'newoption_button':
                $content->addNewOption();
            break;

//            case 'sort':
//                
//            break;

            default:
                // There should not be any other case.
            break;
        }

        return $content;
    }

    /**
     * Subroutine for self::createFromForm().
     *
     * Parses the options from the input form. If $removeOptions is true, then 
     * options marked for removal will already be ignored at this step.
     * 
     * @param ymcDatatypeEnhancedSelectionClassForm $form          No comment.
     * @param boolean                               $removeOptions Used for the remove
     *                                                             button.
     *
     * @return array
     */
    private static function getOptionsFromForm( ymcDatatypeEnhancedSelectionClassForm $form, $removeOptions )
    {
        $options = array();
        //@todo check for validData

        $names       = $form->option_name_array;
        // remove options selected for removal
        // It is sufficient to remove only the entries from the $names array,
        // since we use it as the iterator in the following foreach.
        if(     $removeOptions
            and $form->hasValidData( 'option_remove_array' ) )
        {
            $optionsToRemove = array_keys( $form->option_remove_array );
            foreach( $optionsToRemove as $id )
            {
                if( array_key_exists( $id, $names ) )
                {
                    unset( $names[$id] );
                }
            }
        }

        $identifiers = $form->option_identifier_array;
        $priorities  = $form->option_priority_array;

        foreach( $names as $id => $name )
        {
            $option['name']       = $name;
            $option['id']         = $id;
            $option['identifier'] = $identifiers[$id];
            $option['priority']   = $priorities[$id];

            $options[$id] = $option;
        }
        return $options;
    }

    /**
     * Writes the class options in the class attribute's data fields.
     * 
     * @param eZContentClassAttribute $classAttribute Used as in/out parameter.
     *
     * @return void
     */
    public function setClassAttributeAttributes( eZContentClassAttribute $classAttribute )
    {
        $classAttribute->setAttribute(
            ymcDatatypeEnhancedSelectionType::FIELD_IS_NODE_PLACEMENT,
            (int)$this->isNodePlacement
        );

        $classAttribute->setAttribute(
            ymcDatatypeEnhancedSelectionType::FIELD_IS_MULTIPLE,
            (int)$this->isMultipleSelection
        );

        $classAttribute->setAttribute(
            ymcDatatypeEnhancedSelectionType::FIELD_DONT_ALLOW_REMOVAL,
            (int)$this->isRemovalForbidden
        );

        $classAttribute->setAttribute(
            ymcDatatypeEnhancedSelectionType::FIELD_DELIMITER,
            $this->delimiter
        );

        $classAttribute->setAttribute(
            ymcDatatypeEnhancedSelectionType::FIELD_OPTIONS,
            self::optionArrayToXml( $this->options )
        );
    }

    /**
     * Converts the options xml string to an array.
     *
     * This method had to be rewritten, since ezxml is deprecated in eZP4.
     *
     * @param string $string Valid xml. See tests data for an example.
     *
     * @todo Also accept serialized arrays to make this faster.
     *
     * @return array See $this->options for an example.
     */
    public static function optionStringToArray( $string )
    {
        $optionArray = array();
        // For now we assume to have an xml string. Later we could also
        // support a serialized array.

        $sXml = simplexml_load_string( $string );

        foreach( $sXml->options->option as $optionXml )
        {
            $option['id']         = (string)$optionXml['id'];
            $option['name']       = (string)$optionXml['name'];
            $option['identifier'] = (string)$optionXml['identifier'];
            $option['priority']   = (string)$optionXml['priority'];
            $optionArray[] = $option;
        }
        return $optionArray;
    }

    /**
     * Converts an options array to XML to be saved in the database.
     * 
     * @param array $options As returned from self::optionStringToArray().
     *
     * @return string XML string.
     */
    public static function optionArrayToXml( array $options )
    {
        $sXml = simplexml_load_string( self::OPTIONS_XML_SKELETON );
        $xmlOptions = $sXml->options;

        foreach( $options as $option )
        {
            $xmlOption = $xmlOptions->addChild( 'option' );
            foreach( $option as $key => $value )
            {
                $xmlOption->addAttribute( $key, $value );
            }
        }
        return $sXml->asXml();
    }
}
