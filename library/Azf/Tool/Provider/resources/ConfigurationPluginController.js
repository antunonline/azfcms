define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!../resource/i18n/nls/<?=$ucName?>',
    ],function
(declare, lang, model, storeRegistry,
    nls)
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
        }
            
    });
    
    return _class;
})