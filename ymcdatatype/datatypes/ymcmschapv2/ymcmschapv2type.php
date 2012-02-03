<?php
/**
 * File containing the ymcDatatypeMschapv2Type class.
 *
 * Created on: <07-Jun-2007 22:40:00 ymc-dabe>
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage MSChapV2
 * @author     ymc-dabe
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * A content datatype which handles LM-/NT-passwords.
 *
 * It uses the spare field data_text in a content object attribute for storing
 * the attribute data.
 * 
 * WARNING: This datatype will store the password plaintext in the database.
 *          Actual cryption is done during paring the content. KEEP THIS IN MIND!!!
 *
 * WARNING: Pear: require_once 'Crypt/CHAP.php';
 * 
 * @uses       eZDataType
 * @uses       Crypt_CHAP_MSv2
 * @package    ymcDatatype
 * @subpackage MSChapV2
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-dabe
 * @license    --ymc-unclear---
 */
class ymcDatatypeMschapv2Type extends eZDataType
{
    const DATATYPE_STRING         = 'ymcmschapv2';
    const MAX_LEN_FIELD           = 'data_int1';
    const MAX_LEN_VARIABLE        = '_ymcmschapv2_max_string_length_';
    const MIN_LEN_FIELD           = 'data_int2';
    const MIN_LEN_VARIABLE        = '_ymcmschapv2_min_string_length_';
    const DEFAULT_STRING_FIELD    = 'data_text1';
    const DEFAULT_STRING_VARIABLE = '_ymcmschapv2_default_value_';

    /**
     * Validator.
     * 
     * @var eZIntegerValidator
     */
    private $IntegerValidator;

    /**
     * Ctor.
     * 
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                           'MSCHAPv2 LM-/NT-password',
                                           'Datatype name' ),
                             array( 'serialize_supported' => false ) );
        $this->IntegerValidator = new eZIntegerValidator();
    }

    /**
     * Sets default values for a new class attribute.
     *
     * @param mixed $contentObjectAttribute         Class eZContentObjectAttribute.
     * @param mixed $currentVersion                 Should be NULL on initial obj creation.
     * @param mixed $originalContentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return void
     */
    public function initializeObjectAttribute( $contentObjectAttribute, $currentVersion, $originalContentObjectAttribute )
    {
        if ( $currentVersion != false )
        {
            $dataText = $originalContentObjectAttribute->attribute( "data_text" );
            $contentObjectAttribute->setAttribute( "data_text", $dataText );
        }
        else
        {
            $contentClassAttribute = $contentObjectAttribute->contentClassAttribute();
            $default = $contentClassAttribute->attribute( "data_text1" );
            if ( $default !== "" )
            {
                $contentObjectAttribute->setAttribute( "data_text", $default );
            }
        }
    }

    /**
     * Validates the input from the object edit form concerning this attribute.
     *
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return int eZInputValidator::STATE_INVALID/STATE_ACCEPTED
     */
    public function validateObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ymcmschapv2_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ymcmschapv2_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $classAttribute = $contentObjectAttribute->contentClassAttribute();

