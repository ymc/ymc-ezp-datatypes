<?php
/**
 * File containing the ymcDatatypeException class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Exceptions
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Base of all exceptions specific to the ymcDatatypes extension.
 *
 * @package    ymcDatatype
 * @subpackage Exceptions
 */
class ymcDatatypeException extends ezcBaseException
{
    /**
     * Constructs a new ymcDatatypeException with message $msg.
     *
     * @param string $msg Error message.
     */
    public function __construct( $msg )
    {
        parent::__construct( $msg );
    }
}
?>
