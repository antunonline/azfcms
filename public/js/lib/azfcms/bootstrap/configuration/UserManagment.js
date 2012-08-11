{
    i18nButtonLabelPointer: 'npUserManagmentAction',
    i18nBbuttonFallbackLabel: 'UserManagment',
    iconClass:'dijitIconNewTask',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/view/configuration/UserManagment','azfcms/controller/configuration/UserManagment',
                'azfcms/store/registry!UsersStore'
            ],function
            (View,Controller,
            userStore
                ){
                context.View = View;
                context.Controller = Controller;
                context.userStore = userStore;
                            
                initCallback();
            })
    },
    callback: function(item){
        var view = new this.View({
        });
        this.adminDialog.addChild(view);
        var controller = new this.Controller({
            userStore:this.userStore,
            view:view
        });
                        
    }
}