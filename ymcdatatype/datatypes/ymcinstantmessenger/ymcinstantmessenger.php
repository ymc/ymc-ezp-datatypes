<?php
/**
 * File containing the ymcDatatypeInstantMessenger class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage InstantMessenger
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * 
 * 
 * @uses       eZPersistentObject
 * @package    ymcDatatype
 * @subpackage InstantMessenger
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeInstantMessenger extends eZPersistentObject
{
    const STATUS_UNKNOWN        = 0;
    const STATUS_OFFLINE        = 1;
    const STATUS_ONLINE         = 2;
    const STATUS_AWAY           = 3;
    const STATUS_NOT_AVAILABLE  = 4;
    const STATUS_DO_NOT_DISTURB = 5;
    const STATUS_INVISIBLE      = 6;
    const STATUS_FREE_FOR_CHAT  = 7;
    const STATUS_NO_ACCOUNT     = 8;

    private static $disableRemoteChecks = array();

    private static $definition = array(
        'fields' => array( 'id' => array( 'name' => 'ID',
                                          'datatype' => 'integer',
                                          'default' => 0,
                                          'required' => true ),
                           'contentobjectattribute_id' => array( 'name' => 'ContentObjectAttributeID',
                                                                 'datatype' => 'integer',
                                                                 'default' => 0,
                                                                 'required' => true ),
                           'created' => array( 'name' => 'Created',
                                               'datatype' => 'integer',
                                               'default' => 0,
                                               'required' => true ),
                           'modified' => array( 'name' => 'Modified',
                                                'datatype' => 'integer',
                                                'default' => 0,
                                                'required' => true ),
                           'last_seen' => array( 'name' => 'LastSeen',
                                                 'datatype' => 'integer',  
                                                 'default' => 0,
                                                 'required' => true ),
                           'current_status_id' => array( 'name' => 'CurrentStatusID',
                                                         'datatype' => 'integer',
                                                         'default' => 0,
                                                         'required' => true ),
                           'last_status_id' => array( 'name' => 'LastStatusID',
                                                      'datatype' => 'integer',
                                                      'default' => 0,
                                                      'required' => true ) ),
        'keys' => array( 'id' ),
        'function_attributes' => array( 'contentobject_attribute' => 'contentobject_attribute',
                                        'contentobject'  => 'contentobject',
                                        'current_status' => 'current_status',
                                        'last_status'    => 'last_status',
                                        'record_age'     => 'record_age',
                                        'ever_seen'      => 'ever_seen',
                                        'messenger'      => 'messenger',
                                        'max_record_age' => 'max_record_age' ),
        'increment_key' => 'id',
        'class_name' => 'ymcDatatypeInstantMessenger',
        'name' => 'ymcinstantmessenger' );

    /**
     * Ctor.
     * 
     * @param mixed $row Database row.
     */
    public function __construct( $row )
    {
        $this->eZPersistentObject( $row );
    }

    /**
     * Return the persistent object definition. 
     * 
     * @return array
     */
    public static function definition()
    {
        return self::$definition;
    }

