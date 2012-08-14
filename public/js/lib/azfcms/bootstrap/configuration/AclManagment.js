{
    i18nButtonLabelPointer: 'npAclManagmentAction',
    i18nBbuttonFallbackLabel: 'AclManagment',
    iconClass:'dijitIconNewTask',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/view/configuration/AclManagment','azfcms/store/registry!:UserAclGroup'
            ],function
            (View,userAclGroupStore
                ){
                context.View = View;
                context.userAclGroupStore = userAclGroupStore;
                            
                initCallback();
            })
    },
    callback: function(item){
        var view = new this.View({
            userAclGroupStore:this.userAclGroupStore
        });
        this.adminDialog.addChild(view);
        
                        
    }
}