{let selected_id_array=$attribute.content
     selected_array=array()}

{section name=Option loop=$attribute.class_content.options}
    {section-exclude match=$selected_id_array|contains($Option:item.identifier)|not}
    {$Option:item.name|wash(xhtml)}
    {delimiter}
    {cond($attribute.class_content.delimiter|ne(""),$attribute.class_content.delimiter,", ")}
    {/delimiter}
{/section}

{/let}
