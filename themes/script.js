/*
 * SmartIndex initialization.
 */
jQuery(function(){
    si_init();
});

/*
 * SmartIndex base functions.
 */

function si_init() {
    jQuery.each(jQuery("div.smartindex-treeview"), function(k, v){
        si_initTree(v);
    });
    si_initContextMenu();
    si_initInputDialog();
}

function si_initTree(tree) {
    var cnf = si_getTreeConf(tree);
    var $id = jQuery(tree).attr("id");
    jQuery.each(cnf.rawEvents, function(k, v){
        jQuery(tree).delegate("#"+$id+" "+v.selector, v.event, cnf, window[v.fn]);
    });
}

function si_getTreeConf(tree) {
    return window[jQuery(tree).attr("id")+"_conf"];
}

/*
 * SmartIndex default event handlers
 */

function si_default_openFolder(event) {
    if (event.data.beforeOpenFolder != null) 
        if (!window[event.data.beforeOpenFolder](event))
            return false;
   
    var $link = jQuery(this).children("a");
    var $li = jQuery(this).closest("li");
    
    if ($li.children("ul").length == 0) {
        if ($li.hasClass("waiting")) {
            return false;
        }
        $li.addClass("waiting");
        var ajaxData = 
            {
                "namespace" : url_getParameter($link.attr("href"), "idx"), 
                "depth"     : event.data.depth, 
                "theme"     : event.data.theme, 
                "action"    : "subtree"
            };
    
        if (event.data.beforeSubTreeLoad != null)
            window[event.data.beforeSubTreeLoad](event, ajaxData);
        
        jQuery.post(event.data.url, ajaxData, function(data){
            if (event.data.handleSubTreeLoad != null) {
                window[event.data.handleSubTreeLoad](event, data);
            } else {
                $li.append(data);
                $li.removeClass("waiting");
            }
        });
    }
    $li.removeClass("closed").addClass("open");
    
    if (event.data.afterOpenFolder != null) window[event.data.afterOpenFolder](event);
    
    return false;
}

function si_default_closeFolder(event) {
    if (event.data.beforeCloseFolder != null) 
       if (!window[event.data.beforeCloseFolder](event))
           return false;
    
    
    jQuery(this).parent().removeClass("open").addClass("closed");
    
    if (event.data.afterCloseFolder != null) window[event.data.afterCloseFolder](event);
    
    return false;
}

function si_default_openPage(event) {
    var $link = jQuery(this).children("a");
    window.location = $link.attr("href");
}

function si_initInputDialog() {
    jQuery('body').append('<div id="smartindex-input-dialog-tpl"><p class="info-text"></p><input type="text" name="input-value" class="input-value" /></div>');
    jQuery("#smartindex-input-dialog-tpl").dialog(
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


function si_openInputDialog(data, title, info, okHandler) {
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
                    "text"  : "ok",
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
function url_getParameter(url, param) {
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
     var namespace = url_getParameter(jQuery(c).children("a").attr("href"), "idx");
     var title = jQuery(c).children("a").html();
     si_openInputDialog(namespace, "Create new page in \""+title+"\"", "Enter page name to create:", function(data, input) {
         window.location = DOKU_BASE+"doku.php?do=edit&id="+data+":"+input;
     });
 }

 function si_context_search(e, c) {
     var namespace = url_getParameter(jQuery(c).children("a").attr("href"), "idx");
     var title = jQuery(c).children("a").html();     
     si_openInputDialog(namespace, "Search in \""+title+"\"", "Enter phrase to search in namespace:", function(data, input) {
         window.location = DOKU_BASE+"doku.php?do=search&id="+input+" @"+data;
     });

 }

 function si_context_showAcl(e, c) {
     var namespace = url_getParameter(jQuery(c).children("a").attr("href"), "idx");
     window.location = DOKU_BASE+"doku.php?do=admin&page=acl&id="+namespace;
 }
 
 function si_context_pageInfo(e, c) {
     var page = url_getParameter(jQuery(c).children("a").attr("href"), "id");
     jQuery('body').append('<div id="xxx"></div>');
     jQuery('#xxx').load('/lib/plugins/smartindex/exe/ajax.php', {action: "pageinfo", "page":page});
     jQuery('#xxx').dialog();
 }

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
 
 
 function addBtnActionClick1($btn, props, edid) {
    $btn.click(function() {
        alert('hey you clicked me');
        return false;
    });
 
    return true;
}
 
jQuery(function(){
    if (window.toolbar != undefined) {
        window.toolbar[window.toolbar.length] = {
            "type":"Click1", // we have a new type that links to the function
            "title":"Hey Click me!",
            "icon":"../../plugins/smartindex/themes/tree/img/minus.gif"
        };
    }
});