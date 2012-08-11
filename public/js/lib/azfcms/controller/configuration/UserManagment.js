define(
['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/UserManagment',
    'azfcms/view/configuration/UserManagment/UserForm','azfcms/view/configuration/UserManagment/UserGrid'],function
(declare, lang, model, storeRegistry,
    nls, UserForm, UserGrid)
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
            userForm.on('save',lang.hitch(this,'doCreate'));
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
                title:this.selectedUser.loginName
            });
            userForm.set('value',this.selectedUser);
            userForm.on('save',lang.hitch(this,'doSave'));
            this.view.addChild(userForm);
            
        },
        
        doSave:function(user){
            this.userStore.put(user).then(lang.hitch(this,function(){
                this.userGridView.reloadGrid();
            }));
        },
        
        doCreate:function(user){
            this.userStore.add(user).then(lang.hitch(this,function(){
                this.userGridView.reloadGrid();
            }));
        }
            
    });
    
    return _class;
})