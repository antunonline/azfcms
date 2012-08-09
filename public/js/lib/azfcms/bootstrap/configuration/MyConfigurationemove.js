{
    i18nButtonLabelPointer: 'npCreatePageAction',
    iconClass:'dijitIconNewTask',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/view/configuration/MyConfigurationemove','azfcms/controller/configuration/MyConfigurationemove'
            ],function
            (View,Controller
                ){
                context.View = View;
                context.Controller = Controller;
                            
                initCallback();
            })
    },
    callback: function(item){
        var view = new this.View();
        this.adminDialog.addChild(view);
        var controller = new Controller({
            view:view
        });
                        
    }
}