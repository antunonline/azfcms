define(
    ['dojo/_base/declare', 'dojo/_base/lang','azfcms/model','azfcms/store/registry',
    'dojo/i18n!../resource/i18n/nls/<?=$ucName?>','azfcms/module/<?=$ucModule?>/configurationPlugin/<?=$name?>/view/ReplaceMeGrid',
    'dojo/data/ObjectStore', 'azfcms/module/<?=$ucModule?>/store/ReplaceMeCategory','azfcms/module/<?=$ucModule?>/configurationPlugin/<?=$name?>/view/ReplaceMeForm'
    ],function
    (declare, lang, model, storeRegistry,
        nls, ReplaceMeGrid, ObjectStore, ReplaceMeStore, ReplaceMeForm)
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
             * news store reference
             */
                this.store = new ReplaceMeStore({});
            
                /**
             * View reference
             */
                this.view 
            
                /**
             * News grid reference
             */
                this.newsGrid;
            
                this.init();
            },
        
            _getGridSelection:function(){
                return this.newsGrid.getSelection();
            },
        
            init:function(){
                this.newsGrid = new ReplaceMeGrid({
                    store:new ObjectStore({
                        objectStore:this.store
                    }),
                    onRowSelect:lang.hitch(this,"doEdit")
                });
                this.view.addChild(this.newsGrid);
            
            
                this.view.on("create",lang.hitch(this,'_createNewForm'))
                this.view.on("edit",lang.hitch(this,'_createEditForm'))
                this.view.on("delete",lang.hitch(this,'doDelete'))
            },
        
        
            doEdit:function(recordId){
                this.store.get(recordId).then(lang.hitch(this,'_doEdit'));
            },
        
            _doEdit:function(response){
                if(response.status){
                    var form = new ReplaceMeForm({
                        onSave:lang.hitch(this,function(value){
                            this._doSave(form,value);
                        })
                    });
                    form.set('value',response.response);
                    this.view.addChild(form);
                }
            },
        
            _doSave:function(form, value){
                form.set("disabled",true);
                this.store.put(value).then(lang.hitch(this,function(response){
                    if(response.status){
                        this.view.removeChild(form)
                    } else {
                        form.set("messages",response.errors);
                        form.set("disabled",false);
                    }
                }))
            },
            
            _createNewForm:function(){
                var form = new ReplaceMeForm({
                    layout:ReplaceMeForm.prototype.LAYOUT_NEW,
                    onSave:lang.hitch(this,function(value){
                        this._doCreate(form,value);
                    })
                });
                
                this.view.addChild(form);
            },
            
            _createEditForm:function(){
                var selection = this._getGridSelection()
                if(selection.length>0){
                    for(var i = 0,len=selection.length;i<len;i++){
                        this.doEdit(selection[i].id);
                    }
                }
            },
        
            _doCreate:function(form, value){
                form.set("disabled",true);
                this.store.add(value).then(lang.hitch(this,function(response){
                    if(response.status){
                        this.view.removeChild(form);
                    } else {
                        form.set('messages',response.errors);
                        form.set('disabled',false);
                    }
                }))
            },
            
            doDelete:function(){
                var selection = this._getGridSelection();
                if(selection.length>0){
                    for(var i = 0,len=selection.length;i<len;i++){
                        this.store.remove(selection[i]);
                    }
                }
            }
        
        
            
        });
    
        return _class;
    })