<?php
/**
 * File containing the ymcDatatypeForm class.
 *
 * @version    //autogen//
 * @package    ymcDatatype
 * @subpackage Interfaces
 * @author     ymc-toko
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @license    --ymc-unclear---
 */

/**
 * This abstract class can be used to build form parsers with only two methods.
 *
 * When parsing input from datatypes in eZ Publish, you do not know the
 * concrete fieldname of an input field in advance. The fieldname used in the
 * HTML form is composed from a datatype base identifier, the abstract field
 * identifier like 'day', 'month' and an id for the attribute. For example:
 *
 * "ContentObjectAttribute_ymcDateTime_day_231"
 *
 * This class helps to ease the access to input data by mapping the abstract
 * field names like 'day' to the concrete field names like the above.
 *
 * @package    ymcDatatype
 * @subpackage Interfaces
 * @version    //autogen//
 * @copyright  2008 Young Media Concepts GmbH. All rights reserved.
 * @author     ymc-toko <thomas.koch@ymc.ch>
 * @license    --ymc-unclear---
 */
abstract class ymcDatatypeForm
{
    const FIELDNAME_SEPARATOR = '_';

    /**
     * Button identifiers used by eZP in class and object(?) edit forms. 
     */
    const BUTTON_STORE   = 'StoreButton';
    const BUTTON_APPLY   = 'ApplyButton';
    const BUTTON_DISCARD = 'DiscardButton';

    /**
     * Cache of form parser instances.
     *
     * @var array ( $className.$id => instanceof ymcDatatypeForm )
     * @see getInstance()
     */
    private static $instances = array();

    /**
     * Cache of abstract definitions.
     *
     * @var array ( ClassName => abstract definition array )
     * @see getAbstractDefinition()
     */
    private static $abstractDefinitions = array();

    /**
     * Definition compatible with ezcUserInput with the concrete fieldnames.
     *
     * @var array ( HTTP POST fieldname => ezcInputFormDefinitionElement )
     */
    protected $definition;

    /**
     * The concrete inputFields with their ids.
     *
     * Example:
     * array
     *     'year'     => string 'ContentObjectAttribute_ymcDateTime_year_207'
     *     'month'    => string 'ContentObjectAttribute_ymcDateTime_month_207'
     *     'day'      => string 'ContentObjectAttribute_ymcDateTime_day_207'
     *     'hour'     => string 'ContentObjectAttribute_ymcDateTime_hour_207'
     *     'minute'   => string 'ContentObjectAttribute_ymcDateTime_minute_207'
     *     'second'   => string 'ContentObjectAttribute_ymcDateTime_second_207'
     *     'timezone' => string 'ContentObjectAttribute_ymcDateTime_timezone_207'
     *
     * @var array
     */
    private $inputFields;

    /**
     * Either the Class- or ObjectAttributeInputForm.
     *
     * A NULL value indicates, that not all required fields were sent via
     * POST.
     *
     * @see hasRequiredFields()
     * @var ezcInputForm
     */
    private $form = NULL;

    /**
     * Whether the Input is valid.
     *
     * @var boolean
     * @see isValid()
     */
    private $isValid = NULL;

    /**
     * Caches the name of a pressed HTML button.
     * 
     * @var string
     * @see getPressedButton()
     */
    private $pressedButton = NULL;

    /**
     * Temporary storage between input validation and fetching.
     *
     * For the contentObjectAttribute of a ymcDatatypeDatetime for example,
     * the input is not only validated but also already processed in
     * validateObjectAttributeHTTPInput. The result is saved in this variable
     * to reuse it from fetchObjectAttributeHTTPInput.
     *
     * @var mixed
     */
    public $cache;

