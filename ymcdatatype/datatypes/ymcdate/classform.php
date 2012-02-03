<?php
/**
 * File containing the ymcDatatypeDateClassForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Date
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Class ymcDatatypeDateClassForm.
 *
 * @uses       ymcDatatypeForm
 * @package    ymcDatatype
 * @subpackage Date
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateClassForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentClass_ymcDate';
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
