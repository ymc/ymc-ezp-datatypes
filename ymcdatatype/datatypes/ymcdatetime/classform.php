<?php
/**
 * File containing the ymcDatatypeDateTimeClassForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage DateTime
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Class ymcDatatypeDateTimeClassForm.
 *
 * @uses       ymcDatatypeForm
 * @package    ymcDatatype
 * @subpackage DateTime
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateTimeClassForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentClass_ymcDateTime';
    }

    /**
     * Nothing fancy.
     *
     * @return array
     */
    protected function getAbstractDefinition()
    {
        return array(
            'default' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'int',
                array( 'min_range' => 0, 'max_range' => 1 )
            )
        );
    }
}
