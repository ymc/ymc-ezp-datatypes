<?php
/**
 * File containing the ymcDatatypeInstantMessengerType class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage InstantMessenger
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * A content datatype which stores different instant messenger accounts.
 * 
 * @uses       eZDataType
 * @package    ymcDatatype
 * @subpackage InstantMessenger
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch> 
 * @license    --ymc-unclear---
 */
class ymcDatatypeInstantMessengerType extends eZDataType
{
    const MESSENGER_FIELD    = 'data_text1';
    const MAX_AGE_FIELD      = 'data_int1';
    const DATATYPE_STRING    =  'ymcinstantmessenger';

    private static $messengerTypes = array( "skype",
                                            "icq",
                                            "aim",
                                            "msn",
                                            "yahoo"
    );

    /**
     * Constructs a new ymcDatatypeInstantMessengerType instance.
     * 
     */
    public function __construct()
    {
        parent::__construct( self::DATATYPE_STRING,
                             'ymc'.ezi18n( 'kernel/classes/datatypes',
                                           'Instant Messenger',
                                           'Datatype name' ),
                             array( 'serialize_supported' => true,
                                    'object_serialize_map' => array( 'data_text' => 'text' ) ) );
        $this->IntegerValidator = new eZIntegerValidator();
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
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeInstantMessengerClassForm',
            $classAttribute->attribute( 'id' )
        );

        $form->cache = array();

        if( !$form->hasRequiredFields() )
        {
            return eZInputValidator::STATE_INVALID;
        }

        // Now check the individual fields.
        $formIsValid = TRUE;

        if( $form->hasValidData( 'max_age' ) )
        {
            $form->cache[self::MAX_AGE_FIELD] = $form->max_age;
        }
        else
        {
            $formIsValid = FALSE;
        }

        $messenger = $form->messenger;
        if( in_array( $messenger, self::$messengerTypes, TRUE ) )
        {
            $form->cache[self::MESSENGER_FIELD] = $messenger;
        }
        else
        {
            $formIsValid = FALSE;
        }

