{* DO NOT EDIT THIS FILE! Use an override template instead. *}
<div class="block">
    <label>{'Show in view-mode'|i18n( 'design/standard/class/datatype' )}:</label>
    <input type="checkbox" name="ContentClass_ymcvolume_show_in_view_{$class_attribute.id}" value="1" {section show=eq($class_attribute.data_int1,1)}checked="checked"{/section} />
</div>
<div class="block">
    <label>{'Default value'|i18n( 'design/standard/class/datatype' )}:</label>
    <input type="text" name="ContentClass_ymcvolume_default_value_{$class_attribute.id}" value="{$class_attribute.data_float3|l10n(number)}" size="8" maxlength="20" />
</div>

<div class="block">
{switch name=input_state match=$class_attribute.data_float4}
{case match=1}
    <div class="element">
        <label>{'Min volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_min_volume_value_{$class_attribute.id}" value="{$class_attribute.data_float1|l10n(number)}" size="8" maxlength="20" />
    </div>
    <div class="element">
        <label>{'Max volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_max_volume_value_{$class_attribute.id}" value="" size="8" maxlength="20" />
    </div>
    <div class="break"></div>
{/case}
{case match=2}
    <div class="element">
        <label>{'Min volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_min_volume_value_{$class_attribute.id}" value="" size="8" maxlength="20" />
    </div>
    <div class="element">
        <label>{'Max volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_max_volume_value_{$class_attribute.id}" value="{$class_attribute.data_float2|l10n(number)}" size="8" maxlength="20" />
    </div>
    <div class="break"></div>
{/case}
{case match=3}
    <div class="element">
        <label>{'Min volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_min_volume_value_{$class_attribute.id}" value="{$class_attribute.data_float1|l10n(number)}" size="8" maxlength="20" />
    </div>
    <div class="element">
        <label>{'Max volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_max_volume_value_{$class_attribute.id}" value="{$class_attribute.data_float2|l10n(number)}" size="8" maxlength="20" />
    </div>
    <div class="break"></div>
{/case}
{case}
    <div class="element">
        <label>{'Min volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_min_volume_value_{$class_attribute.id}" value="" size="8" maxlength="20" />
    </div>
    <div class="element">
        <label>{'Max volume value'|i18n( 'design/standard/class/datatype' )}:</label>
        <input type="text" name="ContentClass_ymcvolume_max_volume_value_{$class_attribute.id}" value="" size="8" maxlength="20" />
    </div>
    <div class="break"></div>
{/case}
{/switch}
</div>
