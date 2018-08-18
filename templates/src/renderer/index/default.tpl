<ul>
    {% for key, data in namespace %}
        {% if data[1] %}
            {% if data[2] %}
    <li class="namespace open">
            {% else %}
    <li class="namespace closed">
            {% endif %}
        {% else %}
    <li class="page">
        {% endif %}

        <div>{{ data[0] }}</div>
        {% if data[1] %}
            {{ sayhello() }}
        {% endif %}
    </li>
    {% endfor %}
</ul>