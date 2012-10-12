{
    i18nButtonLabelPointer: 'npNewsAction',
    i18nBbuttonFallbackLabel: 'News',
    iconClass:'some icon...',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/module/vrwhr/configurationPlugin/news/view/News','azfcms/module/vrwhr/configurationPlugin/news/controller/News'
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
        var controller = new this.Controller({
            view:view
        });
                        
    }
}