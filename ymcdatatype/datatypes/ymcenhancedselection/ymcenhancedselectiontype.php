<?php
/**
 * File containing the ymcDatatypeEnhancedSelectionType class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Datatype for a selection of predefined items or node placements.
 *
 * The datatype is based on the ezenhancedselection datatype.
 * 
 * If the datatype is not a node placement selection, it behaves exactly as
 * the ezenhancedselection datatype. 
 *
 * If is_node_placement is choosen, then the options identifiers have to be
 * valid node ids and the object is added to each selected node on object
 * editing.
 *
 * The addition to or removal from nodes is done in a hook to the content edit
 * script from eZ. See class ymcDatatypeEditHook.
 * 
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch> 
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeEnhancedSelectionType extends eZDataType
{
    const DATATYPE_STRING = 'ymcenhancedselection';

    const FIELD_IS_NODE_PLACEMENT  = 'data_int2';
    const FIELD_IS_MULTIPLE        = 'data_int1';
    const FIELD_OPTIONS            = 'data_text5';
    const FIELD_DELIMITER          = 'data_text1';

    //@todo the dont allow removal info is stored in int2 in the original
    //ezenhancedselection. This should be documented.
    const FIELD_DONT_ALLOW_REMOVAL = 'data_int3';

    const INITIAL_OPTIONS_SERIALIZED =
        '<?xml version="1.0" encoding="UTF-8"?>
        <ezenhancedselection>
          <options>
            <option id="0"
                    name=""
                    identifier=""
                    priority="" />
          </options>
        </ezenhancedselection>';

    const OPTIONS_SERIALIZATION_DELIMITER = '***';

    /**
     * Constructs a new ymcDatatypeEnhancedSelectionType object.
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING, 'ymcEnhancedSelection' );
    }

    /**
     * Adds one empty options line to the class attribute form.
     * 
     * @param mixed $classAttribute No comment.
     *
     * @return void
     */
    public function initializeClassAttribute( $classAttribute )
    {
        if ( NULL === $classAttribute->attribute( self::FIELD_OPTIONS ) )
        {
            $classAttribute->setAttribute( self::FIELD_OPTIONS,
                                           self::INITIAL_OPTIONS_SERIALIZED );
        }
    }

    /**
     * Returns the configuration of an ymcenhancedselection attribute of a class.
     * 
     * @param mixed $classAttribute No comment. 
     *
     * @return array
     */
    public function classAttributeContent( $classAttribute )
    {
        $optionArray = ymcDatatypeEnhancedSelectionClassContent::optionStringToArray(
                $classAttribute->attribute( self::FIELD_OPTIONS )
        );

// @todo: Do we need a separate array for the identifiers?
//        This would break the nice delegation of responsability via the above
//        method optionStringToArray or must be solved by an additional
//        foreach:
//        foreach( $optionArray as $option )
//        {
//            $identifiers[] = $option['identifier'];
//        }

        return array(
            'options'              => $optionArray,
//            'identifiers' => $identifiers,
            'is_multiselect'       => 
                $classAttribute->attribute( self::FIELD_IS_MULTIPLE ),
            'do_not_allow_removal' =>
                $classAttribute->attribute( self::FIELD_DONT_ALLOW_REMOVAL ),
            'is_node_placement'    => 
                $classAttribute->attribute( self::FIELD_IS_NODE_PLACEMENT ),
            'delimiter'            =>
                $classAttribute->attribute( self::FIELD_DELIMITER )
        );
    }

    /**
     * Checks, if the input for the class attribute edit form is valid.
     *
     * Checks to be done:
     *
     * - If isNodePlacement: Check that all identifiers corresponds to content
     *                       objects.
     * 
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_...
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeEnhancedSelectionClassForm',
            $classAttribute->attribute( 'id' )
        );

        // If the select is a nodeplacement value, we have to make sure, that
        // all objects referd by the identifiers exists.
        if( $form->hasValidData( 'isnodeplacement_value' ) )
        {
            //@todo check option_identifier_array for valid data.
            foreach ( $form->option_identifier_array as $key => $nodeID )
            {
                if ( $nodeID == '' or (int)$nodeID <= 0 )
                {
//                    echo 'empty input for identifier nr.'.$key ;
                    return eZInputValidator::STATE_INVALID;
                }
                else if ( !is_object(eZContentObjectTreeNode::fetch($nodeID)) )
                {
//                    echo 'Node given in identifier field ' .$key .' with value ' .$nodeID .' is no node...';
                    return eZInputValidator::STATE_INVALID;
                }
            }
        }

        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Debug function. Shows all input for class editing.
     * 
     * @param mixed $form The form.
     *
     * @return void
     */
    public function debug( $form )
    {
        var_dump( $_POST );
        $fields = array(
            // Singe Input Fields
            'delimiter' ,
            'ismultiple_value' ,
            'isnodeplacement_value' ,
            'donotallowremoval_value' ,
            // Per option fields:
            'option_name_array' ,
            'option_identifier_array' ,
            'option_priority_array' ,
            'option_remove_array' ,
            // Per option Buttons (images)
            'move_option_up' ,
            'move_option_down' ,
            // Buttons
            'removeoption_button' ,
            'newoption_button' ,
            'sort_options' ,
            // Selectbox
            'sort_options_order' 
        );


        foreach( $fields as $field )
        {
            echo "Field <b>",$field,"</b> has valid Data: ";
            echo $form->hasValidData( $field ) ? "TRUE" : "FALSE";
            echo "<br />";
            if( $form->hasValidData( $field ) )
            {
                var_dump( $form->$field );
            }
        }

    }

    /**
     * Handles the input specific for one attribute from the class edit interface.
     * 
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return boolean.
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        if( !ymcDatatypeForm::requiresFetching() )
        {
            return FALSE;
        }

        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeEnhancedSelectionClassForm',
            $classAttribute->attribute( 'id' )
        );

        $content = ymcDatatypeEnhancedSelectionClassContent::createFromForm( $form );
        $content->setClassAttributeAttributes( $classAttribute );
        return TRUE;
    }

    /**
     * Initializes the object.
     *
     * Copies the data from the old version, if a new version is made.
     *
     * @param mixed $contentObjectAttribute         Class eZContentObjectAttribute.
     * @param mixed $currentVersion                 Dont now...
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
    }

    /**
     * Validates the input from the object edit form concerning this attribute.
     *
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @todo Check, if options may be removed. This is validated by checking
     *              the identifier.
     * @todo Check, if option is required
     *
     * @return int eZInputValidator::STATE_...
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeEnhancedSelectionObjectForm',
            $contentObjectAttribute->attribute( 'id' )
        );

        if( $form->hasValidData( 'selected_array' ) )
        {
        }
        return eZInputValidator::STATE_ACCEPTED;
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
        $classContent = $contentObjectAttribute->contentClassAttribute()->content();
        if( $classContent['is_node_placement'] )
        {
            $httpInputName = 'ymcActiveNodeAssignmentsPool';
            $form = new ezcInputForm( INPUT_POST, array(
                        'ymcActiveNodeAssignmentsPool' => new ezcInputFormDefinitionElement(
                            ezcInputFormDefinitionElement::OPTIONAL,
                            'string',
                            NULL,
                            FILTER_REQUIRE_ARRAY
                        )
                    ) );
        }
        else
        {
            $httpInputName = 'selected_array';
            $form = ymcDatatypeForm::getInstance(
                'ymcDatatypeEnhancedSelectionObjectForm',
                $contentObjectAttribute->attribute( 'id' )
            );
        }

        
        $data = NULL;

        if( $form->hasValidData( $httpInputName ) )
        {
            $data = implode(
                self::OPTIONS_SERIALIZATION_DELIMITER,
                $form->$httpInputName );
        }
        elseif( $classContent['is_multiselect'] )
        {
            $data = '';
        }

        if( NULL === $data )
        {
            return FALSE;
        }
        else
        {
            $contentObjectAttribute->setAttribute( 'data_text', $data );
            return TRUE;
        }
    }

    /**
     * Returns the selected options as an array.
     *
     * The array is NOT associative.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return array
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        $data = $contentObjectAttribute->attribute( 'data_text' );

        if ( $data != '' )
        {
            return explode( 
                self::OPTIONS_SERIALIZATION_DELIMITER,
                $data
            );
        }
        else
        {
            return array();
        }
    }

}

eZDataType::register(
    ymcDatatypeEnhancedSelectionType::DATATYPE_STRING,
    "ymcDatatypeEnhancedSelectionType" );
