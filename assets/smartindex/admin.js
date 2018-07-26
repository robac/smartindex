jQuery( function() {
    smartindex_admin_init();
});

function smartindex_admin_init() {
    jQuery("#sortable").sortable().disableSelection();
    jQuery('#smartindex__admin-organizer-save').click(smartindex_admin_action_send);
}

function smartindex_admin_action_send() {

}