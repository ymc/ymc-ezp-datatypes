<div class="block">
    <label>
        {"Default value"|i18n("design/standard/class/datatype")}
    </label>
    <div class="labelbreak"></div>
        {section show=eq($class_attribute.data_int1,0)}
            {"Empty"|i18n("design/standard/class/datatype")}
        {/section}
        {section show=eq($class_attribute.data_int1,1)}
            {"Current date"|i18n("design/standard/class/datatype")}
        {/section}
</div>
