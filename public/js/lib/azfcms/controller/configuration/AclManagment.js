define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'azfcms/store/registry!AclGroup','dojo/string','azfcms/view/util'],function
(declare, lang, model, storeRegistry,
    nls,aclGroupStore,string,util)
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
                label:this.nls.aclManagmentNewGroupButtonLabel,
                onClick:lang.hitch(this,'createNewGroupForm')
            })
            this.view.addToolbarButton({
                label:this.nls.aclManagmentEditGroupButtonLabel,
                onClick:lang.hitch(this,'editGroupForm')
            })
            this.view.addToolbarButton({
                label:this.nls.aclManagmentDeleteGroupButtonLabel,
                onClick:lang.hitch(this,'deleteGroup')
            })
        },
        
        createNewGroupForm:function(){
            var form = this.view.createNewGroupForm();
            
            form.on('save',lang.hitch(this,function(group){
                this.aclGroupStore.add(group).then(lang.hitch(this,function(response){
                    if(response.status){
                        this.view.removeChild(form);
                        form.destroyRecursive();
                    } else {
                        form.set("messages",response.errors);
                    }
                }))
            }))
        },
        
        editGroupForm:function(){
            var group = this.view.get("selectedRow");
            if(!group){
                return;
            }
            var form = this.view.createNewGroupForm({
                title:string.substitute(this.nls.aclManagmentEditGroupTabName,group)
            });
            
            form.set('value',group);
            form.on("save",lang.hitch(this,function(group){
                this.aclGroupStore.put(group).then(lang.hitch(this,function(response){
                    if(response.status){
                        this.view.removeChild(form);
                        form.destroyRecursive();
                    } else {
                        form.set("messages",response.errors);
                    }
                }))
            }))
        },
        
        deleteGroup:function(){
            var group = this.view.get("selectedRow");
            if(!group){
                return;
            }
            
            util.confirm(lang.hitch(this,function(confirmed){
                if(confirmed){
                    this.aclGroupStore.remove(group);
                    this.view.reloadAclGroupGrid();
                        
                }
            }),this.nls.aclManagmentConfirmDelete,group);
        }
            
    });
    
    return _class;
})