        return $formIsValid
                    ? eZInputValidator::STATE_ACCEPTED
                    : eZInputValidator::STATE_INVALID;
    }

    /**
     * Handles the input specific for one attribute from the class edit interface.
     *
     * @param mixed  $http           Class eZHTTPTool.
     * @param string $base           Seems to be always 'ContentClassAttribute'.
     * @param mixed  $classAttribute Class eZContentClassAttribute.
     *
     * @return void
     */
    public function fetchClassAttributeHTTPInput( $http, $base, $classAttribute )
    {
        $form = ymcDatatypeForm::getInstance(
            'ymcDatatypeInstantMessengerClassForm',
            $classAttribute->attribute( 'id' )
        );

        if( !is_array( $form->cache ) )
        {
            return FALSE;
        }

        foreach( $form->cache as $field => $data )
        {
            $classAttribute->setAttribute( $field, $data );
        }
        return TRUE;
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
            $contentObjectAttributeID = $originalContentObjectAttribute->attribute( "id" );
            $newInstantMessenger = ymcDatatypeInstantMessenger::fetchByObjectAttributeID($contentObjectAttribute->attribute( 'id' ));
            if ( !is_object($newInstantMessenger) )
            {
                $newInstantMessenger = ymcDatatypeInstantMessenger::fetchByObjectAttributeID($originalContentObjectAttribute->attribute( 'id' ));
                if ( is_object($newInstantMessenger) )
                {
                    $newInstantMessenger->setAttribute( 'contentobject_attribute_id', $contentObjectAttribute->attribute( 'id' ) );
                    $newInstantMessenger->store();
                }
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
        if ( $http->hasPostVariable( $base . '_ymcinstantmessenger_login_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ymcinstantmessenger_login_' . $contentObjectAttribute->attribute( 'id' ) );
            if ( $contentObjectAttribute->validateIsRequired() )
            {
                if ( $data == "" )
                {
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                    return eZInputValidator::STATE_INVALID;
                }
            }
            else
            {
                if ( $data == "" )
                {
                    return eZInputValidator::STATE_ACCEPTED;
                }
            }
            
            $classAttribute = $contentObjectAttribute->contentClassAttribute();
            $messenger = $classAttribute->attribute( self::MESSENGER_FIELD );
            
            switch($messenger)
            {
                case 'icq':
                    $this->IntegerValidator->setRange( 0, 2147483647 );
                    $state = $this->IntegerValidator->validate( $data );
                    if( $state === eZInputValidator::STATE_ACCEPTED )
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'The input is not a valid integer.' ) );
                break;
                
                case 'skype':
                case 'aim':
                case 'msn':
                case 'yahoo':
                    if ( $data != "" )
                    {
                        return eZInputValidator::STATE_ACCEPTED;
                    }
                    $contentObjectAttribute->setValidationError( ezi18n( 'kernel/classes/datatypes',
                                                                         'Input required.' ) );
                break;

                default:
                    // Don't know yet what's default...
                break;
            }
        }
        else
        {
            return eZInputValidator::STATE_ACCEPTED;
        }
        return eZInputValidator::STATE_INVALID;
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
        if ( $http->hasPostVariable( $base . '_ymcinstantmessenger_login_' . $contentObjectAttribute->attribute( 'id' ) ) )
        {
            $data = $http->postVariable( $base . '_ymcinstantmessenger_login_' . $contentObjectAttribute->attribute( 'id' ) );
            
            $instantMessenger = ymcDatatypeInstantMessenger::fetchByObjectAttributeID($contentObjectAttribute->attribute('id'));
            if ( !is_object($instantMessenger) )
            {
                $classAttribute = $contentObjectAttribute->contentClassAttribute();
                $messenger = $classAttribute->attribute( self::MESSENGER_FIELD );
                $instantMessenger = ymcDatatypeInstantMessenger::create($contentObjectAttribute->attribute('id'));
                $instantMessenger->store();
                $instantMessengerID = $instantMessenger->attribute('id');
                $contentObjectAttribute->setAttribute( 'data_int', $instantMessengerID );
            }
            
            $contentObjectAttribute->setAttribute( 'data_text', $data );
            $contentObjectAttribute->store();
            return true;
        }
        return false;
    }

    public function deleteStoredObjectAttribute( $contentObjectAttribute, $version = null )
    {
        if ( $version == null )
        {
            $contentObjectAttributeID = $contentObjectAttribute->attribute( "id" );
            $instantMessenger = ymcDatatypeInstantMessenger::fetchByObjectAttributeID($contentObjectAttribute->attribute('id'));
            if ( is_object($instantMessenger) )
            {
                $instantMessenger->remove();
            }
        }
    }

    /**
     * Store.
     *
     * Does nothing since it uses the data_text field in the content object attribute.
     * See fetchObjectAttributeHTTPInput for the actual storing.
     * 
     * @param mixed $attribute Class of ?.
     *
     * @return void
     */
    public function storeObjectAttribute( $attribute )
    {
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
        $instantMessenger = ymcDatatypeInstantMessenger::fetchByObjectAttributeID($contentObjectAttribute->attribute('id'));
        if ( is_object($instantMessenger) )
        {
            $instantMessenger->update();
        }
        else
        {
            $instantMessenger = ymcDatatypeInstantMessenger::create($contentObjectAttribute->attribute('id'));
        }
        return array( 'login' => $contentObjectAttribute->attribute( 'data_text' ),
                      'messenger' => $instantMessenger );
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
        return $contentObjectAttribute->attribute( 'data_text' );
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
        return $contentObjectAttribute->attribute( 'data_text' );
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
        $data = $contentObjectAttribute->attribute( 'data_text' );
        return trim( $data ) != '';
    }

    /**
     * IsIndexable.
     *
     * @return boolean
     */
    public function isIndexable()
    {
        return true;
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
        include_once( 'lib/ezi18n/classes/ezchartransform.php' );
        $trans = eZCharTransform::instance();
        $data = $contentObjectAttribute->attribute( 'data_text' );
        return $trans->transformByGroup( $data, 'lowercase' );
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
    
    /**
     * SerializeContentClassAttribute.
     * 
     * @param mixed $classAttribute          Class eZContentClassAttribute.
     * @param mixed $attributeNode 
     * @param mixed $attributeParametersNode 
     *
     * @return void
     */
    public function serializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $messenger = $classAttribute->attribute( self::MESSENGER_FIELD );
        $max_age = $classAttribute->attribute( self::MAX_AGE_FIELD );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'messenger', $messenger ) );
        $attributeParametersNode->appendChild( eZDOMDocument::createElementTextNode( 'max-age', $max_age ) );
    }

    /**
     * UnserializeContentClassAttribute.
     * 
     * @param mixed $classAttribute          Class eZContentClassAttribute used as in/out parameter.
     * @param mixed $attributeNode 
     * @param mixed $attributeParametersNode 
     *
     * @return void
     */
    public function unserializeContentClassAttribute( $classAttribute, $attributeNode, $attributeParametersNode )
    {
        $messenger = $attributeParametersNode->elementTextContentByName( 'messenger' );
        $max_age = $attributeParametersNode->elementTextContentByName( 'max-age' );

        $classAttribute->setAttribute( self::MESSENGER_FIELD, $messenger );
        $classAttribute->setAttribute( self::MAX_AGE_FIELD, $max_age );
    }
}

eZDataType::register( ymcDatatypeInstantMessengerType::DATATYPE_STRING, 'ymcDatatypeInstantMessengerType' );

?>
