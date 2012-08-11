define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/UserManagment'],function
(declare, lang, model, storeRegistry,
    nls)
{
    var _class = declare([],{
        nls:nls,
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