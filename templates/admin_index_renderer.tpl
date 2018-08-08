<div>
    <h1>Namespace organizer</h1>
    <br/>
    {if $namespace == ""}
    <h2>ROOT namespace</h2></center>
    {else}
    <h2>namespace: {$namespace}</h2></center>
    {/if}
    <br/>
    <br/>
    <div class="smartindex__namespace-organizer-wrapper">
        <input id="smartindex__sectoken" type="hidden" value="{$sectoken}" />
        <input id="smartindex__admin_namespace" type="hidden" value="{$namespace}">
        <ul id="smartindex__admin-organizer-list" class="smartindex-admin-list">
            {for $i = 0; $i < count($page_titles); $i++}
                {if $isnamespace[$i]}
                    {eval $li_class='namespace'; $i_icon='far fa-folder';}
                {else}
                    {eval $li_class='page'; $i_icon='far fa-file';}
                {/if}
                <li class="ui-state-default {$li_class}"><i class="structure-icon {$i_icon}"></i>{$page_titles[$i]}<i class="sortable-icon fa fa-arrows-alt-v"></i><input type="hidden" value="{$page_ids[$i]}"></li>
            {/for}
        </ul>
        <button id="smartindex__admin-organizer-save" class="ui-button ui-widget ui-corner-all"><i class="fa fa-save"></i>Save</button>
    </div>
</div>