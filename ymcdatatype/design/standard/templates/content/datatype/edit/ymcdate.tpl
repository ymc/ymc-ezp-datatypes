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
      </tr>
      <tr>
        <td>
          <input name="{$attribute_base}_ymcDate_day_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.day}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDate_month_{$attribute.id}"
                 type="text"
                 size="2"
                 maxlength="2" 
                 value="{$attribute.content.month}"/>
        </td>
        <td>
          <input name="{$attribute_base}_ymcDate_year_{$attribute.id}"
                 type="text"
                 size="4"
                 maxlength="4" 
                 value="{$attribute.content.year}"/>
        </td>
      </tr>
    </table>
  </div>
  <div class="break"></div>
</div>
