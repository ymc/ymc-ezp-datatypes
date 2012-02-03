<?php
/**
 * File containing the ymcDatatypeDate class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Date
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Content class for ymcDatatypeDateType.
 *
 * @uses       DateTime
 * @package    ymcDatatype
 * @subpackage Date
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeDate
{
    const SERIALIZATION_DELIMITER = '-';

    /**
     * Virtual properties for __get/__set.
     *
     * @var array
     */
    private $properties = array(
                    'day'   => NULL,
                    'month' => NULL,
                    'year'  => NULL
    );

    /**
     * Constructs an instance of ymcDatatypeDate.
     *
     * @param array $date With 'day', 'month', 'year' as Keys.
     *
     * @throws ymcDatatypeException For an invalid $date array.
     */
    public function __construct( array $date = NULL )
    {
        if( NULL !== $date )
        {
            // Check, if all array elements are present.
            foreach( array( 'day', 'month', 'year' ) as $key )
            {
                if( !array_key_exists( $key, $date ) )
                {
                    throw new ymcDatatypeException( 'Missing key '.$key
                            .' in array given to Ctor of ymcDatatypeDate.');
                }
                if( empty( $date[$key] ) )
                {
                    throw new ymcDatatypeException( 'No data given for '.$key );
                }
            }

            // Check, if we have a valid date.
            if( !checkdate( $date['month'],
                        $date['day'],
                        $date['year'] ) )
            {
                throw new ymcDatatypeException(
                        'No valid date has been given to Ctor. Given data: '
                        .'day: '.$date['day'].' month: '.$date['month']
                        .' year: '.$date['year'] );
            }

            foreach( array( 'day', 'month', 'year' ) as $key )
            {
                $this->properties[$key] = $date[$key];
            }
        }
    }

    /**
     * Returns the attribute $name.
     *
     * @param string $name Of the attribute.
     *
     * @throws ezcBasePropertyNotFoundException For undefined properties.
     * @return mixed
     */
    public function attribute( $name )
    {
        return $this->__get( $name );
    }

    /**
     * Checks, if the attribute $name exists.
     *
     * @param string $name Of the attribute.
     *
     * @return boolean
     */
    public function hasAttribute( $name )
    {
        return $this->__isset( $name );
    }

    /**
     * Magic method to return virtual properties.
     *
     * @param mixed $name Virtual property to return.
     *
     * @throws ezcBasePropertyNotFoundException For undefined properties.
     * @return mixed
     */
    public function __get( $name )
    {
        if( !array_key_exists( $name, $this->properties ) )
        {
            throw new ezcBasePropertyNotFoundException( $name );
        }
        return $this->properties[$name];
    }

    /**
     * Magic method to check the existence of virtual properties.
     *
     * @param mixed $name Virtual property to check.
     *
     * @return boolean
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, $this->properties );
    }

    /**
     * Returns a string representation of the date as in date( 'Y-m-d' ).
     *
     * @return string
     */
    public function __toString()
    {
        return $this->year
               .self::SERIALIZATION_DELIMITER
               .sprintf("%02d", $this->month )
               .self::SERIALIZATION_DELIMITER
               .sprintf("%02d", $this->day );
    }

    /**
     * Returns a ymcDatatypeDate object for the given date string.
     *
     * @param string $datestr Format as in date( 'Y-m-d' ).
     *
     * @return ymcDatatypeDate
     */
    public static function createFromString( $datestr )
    {
        if( '' === $datestr )
        {
            return new self;
        }

        list( $a['year'],
              $a['month'],
              $a['day'] ) = explode(
                                self::SERIALIZATION_DELIMITER,
                                $datestr );
        return new self( $a );
    }

    /**
     * Returns a string for today to be used by createFromString().
     *
     * @return string
     */
    public static function getStringForToday()
    {
        return date( 'Y-m-d' );
    }
}
