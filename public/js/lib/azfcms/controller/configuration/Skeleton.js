define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry'],function
(declare, lang, model, storeRegistry)
{
    var _class = declare([],{
        constructor:function(args){
            lang.mixin(this,args|{});
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
            // In this method you can initialize the view
        }
            
    });
    
    return _class;
})