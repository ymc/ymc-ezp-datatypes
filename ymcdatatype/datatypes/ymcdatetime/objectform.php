<?php
/**
 * File containing the ymcDatatypeDateTimeObjectForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage DateTime
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Represents an input form for the ymcDatatypeDateTimeType object input.
 * 
 * @uses       ymcDatatypeForm
 * @package    ymcDatatype
 * @subpackage DateTime
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch> 
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateTimeObjectForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentObjectAttribute_ymcDateTime';
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
                ),
            'hour'     => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'callback',
                       array( 'ymcDatatypeFilterIntLeadingZero', 'hour24' )
                ),
            'minute'   => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'callback',
                       array( 'ymcDatatypeFilterIntLeadingZero', 'minute' )
                ),
            'second'   => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'callback',
                       array( 'ymcDatatypeFilterIntLeadingZero', 'minute' )
                ),
            'timezone' => new ezcInputFormDefinitionElement(
                        ezcInputFormDefinitionElement::OPTIONAL,
                       'string',
                       array()
                ),
            );
    }

}
