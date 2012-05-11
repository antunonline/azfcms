define(['dojo/_base/declare','azfcms/view/AdminDialog','azfcms/view/NavigationPane',
'azfcms/model/navigation','azfcms/controller/Context'],function
(declare, AdminDialog, NavigationPane,
navigationModel, ContextController)
{
    var _class = declare([],{
        constructor: function(){
            // Admin dialog
            this.adminDialog = new AdminDialog();
            
            
            /**
             * Initialize navigation pane
             * 
             */
            this.navigationPane = new NavigationPane({
                model:navigationModel
            });
            this.adminDialog.addChild(this.navigationPane);
            
            /**
             * Context controller
             */
            this.contextController = new ContextController(this.adminDialog,this.navigationPane);
        }
    });
    
    var instance = new _class();
    // Store class reference
    instance._class = _class;
    return instance;
})


