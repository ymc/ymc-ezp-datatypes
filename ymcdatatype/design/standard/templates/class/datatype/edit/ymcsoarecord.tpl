{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<div class="block">
<label>{'Voreinstellungen f�r neue Objekte'|i18n( 'design/standard/class/datatype' )}</label>
<table>
 <tr>
  <td><label>{"Nameserver"|i18n("design/standard/content/datatype")}</label>
      <input type="text" name="ContentClass_soarecord_ns_{$class_attribute.id}" size="20" value="{$class_attribute.data_text1}" />&nbsp;
  </td>
  <td><label>{"Email"|i18n("design/standard/content/datatype")}</label>
      <input type="text" name="ContentClass_soarecord_email_{$class_attribute.id}" size="20" value="{$class_attribute.data_text2}" />
  </td>
 </tr>
 <tr>
  <td><label>{"Refresh period"|i18n("design/standard/content/datatype")}</label>
      <input type="text" name="ContentClass_soarecord_refresh_period_{$class_attribute.id}" size="10" value="{$class_attribute.data_int1}" />
  </td>
  <td><label>{"Retry interval"|i18n("design/standard/content/datatype")}</label>
      <input type="text" name="ContentClass_soarecord_retry_interval_{$class_attribute.id}" size="10" value="{$class_attribute.data_int2}" />
  </td>
 </tr>
 <tr>
  <td><label>{"Expire time"|i18n("design/standard/content/datatype")}</label>
      <input type="text" name="ContentClass_soarecord_expire_time_{$class_attribute.id}" size="10" value="{$class_attribute.data_int3}" />
  </td>
  <td><label>{"Default ttl"|i18n("design/standard/content/datatype")}</label>
      <input type="text" name="ContentClass_soarecord_default_ttl_{$class_attribute.id}" size="10" value="{$class_attribute.data_int4}" />
  </td>
 </tr>
</table>
</div>
