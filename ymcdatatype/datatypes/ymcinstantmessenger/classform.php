<?php

class ymcDatatypeInstantMessengerClassForm extends ymcDatatypeForm
{
    /**
     * Nothing fancy.
     *
     * @return string
     */
    protected function getBaseName()
    {
        return 'ContentClass_ymcinstantmessenger';
    }

    /**
     * Nothing fancy.
     *
     * @return array
     */
    protected function getAbstractDefinition()
    {
        return array(
            'messenger' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'string'
            ),
            'max_age' => new ezcInputFormDefinitionElement(
                ezcInputFormDefinitionElement::OPTIONAL,
                'int',
                array( 'min_range' => 0, 'max_range' => 2147483647 )
            )
        );
    }
}
