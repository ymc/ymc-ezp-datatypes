<?php
/**
 * File containing the ymcDatatypeFilterIntLeadingZero class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Filter
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Filter methods to be used as callback for the PHP filter extension.
 *
 * This filter checks for different ranges of integer values with leading
 * zeros.
 *
 * @package    ymcDatatype
 * @subpackage Filter
 */
class ymcDatatypeFilterIntLeadingZero
{
    /**
     * Checks, if the data is valid.
     *
     * Returns the filtered string or NULL on invalid input.
     *
     * @param string  $string     The string to filter.
     * @param integer $upperLimit The highest still valid value.
     * @param integer $lowerLimit The lowest still valid value.
     *
     * @return string The filtered String.
     */
    private static function filter( $string, $upperLimit, $lowerLimit = 0 )
    {
        if( strcspn( $string, '0123456789' ) )
        {
            return NULL;
        }

        $string = ltrim( $string, '0' );
        if( (int)$string > $upperLimit
         or (int)$string < $lowerLimit )
        {
            return NULL;
        }
        return $string;
    }

    /**
     * Filter for minutes.
     *
     * @param string $string Input string to filter.
     *
     * @return string|NULL
     */
    public static function minute( $string )
    {
        return self::filter( $string, 59 );
    }

    /**
     * Filter for hours in the 24 hours system.
     *
     * @param string $string Input string to filter.
     *
     * @return string|NULL
     */
    public static function hour24( $string )
    {
        return self::filter( $string, 23 );
    }

    /**
     * Filter for days from 01 to 31.
     *
     * @param string $string Input string to filter.
     *
     * @return string|NULL
     */
    public static function day( $string )
    {
        return self::filter( $string, 31, 1 );
    }

    /**
     * Filter for months.
     *
     * @param string $string Input string to filter.
     *
     * @return string|NULL
     */
    public static function month( $string )
    {
        return self::filter( $string, 12, 1 );
    }
}
