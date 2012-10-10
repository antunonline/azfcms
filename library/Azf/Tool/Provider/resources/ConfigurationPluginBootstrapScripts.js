{
    i18nButtonLabelPointer: 'np<?=$ucName?>Action',
    i18nBbuttonFallbackLabel: '<?=$ucName?>',
    iconClass:'some icon...',
    init: function(initCallback,adminDialog){
        var context = this;
        context.adminDialog = adminDialog;
        require(
            ['azfcms/module/<?=$module?>/configurationPlugin/<?=$name?>/view/<?=$ucName?>','azfcms/module/<?=$module?>/configurationPlugin/<?=$name?>/controller/<?=$ucName?>'
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