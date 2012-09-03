define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!../resource/i18n/nls/UserManagment',
    '../view/UserForm','../view/UserGrid',
    'azfcms/module/default/view/util','dojo/string'],function
(declare, lang, model, storeRegistry,
    nls, UserForm, UserGrid,viewUtil,string)
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
            
            /**
             * User store
             */
            this.userStore;
            /**
             * User grid view 
             */
            this.userGridView;
            
            /**
             * Selected user record, null if none is selected
             */
            this.selectedUser = null;
            
            this.init();
        },
        
        init:function(){
            this._initUserGrid();
            
            
            this.view.addActionButton(this.nls.newUserButtonLabel,lang.hitch(this,'onNew'))
            this.view.addActionButton(this.nls.editUserButtonLabel,lang.hitch(this,'onEdit'))
            this.view.addActionButton(this.nls.deleteUserButtonLabel,lang.hitch(this,'onRemove'))
        },
        
        _initUserGrid:function(){
            this.userGridView = new UserGrid({
                store:this.userStore
            });
            this.userGridView.on("select",lang.hitch(this,function(user){
                this.selectedUser = user;
            }))
            this.userGridView.on("edit",lang.hitch(this,'onEdit'))
            this.view.addChild(this.userGridView);
        },
        
        
        onNew:function(){
            var userForm = new UserForm({
                title:this.nls.newUserFormTabTitle
            });
            userForm.on('save',lang.hitch(this,function(user){
                this.doCreate(user,userForm);
            }));
            this.view.addChild(userForm);
        },
        
        /**
         * 
         */
        onEdit:function(){
            if(!this.selectedUser){
                return;
            }
            
            var userForm = new UserForm({
                title:string.substitute(this.nls.editUserTabLabel,this.selectedUser)
            });
            userForm.set('value',this.selectedUser);
            userForm.on('save',lang.hitch(this,function(response){
                this.doSave(response,userForm);
            }));
            this.view.addChild(userForm);
            
        },
        
        onRemove:function(){
            if(!this.selectedUser){
                return;
            }
            
            viewUtil.confirm(lang.hitch(this,function(confirmed){
                if(confirmed){
                    this.doRemove(this.selectedUser);
                }
            }),this.nls.confirmDeleteMessage,this.selectedUser);
        },
        
        doSave:function(user,userForm){
            this.userStore.put(user).then(lang.hitch(this,function(response){
                if(response.status==false){
                    userForm.set("messages",response.errors);
                } else {
                    userForm.set("messages",{});
                    this.userGridView.reloadGrid();
                }
            }));
        },
        
        doCreate:function(user,userForm){
            this.userStore.add(user).then(lang.hitch(this,function(response){
                if(response.status == false){
                    userForm.set("messages",response.errors);
                } else {
                    userForm.set("messages",{});
                    this.view.tabContainer.removeChild(userForm);
                    this.userGridView.reloadGrid();
                }
            }));
        },
        
        doRemove:function(selectedUser){
            this.userStore.remove(selectedUser).then(lang.hitch(this,function(response){
                if(response.status){
                    this.selectedUser=null;
                    this.userGridView.reloadGrid();
                }
            }))
        }
            
    });
    
    return _class;
})