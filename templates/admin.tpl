<div>
    <h1>Namespace organizerr</h1>
    <br/>
    {if $namespace == ""}
    <h2>ROOT namespace</h2></center>
    {else}
    <h2>namespace: {$namespace}</h2></center>
    {/if}
    <br/>
    <br/>
    <div class="smartindex__namespace-organizer-wrapper">
        <input type="hidden" value="{$namespace}">
        <ul id="sortable" class="smartindex-admin-list">
            {for $i = 0; $i < count($namespaces); $i++}
                {if $isnamespace[$i]}
                    <li class="ui-state-default namespace"><i class="structure-icon fa fa-folder"></i><b>{$namespaces[$i]}</b><i class="sortable-icon fa fa-arrows-alt-v"></i></li>
                {else}
                    <li class="ui-state-default page"><i class="structure-icon fa fa-file"></i>{$namespaces[$i]}<i class="sortable-icon fa fa-arrows-alt-v"></i></li>
                {/if}
            {/for}
        </ul>
        <button id="smartindex__admin-organizer-save" class="ui-button ui-widget ui-corner-all">Save</button>
    </div>
</div>