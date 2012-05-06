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
             * Context controller
             */
            this.contextController = new ContextController(this.adminDialog);
            
            /**
             * Initialize navigation pane
             * 
             */
            var navigationPane = new NavigationPane({
                model:navigationModel,
                controller:this.contextController
            });
            this.adminDialog.addChild(navigationPane);
        }
    });
    
    var instance = new _class();
    // Store class reference
    instance._class = _class;
    return instance;
})


