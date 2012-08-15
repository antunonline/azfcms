define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'azfcms/store/registry!AclGroup'],function
(declare, lang, model, storeRegistry,
    nls,aclGroupStore)
{
    var _class = declare([],{
        nls:nls,
        constructor:function(args){
            lang.mixin(this,args||{});
            if(!this.model){
                this.model = model;
            }
            
            if(!this.aclGroupStore){
                this.aclGroupStore = aclGroupStore;
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
            this._initToolbarButtons();
        },
        
        _initToolbarButtons:function(){
            this.view.addToolbarButton({
                label:"New Group",
                onClick:lang.hitch(this,'createNewGroupForm')
            })
        },
        
        createNewGroupForm:function(){
            var form = this.view.createNewGroupForm();
            form.on('save',lang.hitch(this,function(group){
                this.aclGroupStore.put(group).then(lang.hitch(this,function(response){
                    if(response.status){
                        this.view.removeChild(form);
                        form.destroyRecursive();
                    } else {
                        form.set("messages",response.errors);
                    }
                }))
            }))
        }
            
    });
    
    return _class;
})