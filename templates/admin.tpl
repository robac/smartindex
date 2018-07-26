<div>
    <center><h1>SmartIndex page sorter</h1></center>
    <br/>
    {if $namespace == ""}
    <center><h2>ROOT namespace</h2></center>
    {else}
    <center><h2>namespace: {$namespace}</h2></center>
    {/if}
    <br/>
    <br/>
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
    <button id="si__pagesorter_save">Save</button>
</div>