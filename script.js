/* DOKUWIKI:include js/jquery.ui.position.js */
/* DOKUWIKI:include js/jquery.contextMenu.min.js */



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

SI_ACTION_LOADSUBTREE = 'load_namespace';
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
        SI_initIndex(index);
    });
    SI_createHTMLControls();
    SI_initInputDialog();
    SI_initContextMenu();
}

function SI_initIndex(index) {
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
            namespace : SI_getURLParameter(link.attr("href"), SI_URLPARAMETER_NAMESPACE),
            depth     : index_config.depth,
            theme     : index_config.theme,
            action    : SI_ACTION_LOADSUBTREE
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

        jQuery.post(event.data.url, SI_createLoadNamespaceRequestData(event.data, link), function(data){
                element.append(data);
                element.removeClass(SI_CLASS_WAITINIG).removeClass(SI_CLASS_CLOSEDNAMESPACE).addClass(SI_CLASS_OPENNAMESPACE);
        });
    }

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
    jQuery('body').append('<span class="context-menu-one btn btn-neutral">right click me</span>');
}

function SI_initInputDialog() {
    jQuery('#'+SI_ID_INPUTDIALOG).dialog(
        {
            "closeOnEscape" : true,
            "modal"         : true,
            "autoOpen"      : false,
            "resizable"     : false, 
            "dialogClass"   : 'smartindex-input-dialog'
        }
    );
    jQuery("#smartindex-input-dialog-tpl .input-value").keypress(function(e) {
        if (e.which == 13) {
            e.preventDefault();
            var buttons = jQuery("#smartindex-input-dialog-tpl").dialog("option", "buttons");
            buttons[1].click.apply(jQuery("#smartindex-input-dialog-tpl"));
        }
        
    });
}

function SI_initContextMenu() {
    jQuery.contextMenu({
        selector: '.context-menu-one',
        callback: function(key, options) {
            var m = "clicked: " + key;
            window.console && console.log(m) || alert(m);
        },
        items: {
            "edit": {name: "Edit", icon: "edit"},
            "cut": {name: "Cut", icon: "cut"},
            copy: {name: "Copy", icon: "copy"},
            "paste": {name: "Paste", icon: "paste"},
            "delete": {name: "Delete", icon: "delete"},
            "sep1": "---------",
            "quit": {name: "Quit", icon: function(){
                    return 'context-menu-icon context-menu-icon-quit';
                }}
        }
    });
}


function SI_openInputDialog(data, title, info, okHandler) {
    jQuery("#smartindex-input-dialog-tpl").dialog("option", "title", title);
    jQuery("#smartindex-input-dialog-tpl .info-text").html(info);   
    jQuery("#smartindex-input-dialog-tpl .input-value").val("");
    jQuery("#smartindex-input-dialog-tpl").dialog(
        {
            "buttons": 
            [
                {
                    "text"  : "cancel",
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
    var regexS = "[\\?&]" + param + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(url);
    if (results == null) {
        return "";
    } else {
        return decodeURIComponent(results[1].replace(/\+/g, " "));
    }
}

function si_context_newPage(e, c) {
     var namespace = SI_getURLParameter(jQuery(c).children("a").attr("href"), "idx");
     var title = jQuery(c).children("a").html();
     SI_openInputDialog(namespace, "Create new page in \""+title+"\"", "Enter page name to create:", function(data, input) {
         window.location = DOKU_BASE+"doku.php?do=edit&id="+data+":"+input;
     });
 }

 function si_context_search(e, c) {
     var namespace = SI_getURLParameter(jQuery(c).children("a").attr("href"), "idx");
     var title = jQuery(c).children("a").html();     
     SI_openInputDialog(namespace, "Search in \""+title+"\"", "Enter phrase to search in namespace:", function(data, input) {
         window.location = DOKU_BASE+"doku.php?do=search&id="+input+" @"+data;
     });

 }

 function si_context_showAcl(e, c) {
     var namespace = SI_getURLParameter(jQuery(c).children("a").attr("href"), "idx");
     window.location = DOKU_BASE+"doku.php?do=admin&page=acl&id="+namespace;
 }
 
 function si_context_pageInfo(e, c) {
     var page = SI_getURLParameter(jQuery(c).children("a").attr("href"), "id");
     jQuery('body').append('<div id="xxx"></div>');
     jQuery('#xxx').load('/lib/plugins/smartindex/exe/ajax.php', {action: "pageinfo", "page":page});
     jQuery('#xxx').dialog();
 }

/*
 function si_initContextMenu() {
     var set_folder = {
         "useWrapper": true,
         "id" : "menu1", 
         "selectors" : [
             {
                 "selector"  : ".smartindex-treeview li.namespace>div", 
                 "event"     : "contextmenu"
             },
         ],
         "items": [
             {
                 "id"    : "newpage", 
                 "text"  : "new page", 
                 "fn"    : si_context_newPage
             }, 
             {
                 "id"    : "search", 
                 "text"  : "search", 
                 "fn"    : si_context_search
             },
             {
                 "id "   : "acl", 
                 "text"  : "acl", 
                 "fn"    : si_context_showAcl
             },
         ]
     };

     jg_buildContext(set_folder);
     
     var set_file = {
         "useWrapper": true,
         "id" : "menu2", 
         "selectors" : [
             {
                 "selector"  : ".smartindex-treeview li.page>div", 
                 "event"     : "contextmenu"
             },
         ],
         "items": [
             {
                 "id"    : "pageinfo", 
                 "text"  : "page info", 
                 "fn"    : si_context_pageInfo
             }
         ]
     };
     jg_buildContext(set_file);
 }
 */