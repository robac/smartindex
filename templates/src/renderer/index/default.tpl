{% spaceless %}
<ul>
    {% for key, data in items[namespace] %}
        {# examine <li> class #}
        {% if data[1] %}
            {% if data[2] %}
                {% set class = 'namespace open' %}
            {% else %}
                {% set class = 'namespace closed' %}
            {% endif %}
        {% else %}
            {% set class = 'page' %}
        {% endif %}


    <li class="{{ class }}">
        <div>
            {% if isNamespace(namespace, key) == false %}
                <a href="{{ getPageURL(namespace, key) }}">{{ data[0] }}</a>
            {% else %}
                <a>{{ data[0] }}</a>
            {% endif %}
        </div>
        {% if isNamespace(namespace, key) %}
            {# recurse to sub namespace#}
            {% set subnamespace = getItemId(namespace, key) %}
            {% if items[subnamespace] is defined %}
                {% include "default.tpl" with {'namespace' : subnamespace} %}
            {% endif %}
        {% endif %}
    </li>
    {% endfor %}
</ul>
{% endspaceless %}
