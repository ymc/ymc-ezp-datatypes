<?php
/**
 * File containing the ymcDatatypeDateTime class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage DateTime
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * Content class for ymcDatatypeDateTimeType.
 *
 * @uses       DateTime
 * @package    ymcDatatype
 * @subpackage DateTime
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
class ymcDatatypeDateTime
{
    const IN_SEC_1_DAYS   = 86400;
    const IN_SEC_2_DAYS   = 172800;
    const IN_SEC_3_DAYS   = 259200;
    const IN_SEC_4_DAYS   = 345600;
    const IN_SEC_5_DAYS   = 432000;
    const IN_SEC_6_DAYS   = 518400;
    const IN_SEC_2_HOURS  = 7200;
    const IN_SEC_5_HOURS  = 18000;
    const IN_SEC_80_HOURS = 288000;
    const IN_SEC_60_HOURS = 216000;

    const MO = '1';
    const TU = '2';
    const WE = '3';
    const TH = '4';
    const FR = '5';
    const SA = '6';
    const SU = '0';
    // Different Sunday strings for format( 'w' ) or format( 'N' );
    const SU_N = '7';
    const SU_W = '0';

    const FORMAT_MYSQL_FULL           = 'Y-m-d H:i:s';
    const FORMAT_MYSQL_DATE           = 'Y-m-d';
    const FORMAT_MYSQL_TIME           = 'H:i:s';

    const FORMAT_YEAR_4_DIGITS        = 'Y';
    const FORMAT_YEAR_2_DIGITS        = 'y';

    const FORMAT_MONTH_LEADING_ZERO   = 'm';
    const FORMAT_MONTH                = 'n';

    const FORMAT_DAY_LEADING_ZERO     = 'd';
    const FORMAT_DAY                  = 'j';

    const FORMAT_HOUR_24_LEADING_ZERO = 'H';
    const FORMAT_HOUR_12_LEADING_ZERO = 'h';
    const FORMAT_HOUR_24              = 'G';
    const FORMAT_HOUR_12              = 'g';

    const FORMAT_MINUTE_LEADING_ZERO  = 'i';
    const FORMAT_SECOND_LEADING_ZERO  = 's';

    const FORMAT_TIMEZONE_ABBREVIATION = 'T';
    const FORMAT_TIMEZONE_IDENTIFIER   = 'e';

    const FORMAT_UNIX_TIMESTAMP        = 'U';
    const FORMAT_FULL_INFO             = DATE_ATOM;

    /**
     * Defines attributes and their formats for __get().
     *
     * @var array
     */
    private static $virtualProperties = array(
                          'year'       => self::FORMAT_YEAR_4_DIGITS,
                          'month'      => self::FORMAT_MONTH,
                          'day'        => self::FORMAT_DAY,
                          'hour'       => self::FORMAT_HOUR_24,
                          'minute'     => self::FORMAT_MINUTE_LEADING_ZERO,
                          'second'     => self::FORMAT_SECOND_LEADING_ZERO,
                          'timezone'   => self::FORMAT_TIMEZONE_ABBREVIATION,
                          'timezoneId' => self::FORMAT_TIMEZONE_IDENTIFIER,
                          'timestamp'  => self::FORMAT_UNIX_TIMESTAMP,
                          'fullInfo'   => self::FORMAT_FULL_INFO,
                          'mysqlFull'  => self::FORMAT_MYSQL_FULL
    );

    /**
     * Holds a string representation of the date for serialization.
     *
     * @var string
     */
    private $dateString;

    /**
     * Holds a string representation of the timezone for serialization.
     *
     * @var string
     */
    private $timezoneString;

    /**
     * Contains the actual content data of this class.
     *
     * @var DateTime
     */
    private $dateTime = NULL;

    /**
     * Constructs a new ymcDatatypeDateTime instance.
     *
     * The parameters can be ommited which leads to empty strings for all
     * attributes.
     *
     * @param string $dtString A valid first parameter for DateTime::__construct().
     * @param mixed  $tz       Instance of DateTimeZone or a valid parameter
     *                         for DateTimeZone::__construct().
     *
     * @throws ymcDatatypeInvalidParamsException
     */
    public function __construct( $dtString = NULL, $tz = NULL )
    {
        if( is_string( $dtString ) )
        {
            if( is_string( $tz ) )
            {
                try{
                    $tz = new DateTimeZone( $tz );
                } catch( Exception $e ) {
                    throw new ymcDatatypeInvalidParamsException(
                            $tz.' is no valid timezone identifier.'
                    );
                }
            }
            elseif( NULL === $tz )
            {
                $tz = new DateTimeZone( date_default_timezone_get() );
            }

            if( !$tz instanceOf DateTimeZone )
            {
                throw new ymcDatatypeInvalidParamsException(
                        'The second parameter of the constructor needs to be either a '
                        .'valid Timezone string or an instance of DateTimeZone.' );
            }

            try
            {
                $this->dateTime = new DateTime( $dtString, $tz );
            }
            catch( Exception $e )
            {
                throw new ymcDatatypeInvalidParamsException( $dtString.' is no valid DateTime string.');
            }
        }
    }

    /**
     * Returns the attribute $name.
     *
     * @param string $name Of the attribute.
     *
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
     * Returns the virtual property $name.
     *
     * @param string $name Of the attribute.
     *
     * @see    self::$virtualProperties
     * @throws ezcBasePropertyNotFoundException For undefined properties.
     * @return mixed
     */
    public function __get( $name )
    {
        if( array_key_exists( $name, self::$virtualProperties ) )
        {
            if( NULL === $this->dateTime )
            {
                return '';
            }
            return $this->dateTime->format( self::$virtualProperties[$name] );
        }
        throw new ezcBasePropertyNotFoundException( $name );
    }

    /**
     * Checks, if the attribute $name exists.
     *
     * @param string $name Of the attribute.
     *
     * @return boolean
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, self::$virtualProperties );
    }

    /**
     * Returns whether the object contains data.
     *
     * @return boolean
     */
    public function hasContent()
    {
        return NULL !== $this->dateTime;
    }

    /**
     * Returns a string representation of the datetime object.
     *
     * This string can also be used to save the data to the database.
     * 
     * @return string
     */
    public function __toString()
    {
        if( NULL === $this->dateTime )
        {
            return '';
        }
        return $this->dateTime->format( self::FORMAT_MYSQL_FULL )
              .$this->dateTime->format( self::FORMAT_TIMEZONE_IDENTIFIER );
    }

    /**
     * Returns an instance of self with the data of $string.
     *
     * If an empty string is given, then the returned object is like one
     * returned by new ymcDatatypeDateTime without parameters.
     * 
     * @param string $string Must be exactly as returned by __toString().
     *
     * @return ymcDatatypeDateTime
     */
    public static function createFromString( $string )
    {
        if( '' === $string )
        {
            return new self;
        }
        return new self(
            substr( $string, 0, 19 ),
            substr( $string, 19 )
        );
    }
}
