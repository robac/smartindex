/* DOKUWIKI:include assets/smartindex/admin.js */

SI_CLASS_INDEX = 'div.smartindex-treeview';
SI_CLASS_NAMESPACE = 'namespace';
SI_CLASS_OPENNAMESPACE = 'open';
SI_CLASS_CLOSEDNAMESPACE = 'closed';
SI_CLASS_PAGE = 'page';
SI_CLASS_WAITINIG = 'waiting';

SI_ID_INPUTDIALOG = 'smartindex-input-dialog-tpl';

SI_SUFFIX_INDEXCONFIG = '_conf';

SI_SELECTOR_CLOSEDNAMESPACE = 'li.'+SI_CLASS_NAMESPACE+'.'+SI_CLASS_CLOSEDNAMESPACE+' > div';
SI_SELECTOR_OPENEDNAMESPACE = 'li.'+SI_CLASS_NAMESPACE+'.'+SI_CLASS_OPENNAMESPACE+' > div';
SI_SELECTOR_PAGE = 'li.'+SI_CLASS_PAGE+' > div';

SI_URLPARAMETER_NAMESPACE = 'idx';


SI_ACTION_LOADSUBTREE = 'render_subtree';
SI_INPUTDIALOG_OPTIONS =
    {
        "closeOnEscape" : true,
        "modal"         : true,
        "autoOpen"      : false,
        "resizable"     : false,
        "dialogClass"   : 'smartindex-input-dialog'
    };


/*
 * SmartIndex initialization.
 */
jQuery(function(){
    SI_init();
});

/*
 * SmartIndex base function
 * call
 */
function SI_init() {
    jQuery.each(jQuery(SI_CLASS_INDEX), function(k, index){
        SI_initIndexEvents(index);
    });
    SI_createHTMLControls();
    SI_initInputDialog();
    SI_initContextMenu();
}

function SI_initIndexEvents(index) {
    var index_config = SI_getIndexConfiguration(index);
    jQuery(index).on('click', SI_SELECTOR_CLOSEDNAMESPACE, index_config, SI_loadNamespaceSubtree);
    jQuery(index).on('click', SI_SELECTOR_OPENEDNAMESPACE, index_config, SI_hideNamespaceContent);
    jQuery(index).on('click', SI_SELECTOR_PAGE, index_config, SI_redirectPage);
}

function SI_getIndexID(index) {
    return jQuery(index).attr("id")+SI_SUFFIX_INDEXCONFIG;
}

function SI_getIndexConfiguration(index) {
    return window[SI_getIndexID(index)];
}

function SI_createLoadNamespaceRequestData(index_config, link) {
    var data =
        {
            call: 'plugin_smartindex',
            sectoken: jQuery("input[name='sectok']").val(),
            action    : 'render_subtree',
            namespace : SI_getURLParameter(link.attr("href"), SI_URLPARAMETER_NAMESPACE),
            depth     : index_config.depth,
            theme     : index_config.theme,
        };
    return data;
}

/*
 * SmartIndex default event handlers
*/
function SI_loadNamespaceSubtree(event) {
    var link = jQuery(this).children("a");
    var element = jQuery(this).closest("li");

    if (element.hasClass(SI_CLASS_WAITINIG)) return false;
    
    if (! element.children("ul").length) {
        element.addClass(SI_CLASS_WAITINIG);

        /*jQuery.post(event.data.url, SI_createLoadNamespaceRequestData(event.data, link), function(data){
                element.append(data);
                element.removeClass(SI_CLASS_WAITINIG).removeClass(SI_CLASS_CLOSEDNAMESPACE).addClass(SI_CLASS_OPENNAMESPACE);
        });*/

        jQuery.post(
            DOKU_BASE + 'lib/exe/ajax.php',
            SI_createLoadNamespaceRequestData(event.data, link),
            function (response) {
                element.append(response.index);
                element.removeClass(SI_CLASS_WAITINIG).removeClass(SI_CLASS_CLOSEDNAMESPACE).addClass(SI_CLASS_OPENNAMESPACE);
            },
            'json'
        );
    }
    element.removeClass(SI_CLASS_CLOSEDNAMESPACE).addClass(SI_CLASS_OPENNAMESPACE);

    return false;
}

function SI_hideNamespaceContent(event) {
    jQuery(this).parent().removeClass(SI_CLASS_OPENNAMESPACE).addClass(SI_CLASS_CLOSEDNAMESPACE);
    return false;
}

