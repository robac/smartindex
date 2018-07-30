jQuery( function() {
    smartindex_admin_init();
});

function smartindex_admin_init() {
    jQuery("#smartindex__admin-organizer-list").sortable({
        update: function( event, ui ) {
            alert('change');
        }
    }).disableSelection();
    jQuery("#smartindex__admin-organizer-list li.namespace").dblclick(function(event){
        smartindex_admin_action_opennamespace(event);
    });

    jQuery('#smartindex__admin-organizer-save').click(smartindex_admin_action_send);

    if (jQuery("#smartindex__admin-organizer-list").length > 0) {
        jQuery(window).bind('beforeunload', function(){
            return "Are zou sure?";
        });
    }

}

function smartindex_admin_action_opennamespace(event) {

    var parentnamespace = jQuery('#smartindex__admin_namespace').val();
    var namespace = ((parentnamespace == '') ? '' : parentnamespace+':')+jQuery(event.target).children('input').val()+':organize';

    window.location.href = DOKU_BASE+"doku.php?do=admin&page=smartindex&id="+namespace;;
}

function smartindex_admin_action_send() {
    //var data = ()

    var orderList = new Array();
    jQuery("#smartindex__admin-organizer-list li").each(function(index){
        console.log( index + ": " + jQuery( this ).text() );
        if (jQuery(this).hasClass("namespace")) console.log('namespace');
        orderList.push({
           namespace: jQuery(this).hasClass("namespace"),
           id: jQuery(this).children('input').val()
        });
    });

    alert(JSON.stringify(orderList));

    jQuery.post(
        DOKU_BASE + 'lib/exe/ajax.php',
        {
            call: 'plugin_smartindex',
            action: 'save_namespace_order',
            sectoken: jQuery("input[name='sectok']").val(),
        },
        function(data) {
            alert(JSON.stringify(data));
        },
        'json'
    );
}