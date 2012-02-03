<?php
/**
 * File containing the ymcDatatypeEnhancedSelectionClassForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Class ymcDatatypeDateTimeClassForm.
 *
 * @uses       ymcDatatypeForm
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeEnhancedSelectionClassForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentClass_ymcenhancedselection';
    }

    /**
     * Nothing fancy.
     *
     * @return array
     */
    protected function getButtonNames()
    {
        return array( 
              'move_option_up',
              'move_option_down',
              'removeoption_button',
              'newoption_button',
              'sort_options'    
        );
    }

    /**
     * Nothing fancy.
     *
     * @return array
     */
    protected function getAbstractDefinition()
    {
        return array(
            // Singe Input Fields
            'delimiter' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string'
            ),
            'ismultiple_value' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'boolean'
            ),
            'isnodeplacement_value' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'boolean'
            ),
// Der ist nur im original
            'donotallowremoval_value' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'boolean'
            ),
            // Per option fields:
            'option_name_array' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string',
                NULL,
                FILTER_REQUIRE_ARRAY
            ),
            'option_identifier_array' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string',
                NULL,
                FILTER_REQUIRE_ARRAY
            ),
            'option_priority_array' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'int',
                NULL,
                FILTER_REQUIRE_ARRAY
            ),
            'option_remove_array' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'boolean',
                NULL,
                FILTER_REQUIRE_ARRAY
            ),
            // Per option Buttons (images)
// Die müssen umbenannt werden:
            'move_option_up' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string',
                NULL,
                FILTER_REQUIRE_ARRAY
            ),
            'move_option_down' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string',
                NULL,
                FILTER_REQUIRE_ARRAY
            ),
            // Buttons
            'removeoption_button' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string'
            ),
            'newoption_button' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string'
            ),
// Hat kein Prefix
            'sort_options' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string'
            ),
            // Selectbox
// Hat kein Prefix
            'sort_options_order' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string'
            )
        );
    }
}
