{let content=$class_attribute.content}

<div class="block">
    <div class="element">
        <label>Multiselect:</label>
        <p>{cond(int($content.is_multiselect)|eq(1),"Yes","No")}</p>
    </div>

    <div class="element">
        <label>Delimiter:</label>
        <p>{cond($content.delimiter|ne(""),$content.delimiter,"<i>Empty</i><br />(Defaults to a comma)")}</p>
    </div>

    <div class="element">
        <label>Do not allow removal of previously selected options:</label>
        <p>{cond(int($content.do_not_allow_removal)|eq(1),"Yes","No")}</p>
    </div>

    <div class="element">
        <label>Is Node placement:</label>
        <p>{cond(int($content.is_node_placement)|eq(1),"Yes","No")}</p>
    </div>
</div>

<div class="block">
    <div class="element">
        <label>Options:</label>
        <table class="list" cellspacing="0">
            <tr>
                <th>Name</th>
                <th>Identifier</th>
            </tr>
        {section var=option loop=$content.options}
            <tr>
                <td>{cond($option.name|ne(""),$option.name|wash,"<i>Empty</i>")}</td>
                <td>{cond($option.identifier|ne(""),$option.identifier|wash,"<i>Empty</i>")}</td>
            </tr>
        {/section}
        </table>
    </div>
</div>

{/let}