            if ( $data == "" )
            {
                if ( $contentObjectAttribute->validateIsRequired() )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else
            {
                $maxLen = $classAttribute->attribute( self::MAX_LEN_FIELD );
                $textCodec = eZTextCodec::instance( false );
                if ( $textCodec->strlen( $data ) > $maxLen and
                     $maxLen > 0 )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'The input text is too long. The maximum number of characters allowed is %1.' ),
                                                                 $maxLen );
                    return eZInputValidator::STATE_INVALID;
                }
                
                $minLen = $classAttribute->attribute( self::MIN_LEN_FIELD );
                if ( $textCodec->strlen( $data ) < $minLen and
                     $minLen > 0 )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'The input text is too short. The minimum number of characters is %1.' ),
                                                                 $minLen );
                    return eZInputValidator::STATE_INVALID;
                }
                
                //Make the user input asomething different from his normal password //start
                //ToDo: Do not return valid if the user has changed his pwassword but the new password is not in the "_POST-Array...
                $found_login = false;
                $found_password = false;
                
                //ToDO: Don't walk through the $_POST-array...
                foreach ( $_POST as $key => $content )
                {
                    if ( strpos($key,'_data_user_login_') != false and
                        $content != '' )
                    {
                        $found_login = $content;
                    }
                    else if ( strpos($key,'_data_user_password_') != false and
                              $content != '' )
                    {
                        $found_password = $content;
                    }
                }
                
                if ( $found_password !== false and $found_password == $data )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'This has to be different from your new user account password.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
                else
                {
                    $objectID = $contentObjectAttribute->attribute('contentobject_id');
                    include_once( "kernel/classes/datatypes/ezuser/ezuser.php" );
                    $user = eZUser::fetch($objectID);
                    
                    if ( is_object($user))
                    {
                        if ( $found_login === false )
                        {
                            $found_login = $user->attribute('login');
                        }
                        
                        $passHash = eZUser::createHash( $found_login, $data, eZUser::site(), $user->attribute('password_hash_type') );
                        if ( $passHash == $user->attribute('password_hash') )
                        {
                            $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                                 'This has to be different from your current user account password.' ) );
                            return eZInputValidator::STATE_INVALID;
                        }
                    }
                }
                //Make the user input asomething different from his normal password //end
                
                return eZInputValidator::STATE_ACCEPTED;
            }
        }
        return eZInputValidator::STATE_ACCEPTED;
    }

    /**
     * Stores the object attribute input in the $contentObjectAttribute.
     *
     * @param mixed  $http                   Class eZHTTPTool.
     * @param string $base                   Seems to be always 'ContentObjectAttribute'.
     * @param mixed  $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean Whether to save the changes to the db or not.
     */
    public function fetchObjectAttributeHTTPInput( $http, $base, $contentObjectAttribute )
    {
        if ( $http->hasPostVariable( $base . '_ymcmschapv2_data_text_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ymcmschapv2_data_text_' . $contentObjectAttribute->attribute( 'id' ) );
            $contentObjectAttribute->setAttribute( 'data_text', $data );
            return true;
        }
        return false;
    }

    /**
     * Validates the input from the class definition form concerning this attribute.
     *
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return int eZInputValidator::STATE_INVALID/STATE_ACCEPTED
     */
    public function validateClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $result = eZInputValidator::STATE_ACCEPTED;
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        $minLenName = $base . self::MIN_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $maxLenValue = str_replace(" ", "", $maxLenValue );
            if( ( $maxLenValue == "" ) ||  ( $maxLenValue == 0 ) )
            {
                $maxLenValue = 0;
                $http->setPostVariable( $maxLenName, $maxLenValue );
            }
            else
            {
                $this->IntegerValidator->setRange( 1, false );
                $result = $this->IntegerValidator->validate( $maxLenValue );
            }
        }
        
        if ( $http->hasPostVariable( $minLenName ) )
        {
            $minLenValue = $http->postVariable( $minLenName );
            $minLenValue = str_replace(" ", "", $minLenValue );
            if( ( $minLenValue == "" ) ||  ( $minLenValue == 0 ) )
            {
                $minLenValue = 0;
                $http->setPostVariable( $minLenName, $minLenValue );
            }
            else
            {
                $this->IntegerValidator->setRange( 1, false );
                $result = $this->IntegerValidator->validate( $minLenValue );
            }
        }
        
        return $result;
    }

    public function fixupClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        $minLenName = $base . self::MIN_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $this->IntegerValidator->setRange( 1, false );
            $maxLenValue = $this->IntegerValidator->fixup( $maxLenValue );
            $http->setPostVariable( $maxLenName, $maxLenValue );
        }
        if ( $http->hasPostVariable( $minLenName ) )
        {
            $minLenValue = $http->postVariable( $minLenName );
            $this->IntegerValidator->setRange( 1, false );
            $minLenValue = $this->IntegerValidator->fixup( $minLenValue );
            $http->setPostVariable( $minLenName, $minLenValue );
        }
    }

    /**
     * Handles the input specific for one attribute from the class edit interface.
     *
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return boolean
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $maxLenName = $base . self::MAX_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        $minLenName = $base . self::MIN_LEN_VARIABLE . $classAttribute->attribute( 'id' );
        $defaultValueName = $base . self::DEFAULT_STRING_VARIABLE . $classAttribute->attribute( 'id' );
        if ( $http->hasPostVariable( $maxLenName ) )
        {
            $maxLenValue = $http->postVariable( $maxLenName );
            $classAttribute->setAttribute( self::MAX_LEN_FIELD, $maxLenValue );
        }
        if ( $http->hasPostVariable( $minLenName ) )
        {
            $minLenValue = $http->postVariable( $minLenName );
            $classAttribute->setAttribute( self::MIN_LEN_FIELD, $minLenValue );
        }
        if ( $http->hasPostVariable( $defaultValueName ) )
        {
            $defaultValueValue = $http->postVariable( $defaultValueName );

            $classAttribute->setAttribute( self::DEFAULT_STRING_FIELD, $defaultValueValue );
        }
        return true;
    }

    /**
     * Returns the content object of the attribute.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return array
     */
    public function objectAttributeContent( $contentObjectAttribute )
    {
        $data = $contentObjectAttribute->attribute( 'data_text' );
        if ( $data != '' )
        {
            require_once 'Crypt/CHAP.php';
            $crypt = new Crypt_CHAP_MSv2();
            $crypt->password = $data;
            $ntPass = bin2hex($crypt->ntPasswordHash());
            $lmPass = bin2hex($crypt->lmPasswordHash());
            $result = array( 'plain' => $data,
                             'nt' => $ntPass,
                             'nt_prefix' => '0x'.$ntPass,
                             'lm' => $lmPass,
                             'lm_prefix' => '0x'.$lmPass );
        }
        else
        {
            $result = array( 'plain' => '',
                             'nt' => '',
                             'nt_prefix' => '',
                             'lm' => '',
                             'lm_prefix' => '' );
        }
        return $result;
    }

    /**
     * Returns the meta data used for storing search indeces.
     * 
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function metaData( $contentObjectAttribute )
    {
        return '';
    }

    /**
     * Returns a string that could be used for the object title.
     *
     * @param mixed $contentObjectAttribute ContentObjectAttribute.
     * @param mixed $name                   No idea...
     *
     * @return string
     */
    public function title( $contentObjectAttribute, $name = null )
    {
        return "LM-/NT-PW COAID-".$contentObjectAttribute->attribute( 'id' );
    }

    /**
     * Returns whether the attribute contains data.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return boolean
     */
    public function hasObjectAttributeContent( $contentObjectAttribute )
    {
        return trim( $contentObjectAttribute->attribute( 'data_text' ) ) != '';
    }

    /**
     * IsIndexable.
     *
     * @return boolean
     */
    public function isIndexable()
    {
        return false;
    }

    public function isInformationCollector()
    {
        return false;
    }

    /**
     * Returns a key to sort attributes.
     *
     * @param mixed $contentObjectAttribute Class eZContentObjectAttribute.
     *
     * @return string
     */
    public function sortKey( $contentObjectAttribute )
    {
        return eZCharTransform::instance()->transformByGroup( $contentObjectAttribute->attribute( 'id' ), 'lowercase' );
    }

    /**
     * Returns the type of the sortKey.
     *
     * @return string
     */
    public function sortKeyType()
    {
        return 'string';
    }

    public function diff( $old, $new, $options = false )
    {
        $diff = new eZDiff();
        $diff->setDiffEngineType( $diff->engineType( 'text' ) );
        $diff->initDiffEngine();
        $old_content = $old->content();
        $new_content = $new->content();
        $diffObject = $diff->diff( $old_content['nt'], $new_content['nt'] );
        return $diffObject;
    }
}

eZDataType::register( ymcDatatypeMschapv2Type::DATATYPE_STRING, 'ymcDatatypeMschapv2Type' );

?>