    /**
     * Inits the concrete form def. and tries to instantiate an ezcInputForm.
     *
     * Use getInstance() to get an instance of this class.
     *
     * @param string $id The attribute id the form class should be
     *                   responsible for.
     *
     * @throws ezcInputFormInvalidDefinitionException when the definition
     *         array is invalid or when the input source was invalid.
     */
    protected function __construct( $id )
    {
        $this->initFormDefinition( $id );

        try
        {
            $this->form = new ezcInputForm( INPUT_POST, $this->definition );
        }
        catch( ezcInputFormVariableMissingException $e )
        {
            // $this->form is left NULL to indicate, that required fields are
            // missing.
        }
    }

    /**
     * Returns a form instance with the given classname and attribute id.
     *
     * @param string $class The class to instantiate.
     * @param string $id    The attribute id the form class should be
     *                      responsible for.
     *
     * @throws ezcInputFormInvalidDefinitionException when the definition
     *         array is invalid or when the input source was invalid.
     *
     * @return ymcDatatypeForm
     */
    public static function getInstance( $class, $id )
    {
        $key = $class.$id;
        if( !array_key_exists( $key, self::$instances ) )
        {
            self::$instances[$key] = new $class( $id );
        }
        return self::$instances[$key];
    }

    /**
     * Does object initialization work.
     *
     * Takes the data from the two methods of the child class getBaseName()
     * and getAbstractDefinition() and writes the mapping of abstract to
     * concrete fieldnames to $this->inputFields and the concrete ezcUserInput
     * definition to $this->definition.
     *
     * @param string $id The attribute id the form should be responsible for.
     *
     * @return void
     */
    protected function initFormDefinition( $id )
    {
        $class = get_class( $this );
        if( !array_key_exists(
                $class,
                self::$abstractDefinitions ) )
        {
            self::$abstractDefinitions[$class] = $this->getAbstractDefinition();
        }

        $abstractDef = self::$abstractDefinitions[$class];
        $baseName    = $this->getBaseName();

        foreach( $abstractDef as $field => $def )
        {
            $concreteField = $baseName
                            .self::FIELDNAME_SEPARATOR
                            .$field
                            .self::FIELDNAME_SEPARATOR
                            .$id;
            $inputFields[$field] = $concreteField;

            $definition[$concreteField] = $def;
        }

        $this->inputFields  = $inputFields;
        $this->definition   = $definition;
    }

    /**
     * Forwards the request to the concrete fieldname in $this->form.
     *
     * @param string $field The abstract field name.
     *
     * @return string The user input.
     */
    public function __get( $field )
    {
        //@todo exceptions.
        return $this->form->{$this->inputFields[$field]};
    }

    /**
     * Wrapper to access form fields without checking for validData first.
     *
     * With this method you can replace code like this:
     * <code>
     *  if( $form->hasValidData( FIELDNAME ) )
     *  {
     *      $field = $form->FIELDNAME;
     *  }
     *  else
     *  {
     *      $field = NULL;
     *  }
     * </code>
     *
     * with this:
     * <code>
     *      $field = $form->getDataOrNull( FIELDNAME );
     * </code>
     *
     * Directly accessing a field with $form->FIELDNAME without checking for
     * validData first could throw an ezcException.
     *
     * This method still throws exceptions for FIELDNAMEs that are not defined!
     * 
     * @param string $field Fieldname.
     *
     * @return mixed
     */
    public function getDataOrNull( $field )
    {
        //@todo exceptions.
        //if the field does not exist?
        //
        $concreteField = $this->inputFields[$field];

        if( $this->form->hasValidData( $concreteField ) )
        {
            return $this->form->$concreteField;
        }
        else
        {
            return NULL;
        }
    }

    /**
     * Returns whether the input field conforms to the input definition.
     *
     * The request is mapped to the hasValidData method of the ezcUserInput
     * form.
     *
     * @param string $field The abstract field name.
     *
     * @return boolean
     */
    public function hasValidData( $field )
    {
        if( !$this->form instanceof ezcInputForm )
        {
            return FALSE;
        }

        return $this->form->hasValidData(
            $this->inputFields[$field]
        );
    }