//    Strict standards: Declaration of ymcDatatypeInstantMessenger::remove()
//    should be compatible with that of eZPersistentObject::remove() 
//
//     * Nach ID entfernen.
//     * 
//     * @param integer $id The id of the row to remove.
//     *
//     * @return void
//     */
//    public function remove( $id )
//    {
//        
//        $delID = $this->ID;
//        if ( is_numeric( $id ) )
//        {
//            $delID = $id;
//        }
//
//        if( !is_numeric( $delID ) )
//        {
//            return;
//        }
//
//        $db = eZDB::instance();
//
//        $db->query( "DELETE FROM ymcinstantmessenger
//                     WHERE id = '$delID'" );
//    }

    /**
     * Nach id fetchen.
     * 
     * @param mixed $id 
     * @param mixed $asObject 
     *
     * @return self
     */
    public static function fetch( $id, $asObject = true )
    {
        return eZPersistentObject::fetchObject( self::definition(),
                                                null,
                                                array( 'id' => $id ),
                                                $asObject );
    }

    /**
     * Nach objectAttributeId fetchen.
     * 
     * @param mixed $objectAttributeID 
     * @param mixed $asObject 
     *
     * @return self
     */
    public static function fetchByObjectAttributeID( $objectAttributeID, $asObject = true )
    {
        return ezPersistentObject::fetchObject( self::definition(),
                                                null,
                                                array( 'contentobjectattribute_id' => $objectAttributeID ),
                                                $asObject );
    }

    /**
     * Return the type of the messenger.
     * 
     * @return self
     */
    public function messenger()
    {
        return $this
               ->contentObjectAttribute
               ->contentClassAttribute()
               ->attribute( YMC_DATATYPE_INSTANTMESSENGER_MESSENGER_FIELD );
    }

    /**
     * Return the maximum age of an entry.
     * 
     * @return string
     */
    public function maxRecordAge()
    {
        return $this
               ->contentObjectAttribute
               ->contentClassAttribute()
               ->attribute( YMC_DATATYPE_INSTANTMESSENGER_MAX_AGE_FIELD );
    }

    /**
     * Return the contentObject.
     * 
     * @return void
     */
    public function contentObject()
    {
        $singleAttributeList = ezPersistentObject::fetchObjectList( eZContentObjectAttribute::definition(),
                                                                    null,
                                                                    array( 'id' => $this->attribute( 'contentobjectattribute_id' ) ),
                                                                    null, 1,
                                                                    true );
        $singleAttribute = $singleAttributeList[0];
        return $singleAttribute->attribute('object');
    }

    /**
     * Return contentObjectAttribute.
     * 
     * @return void
     */
    public function contentObjectAttribute()
    {
        $object = $this->contentObject();
        $version = $object->attribute('current_version');
        return eZContentObjectAttribute::fetch( $this->attribute( 'contentobjectattribute_id' ), $version );
    }

    /**
     * Whether the user has ever been online.
     * 
     * @return boolean
     */
    public function everSeen()
    {
        return (bool) $this->attribute( 'last_seen' ) > 0;
    }

    /**
     * Returns the age of the entry in seconds.
     * 
     * @return integer
     */
    public function recordAge()
    {
        $timestamp = time();
        $modified = $this->attribute( 'modified' );
        return $timestamp - $modified;
    }

    /**
     * Returns the current status. Calls $this->update().
     * 
     * @return void
     */
    public function currentStatus()
    {
        $this->update();
        return $this->status( $this->attribute('current_status_id' ) );
    }
    
    /**
     * Returns the last status.
     * 
     * @return void
     */
    public function lastStatus()
    {
        return $this->status( $this->attribute('last_status_id' ) );
    }
    
    /**
     * 
     * Status nach $status_id ausgeben
     * 
     * @param mixed $status_id 
     *
     * @return array
     */
    public function status( $status_id )
    {
        $messenger = $this->attribute('messenger');
        if ( $messenger == "skype" )
        {
            return self::skypeMapping( $status_id );
        }
        return array( 'official_status'               => 'UNSUPPORTED MESSENGER',
                      'status'                        => ezi18n( 'kernel/classes/datatypes', 'Unsupported messenger' ),
                      'official_icon_name'            => 'unsupported_messenger',
                      'icon_name'                     => 'unsupported_messenger',
                      'description'                   => ezi18n( 'kernel/classes/datatypes', 'The messenger is not supported.' ) );
    }

    /**
     * Update the status.
     * 
     * @param boolean $force. 
     *
     * @return boolean
     */
    public function update( $force = false )
    {
        if ( $this->recordAge() > $this->maxRecordAge() or $force )
        {
            $messenger = $this->attribute('messenger');
            $objectAttribute = $this->contentObjectAttribute();
            $timestamp = time();
            
            if ( $messenger = "skype" )
            {
                if ( is_object($objectAttribute) and $objectAttribute->attribute('has_content') )
                {
                    $skypeName = str_replace('.', '%2E', rawurlencode($objectAttribute->attribute('data_text')));
                    $status_id = (int) trim($this->getRemoteServerResponse( 'http://mystatus.skype.com/'.$skypeName.'.num', $messenger ));
                }
                else
                {
                    $status_id = self::STATUS_NO_ACCOUNT;
                }
            }
            else
            {
                return false;
            }
            
            if ( $status_id > 8 )
            {
                return false;
            }
            
            if ( $this->attribute( 'current_status_id' ) != $status_id )
            {
                $last_status_id = $this->attribute( 'current_status_id', $status_id );
                $this->setAttribute( 'last_status_id', $last_status_id );
                $this->setAttribute( 'current_status_id', $status_id );
            }
            //$this->setAttribute( 'last_status_id', 6 );
            $this->setAttribute( 'modified', $timestamp );
            if ( $status_id != self::STATUS_UNKNOWN and
                 $status_id != self::STATUS_OFFLINE and
                 $status_id != self::STATUS_NO_ACCOUNT )
            {
                $this->setAttribute( 'last_seen', $timestamp );
            }
            $this->store();
            return true;
        }
        return false;
    }

    /**
     * 
     * 
     * @param mixed $server_url 
     * @param mixed $messenger 
     * @param float $fsocket_timeout 
     *
     * @return void
     */
    public function getRemoteServerResponse( $server_url, $messenger, $fsocket_timeout = 5 )
    {
        if ( !isset(self::$disableRemoteChecks[$messenger]) )
        {
            self::$disableRemoteChecks[$messenger] = false;
        }
        
        if ( !self::$disableRemoteChecks[$messenger] )
        {
            $fp = false;
            $urlParts = parse_url($server_url);
            $host = $urlParts['host'];
            $port = (isset($urlParts['port'])) ? $urlParts['port'] : 80;
            
            if( !$fp = @fsockopen( $host, $port, $errno, $errstr, $fsocket_timeout ))
            {
                // Server not responding
                self::$disableRemoteChecks[$messenger] = true;
                return null;
            }
            else
            {
                if( !fputs( $fp, "GET $server_url HTTP/1.0\r\nHost:$host\r\n\r\n" ))
                {
                    fclose ($fp);
                    self::$disableRemoteChecks[$messenger] = true;
                    return null;
                }
                
                $data = null;
                stream_set_timeout($fp, $fsocket_timeout);   
                $status = socket_get_status($fp);
                while( !feof($fp) && !$status['timed_out'])         
                {
                   $data .= fgets ($fp,8192);
                   $status = socket_get_status($fp);
                }
                fclose ($fp);
                
                if ( $status['timed_out'] or stristr($data, "Connection refused") )
                {
                    self::$disableRemoteChecks[$messenger] = true;
                    return null;
                }
                
                // strip headers
                $sData = split("\r\n\r\n", $data, 2);
                $data = $sData[1];
            }
            return $data;
        }
        return null;
    }

    /**
     * Creates a new ymcDatatypeInstantMessenger instance.
     * 
     * @param mixed $objectAttributeID 
     *
     * @return self
     */
    public static function create( $objectAttributeID )
    {
        $timestamp = time();
        $row = array(
            'contentobjectattribute_id' => $objectAttributeID,
            'created'                   => $timestamp,
            'modified'                  => 0,
            'last_seen'                 => 0,
            'current_status_id'         => self::STATUS_NO_ACCOUNT,
            'last_status_id'            => self::STATUS_NO_ACCOUNT );

        return new self( $row );
    }

    /**
     * Map status for skype.
     * 
     * @param mixed $status_id 
     *
     * @return array
     */
    private static function skypeMapping( $status_id )
    {
        static $skypeMapping = NULL;

        if( NULL === $skypeMapping )
        {
            $skypeMapping = array(
                    self::STATUS_UNKNOWN => array( 
                        'official_status'    => 'UNKNOWN',
                        'status'             => ezi18n( 'kernel/classes/datatypes', 'Unknown' ),
                        'official_icon_name' => 'skype_unknown',
                        'icon_name'          => 'unknown',
                        'description'        => ezi18n( 'kernel/classes/datatypes', 'Not opted in or no data available.' ) ),
                    self::STATUS_OFFLINE => array( 
                        'official_status'    => 'OFFLINE',
                        'status'             => ezi18n( 'kernel/classes/datatypes', 'Offline' ),
                        'official_icon_name' => 'skype_offline',
                        'icon_name'          => 'offline',
                        'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is Offline' ) ),
                    self::STATUS_ONLINE => array( 
                        'official_status'    => 'ONLINE',
                        'status'             => ezi18n( 'kernel/classes/datatypes', 'Online' ),
                        'official_icon_name' => 'skype_online',
                        'icon_name'          => 'online',
                        'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is Online' ) ),
                    self::STATUS_AWAY => array( 
                        'official_status'    => 'AWAY',
                        'status'             => ezi18n( 'kernel/classes/datatypes', 'Away' ),
                        'official_icon_name' => 'skype_away',
                        'icon_name'          => 'away',
                        'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is Away' ) ),
                    self::STATUS_NOT_AVAILABLE => array( 
                            'official_status'    => 'NOT AVAILABLE',
                            'status'             => ezi18n( 'kernel/classes/datatypes', 'Not Available' ),
                            'official_icon_name' => 'skype_notavailable',
                            'icon_name'          => 'notavailable',
                            'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is Not Available' ) ),
                    self::STATUS_DO_NOT_DISTURB => array( 
                            'official_status'    => 'DO NOT DISTURB',
                            'status'             => ezi18n( 'kernel/classes/datatypes', 'Do Not Disturb' ),
                            'official_icon_name' => 'skype_donotdisturb',
                            'icon_name'          => 'donotdisturb',
                            'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is Do Not Disturb (DND)' ) ),
                    self::STATUS_INVISIBLE => array( 
                            'official_status'    => 'INVISIBLE',
                            'status'             => ezi18n( 'kernel/classes/datatypes', 'Invisible' ),
                            'official_icon_name' => 'skype_invisible',
                            'icon_name'          => 'invisible',
                            'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is Invisible or appears Offline' ) ),
                    self::STATUS_FREE_FOR_CHAT => array( 
                            'official_status'    => 'SKYPE ME',
                            'status'             => ezi18n( 'kernel/classes/datatypes', 'Free for chat' ),
                            'official_icon_name' => 'skype_freeforchat',
                            'icon_name'          => 'freeforchat',
                            'description'        => ezi18n( 'kernel/classes/datatypes', 'The user is in Skype Me mode' ) ),
                    self::STATUS_NO_ACCOUNT => array( 
                            'official_status'    => 'NO ACCOUNT',
                            'status'             => ezi18n( 'kernel/classes/datatypes', 'No skype account' ),
                            'official_icon_name' => 'skype_noaccount',
                            'icon_name'          => 'noaccount',
                            'description'        => ezi18n( 'kernel/classes/datatypes', 'The user does not have a skype account.' ) ) );

        }

        return $skypeMapping[$status_id];
    }

    /**
     * Entfernt alle Einträge aus der DB.
     * 
     * @return void
     */
    public static function cleanup()
    {
        $db = eZDB::instance();
        $db->query( "DELETE FROM ymcinstantmessenger" );
    }

    /**
     * 
     * @param mixed   $cli 
     * @param boolean $isQuiet 
     *
     * @return boolean
     */
    public static function ymcTidyUp( $cli, $isQuiet = true )
    {
        $info = array();
        if ( !is_object($cli) )
        {
            return false;
        }
//        include_once( 'extension/ymctidyup/classes/ymctidyup.php' );
        $info = ymcTidyUp::tidyUpUsingContentObjectAttributeReferenceField(
        self::definition(), 'contentobjectattribute_id' );
        ymcTidyUp::printSomeInfo( $info, $cli, $isQuiet );
        return true;
    }
}

?>
