{default $attribute_base='ContentObjectAttribute'
         }
<div class="block">
  <div class="element">
    <div class="labelbreak"></div>
    <table border="0" cellpadding="2" cellspacing="0">
      <tr>
        <th width="50"><label>{"Day"|i18n("design/standard/content/datatype")}</label></th>
        <th width="50"><label>{"Month"|i18n("design/standard/content/datatype")}</label></th>
        <th width="50"><label>{"Year"|i18n("design/standard/content/datatype")}</label></th>
        <th width="50"><label>{"Hour"|i18n("design/standard/content/datatype")}</label></th>
        <th width="50"><label>{"Minute"|i18n("design/standard/content/datatype")}</label></th>
        <th width="50"><label>{"Second"|i18n("design/standard/content/datatype")}</label></th>
        <th width="50"><label>{"Timezone"|i18n("design/standard/content/datatype")}</label></th>
      </tr>
      <tr>
        <td>
          <input name="{$attribute_base}_ymcDateTime_day_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.day}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDateTime_month_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.month}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDateTime_year_{$attribute.id}"
                 type="text"
                 size="4"
                 maxlength="4" 
                 value="{$attribute.content.year}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDateTime_hour_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.hour}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDateTime_minute_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.minute}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDateTime_second_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.second}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDateTime_timezone_{$attribute.id}"
                 type="text"
                 size="3"
                 maxlength="3" 
                 value="{$attribute.content.timezone}"/>
        </td>
      </tr>
    </table>
  </div>
  <div class="break"></div>
</div>
