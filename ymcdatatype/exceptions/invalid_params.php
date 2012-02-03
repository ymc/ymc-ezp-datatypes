<?php
/**
 * File containing the ymcDatatypeInvalidParamsException class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Exceptions
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Indicates invalid parameters given to method.
 *
 * @package    ymcDatatype
 * @subpackage Exceptions
 */
class ymcDatatypeInvalidParamsException extends ymcDatatypeException
{
    /**
     * Constructs a new ymcDatatypeInvalidParamsException with message $msg.
     *
     * @param string $msg Error message.
     */
    public function __construct( $msg )
    {
        parent::__construct( 
            "Invalid method parameters given.\n".
            $msg
        );
    }
}
?>