function SI_redirectPage(event) {
    window.location = jQuery(this).children("a").attr("href");
    return false;
}

function SI_createHTMLControls() {
    jQuery('body').append('<div id="'+SI_ID_INPUTDIALOG+'"><p class="info-text"></p><input type="text" name="input-value" class="input-value" /></div>');
}

function SI_initInputDialog() {
    jQuery('#'+SI_ID_INPUTDIALOG).dialog(SI_INPUTDIALOG_OPTIONS);
    jQuery("#smartindex-input-dialog-tpl .input-value").keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var buttons = jQuery("#smartindex-input-dialog-tpl").dialog("option", "buttons");
            buttons[1].click.apply(jQuery("#smartindex-input-dialog-tpl"));
        }
        
    });
}

function SI_initContextMenu() {
    $items = {
        "new": {
            name: "New page",
            icon: "fas fa-plus",
            callback: SI_action_newPage,
        },
        "search": {
            name: "Search in namespace",
            icon: "fas fa-search",
            callback: SI_action_searchNamespace,
        },
        "acl": {
            name: "Show ACL",
            icon: "fas fa-users",
            callback: SI_action_showAcl,
        },
        "sep11": "---------",
        "organize": {
            name: "Organize namespace",
            icon: "fas fa-random",
            callback: SI_action_organizeNamespace,
        },
        "sep12": "---------",
        "quit": {
            name: "Quit",
            icon: "fas fa-times-circle",
            callback: function () {return;}
        }
    };

    jQuery.contextMenu({
        selector: 'li.namespace > div',
        items: $items
    });

    if (SI_HOOK_SITEMAP) {
        jQuery.contextMenu({
            selector: "li[role='treeitem'] > div",
            items: $items
        });
    }
}


function SI_action_openInputDlg(data, title, info, okHandler) {
    jQuery("#smartindex-input-dialog-tpl").dialog("option", "title", title);
    jQuery("#smartindex-input-dialog-tpl .info-text").html(info);   
    jQuery("#smartindex-input-dialog-tpl .input-value").val("");
    jQuery("#smartindex-input-dialog-tpl").dialog(
        {
            "buttons": 
            [
                {
                    "text"  : "Cancel",
                    "click" : function() {jQuery(this).dialog("close");}
                },
                {
                    "text"  : "OK",
                    "click" : function() {jQuery(this).dialog("close"); if (okHandler != null) okHandler(data, jQuery("#smartindex-input-dialog-tpl .input-value").val());}
                }
                
            ]
        });
    jQuery("#smartindex-input-dialog-tpl").dialog("open");
}

/*
 * Utils.
 */

/* Get value of URL parameter.
 * http://james.padolsey.com/javascript/bujs-1-getparameterbyname/
 */
function SI_getURLParameter(url, param) {
    param = param.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "([\\?&]|\&amp;)" + param + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(url);
    if (results == null) {
        return "";
    } else {
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
}

function SI_action_newPage(itemKey, opt) {
     var namespace = SI_getURLParameter(jQuery(this).find("a").first().attr("href"), "idx");
     var title = jQuery(this).find("a").first().html();
     SI_action_openInputDlg(namespace, "Create new page in \""+title+"\"", "Enter page name to create:", function(data, input) {
         window.location = DOKU_BASE+"doku.php?do=edit&id="+data+":"+input;
     });
 }

 function SI_action_searchNamespace(itemKey, opt) {
     var namespace = SI_getURLParameter(jQuery(this).find("a").first().attr("href"), "idx");
     var title = jQuery(this).find("a").first().html();
     SI_action_openInputDlg(namespace, "Search in \""+title+"\"", "Enter phrase to search in namespace:", function(data, input) {
         window.location = DOKU_BASE+"doku.php?do=search&id="+input+" @"+data;
     });

 }

 function SI_action_showAcl(itemKey, opt) {
     var namespace = SI_getURLParameter(jQuery(this).find("a").first().attr("href"), "idx");
     window.location = DOKU_BASE+"doku.php?do=admin&page=acl&id="+namespace;
 }

function SI_action_organizeNamespace(itemKey, opt) {
    var namespace = SI_getURLParameter(jQuery(this).find("a").first().attr("href"), "idx");
    window.alert(namespace);
    exit;
    window.location = DOKU_BASE+"doku.php?do=admin&page=smartindex&id="+namespace+':organize';
}