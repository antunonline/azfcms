{
    i18nButtonLabelPointer: 'npAclManagmentAction',
    i18nBbuttonFallbackLabel: 'AclManagment',
    iconClass:'dijitIconKey',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/module/default/configurationPlugin/aclManagment/view/AclManagment','azfcms/module/default/configurationPlugin/aclManagment/controller/AclManagment',
                'azfcms/store/registry!:UserAclGroup','azfcms/store/registry!:AclAclGroup','azfcms/store/registry!:AclGroup'
            ],function
            (View,Controller,
            userAclGroupDataStore,aclAclGroupDataStore,aclGroupDataStore
                ){
                context.View = View;
                context.Controller = Controller;
                context.userAclGroupDataStore =userAclGroupDataStore;
                context.aclAclGroupDataStore  =aclAclGroupDataStore;
                context.aclGroupDataStore    =aclGroupDataStore;
                            
                initCallback();
            })
    },
    callback: function(item){
        var view = new this.View({
            userAclGroupStore:this.userAclGroupDataStore,
            aclAclGroupStore:this.aclAclGroupDataStore,
            aclGroupStore:this.aclGroupDataStore
        });
        this.adminDialog.addChild(view);
        
        new this.Controller({
            view:view,
            userAclGroupStore:this.userAclGroupDataStore.store
        })
        
                        
    }
}