<?php
/**
 * File containing the ymcDatatypeEnhancedSelectionObjectForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Input Form for the object editing of an ymcenhancedselection datatype.
 *
 * @uses       ymcDatatypeForm
 * @package    ymcDatatype
 * @subpackage EnhancedSelection
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeEnhancedSelectionObjectForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentObjectAttribute_ymcenhancedselection';
    }

    /**
     * Nothing fancy.
     *
     * @return array
     */
    protected function getAbstractDefinition()
    {
        return array(
            'selected_array' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string',
                NULL,
                FILTER_REQUIRE_ARRAY
            )
        );
    }
}
