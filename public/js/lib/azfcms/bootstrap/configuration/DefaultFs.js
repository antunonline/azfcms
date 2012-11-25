{
    i18nButtonLabelPointer: 'npFsAction',
    i18nBbuttonFallbackLabel: 'Upravljanje Dadotekama',
    iconClass:'some icon',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/module/default/configurationPlugin/fs/Fs',
                'azfcms/module/default/store/FSStore',
            ],function
            (Controller, FSStore
                ){
                context.Controller = Controller;
                context.FSStore = FSStore;
                            
                initCallback();
            })
    },
    callback: function(item){
        var controller = new this.Controller({
            model:new this.FSStore()
        });
        this.adminDialog.addChild(controller);
                        
    }
}