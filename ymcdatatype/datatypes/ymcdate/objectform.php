<?php
/**
 * File containing the ymcDatatypeDateObjectForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Date
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Represents an input form for the ymcDatatypeDateType object input.
 *
 * @uses       ymcDatatypeForm
 * @package    ymcDatatype
 * @subpackage Date
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateObjectForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentObjectAttribute_ymcDate';
    }

    /**
     * Nothing fancy.
     *
     * @return array
     */
    protected function getAbstractDefinition()
    {
        return array(
            'year'     => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'int',
                       array()
                ),
            'month'    => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'callback',
                       array( 'ymcDatatypeFilterIntLeadingZero', 'month' )
                ),
            'day'      => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'callback',
                       array( 'ymcDatatypeFilterIntLeadingZero', 'day' )
                )
            );
    }

}
