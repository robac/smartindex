<div>
    <h1>Namespace organizer</h1>
    <br/>
    {% if namespace == '' %}
        <h2>ROOT namespace</h2></center>
    {% else %}
        <h2>namespace: {{ namespace }}</h2></center>
    {% endif %}
    <br/>
    <br/>
    <div class="smartindex__namespace-organizer-wrapper">
        <input id="smartindex__sectoken" type="hidden" value="{{ sectoken }}" />
        <input id="smartindex__admin_namespace" type="hidden" value="{{ namespace }}">
        <ul id="smartindex__admin-organizer-list" class="smartindex-admin-list">
            {% for key, data in items[namespace] %}
                {% if isNamespace(namespace, key) %}
                    {% set liClass, iClass = 'namespace', 'far fa-folder' %}
                {% else %}
                    {% set liClass, iClass = 'page', 'far fa-file' %}
                {% endif %}
                <li class="ui-state-default {{ liClass }}"><i class="structure-icon {{ iClass }}"></i>{{ data[0] }}<i class="sortable-icon fa fa-arrows-alt-v"></i><input type="hidden" value="{{ key }}"></li>
            {% endfor %}
        </ul>
        <button id="smartindex__admin-organizer-save" class="ui-button ui-widget ui-corner-all"><i class="fa fa-save"></i>Save</button>
    </div>
</div>