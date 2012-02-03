{* DO NOT EDIT THIS FILE! Use an override template instead. *}
{let messengers=hash( "aim", "AOL Instant Messenger"|i18n( 'design/standard/class/datatype' ),
                      "icq", "ICQ"|i18n( 'design/standard/class/datatype' ),
                      "msn", "MSN Messenger"|i18n( 'design/standard/class/datatype' ),
                      "skype", "Skype"|i18n( 'design/standard/class/datatype' ),
                      "yahoo", "Yahoo Messenger"|i18n( 'design/standard/class/datatype' ) ) }
<div class="block">
    <label>{'Messenger'|i18n( 'design/standard/class/datatype' )}</label>
    <select name="ContentClass_ymcinstantmessenger_messenger_{$class_attribute.id}">
      <option value=""></option>
    {foreach $messengers as $key => $value}
      <option value="{$key}"{if eq($key,$class_attribute.data_text1)} selected="selected"{/if}>{$value}</option>
    {/foreach}
    </select>
</div>
<div class="block">
    <label>{'Max record age in seconds'|i18n( 'design/standard/class/datatype' )}</label>
    <input type="text" name="ContentClass_ymcinstantmessenger_max_age_{$class_attribute.id}" value="{$class_attribute.data_int1}" />
</div>
{/let}
