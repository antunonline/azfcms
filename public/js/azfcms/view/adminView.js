/* 
 * @author Antun Horvat
 */
define(['azfcms','dijit/Dialog', 'dijit/layout/TabContainer',
      'dijit/layout/ContentPane' ], 
function(azfcms,    Dialog,             TabContainer,
        ContentPane ){
    
    var dialog = new Dialog({
        style:"width:600px;height: 400px;",
        title:"Admin Console"
    });
    
    // Initialize tab container
    var tabContainer = new TabContainer({
        style:"width:100%;height:100%"
    });
    dialog.containerNode.appendChild(tabContainer.domNode);
    tabContainer.startup();
    
    
    // Initialize first panel
    var frontPanel = new ContentPane({
        content:"HELLO",
        title:"Front panel"
    });
    tabContainer.addChild(frontPanel);
    
    
    // Initialize container
    
    tabContainer.startup();
    return {
        _dialog: dialog,
        show: function(){
            this._dialog.show();
        }
    }
})


