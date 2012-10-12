define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!../resource/i18n/nls/News','azfcms/module/vrwhr/configurationPlugin/news/view/NewsGrid',
    'dojo/data/ObjectStore', 'azfcms/module/vrwhr/store/NewsCategory'
    ],function
(declare, lang, model, storeRegistry,
    nls, NewsGrid, ObjectStore, store)
{
    var _class = declare([],{
        nls:nls,
        constructor:function(args){
            lang.mixin(this,args||{});
            if(!this.model){
                this.model = model;
            }
            
            /**
             * Model reference
             */
            this.model;
            
            /**
             * View reference
             */
            this.view 
            
            this.init();
        },
        
        init:function(){
            this.view.addChild(new NewsGrid({
                store:new ObjectStore({
                    objectStore:new store({})
                })
            }));
        }
            
    });
    
    return _class;
})