    /**
     * Whether all input fields have valid data.
     *
     * This method is only useful for simple forms without buttons, array or
     * checkboxes, since this method returns FALSE if one of the checkboxes is
     * off or an array contains no elements or a button is not clicked.
     *
     * @return boolean
     */
    public function isValid()
    {
        if( NULL !== $this->isValid )
        {
            return $this->isValid;
        }

        if( !$this->form instanceof ezcInputForm )
        {
            return $this->isValid = FALSE;
        }

        foreach( $this->inputFields as $concreteField )
        {
            if( !$this->form->hasValidData( $concreteField ) )
            {
                return $this->isValid = FALSE;
            }
        }
        return $this->isValid = TRUE;
    }

    /**
     * Checks, whether all required fields are present in the POST request. 
     *
     * Required fields are those marked REQUIRED in the form definition. See
     * the documentation of ezcInputFormDefinitionElement.
     *
     * The check, whether all fields are present relies on the try catch in
     * the ctor. If an ezcInputFormVariableMissingException is catched there,
     * the $form property is left null to indicate this error condition.
     * 
     * @return boolean
     */
    public function hasRequiredFields()
    {
        return $this->form instanceof ezcInputForm;
    }

    /**
     * Returns the abstract name of the pressed button or false, if none pressed.
     * 
     * This method requires that the child class implemented getButtonNames().
     * 
     * @return string/FALSE
     */
    public function getPressedButton()
    {
        if( NULL === $this->pressedButton )
        {
            $this->pressedButton = FALSE;
            foreach( $this->getButtonNames() as $button )
            {
                if( $this->hasValidData( $button ) )
                {
                    $this->pressedButton = $button;
                    break;
                }
            }
        }

        return $this->pressedButton;
    }

    /**
     * Returns the concrete fieldname for $field as present in $_POST.
     *
     * This method is intended only for debugging and should not be used in
     * production code!
     * 
     * @param string $field The abstract fieldname.
     *
     * @return string
     */
    public function getConcreteFieldName( $field )
    {
        return $this->inputFields[$field];
    }

    /**
     * Returns whether input should be fetched or not.
     *
     * This method helps to work around a bug(?) in eZP: the methods
     * fetchClassAttributeHTTPInput and fetchObjectAttributeHTTPInput are
     * called, even if the user has just clicked edit or if he pressed
     * chancel.
     * By calling this method on top of your fetch method, you can avoid
     * unnecessary parsing and storing of input.
     *
     * @todo Report this bug to eZ.
     * 
     * @return boolean
     */
    public static function requiresFetching()
    {
        if( count( $_POST ) < 5 )
        {
            return FALSE;
        }
        elseif( array_key_exists( self::BUTTON_DISCARD, $_POST ) )
        {
            return FALSE;
        }
        return TRUE;
    }

    /////////////////////////////////////////////////////////
    // INTERFACE METHODS
    /////////////////////////////////////////////////////////

    /**
     * Returns an array of fieldnames that refer to buttons.
     * 
     * Those fieldnames are used by getPressedButton to return the button the
     * user has clicked. This method should be overwritten by the child class,
     * if it contains button fields.
     *
     * @return array
     */
    protected function getButtonNames()
    {
        return array();
    }

    /**
     * Returns a prefix for all concrete fieldnames.
     *
     * @return string.
     */
    abstract protected function getBaseName();

    /**
     * Returns a valid form definition for ezcUserInput.
     *
     * The only difference to the concrete form definition actually used are
     * the keys of the definition array. Instead of
     *
     * "ContentObjectAttribute_ymcDateTime_day_231"
     *
     * you should only write 'day'. The prefix "ContentObjectAttribute_ymcDateTime"
     * must come from getBaseName() and the suffix is added according to the
     * id given to getInstance().
     *
     * @return array ( abstract fieldname => ezcInputFormDefinitionElement )
     */
    abstract protected function getAbstractDefinition();
}
