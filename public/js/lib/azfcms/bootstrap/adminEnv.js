define(['dojo/_base/declare','azfcms/view/AdminDialog','azfcms/view/NavigationPane',
    'azfcms/model/navigation','azfcms/controller/Context','dojo/i18n!azfcms/resources/nls/view',
    'dojo/_base/Deferred'],function
    (declare, AdminDialog, NavigationPane,
        navigationModel, ContextController, nls,
        Deferred)
        {
        var _class = declare([],{
            constructor: function(){
            
                // Admin dialog
                this.adminDialog = new AdminDialog();
            
            
                /**
             * Initialize navigation pane
             * 
             */
                this.navigationPane = new NavigationPane({
                    model:navigationModel
                });
                this.adminDialog.addChild(this.navigationPane);
            
                /**
             * Context controller
             */
                this.contextController = new ContextController(this.adminDialog,this.navigationPane);
            
                /**
             * Build navigation actions
             * 
             */
                this._constructNavigationActions();
            },
        
            /**
         * Build navigation actions
         */
            _constructNavigationActions: function(){
            
                var actionDefinitions = this._getNavigationActionDefinitions();
                var navigationPane = this.navigationPane;
                var context = {};
                for(var i in actionDefinitions){
                    var definition = actionDefinitions[i];
                    var label = nls[definition.i18nButtonLabelPointer];
                    var iconClass = definition.iconClass;
                    context[i]={};
                    var storage = context[i];
                
                    var callback = function(item){
                    
                        if('isInit' in storage == false){
                            storage.isInit=false;
                            storage.d = new Deferred();
                            definition.init(function(){
                                storage.d.callback()
                                },navigationPane);
                            storage.d.then(function(){
                                storage.i=true;
                                definition.callback(item);
                            })
                        } else {
                            if(storage.isInit==false){
                                storage.d.then(function(){
                                    definition.callback(item);
                                })
                            } else {
                                definition.callback(item);
                            }
                        }
                    }
                
                    this.navigationPane.addButton(label,callback,iconClass);
                }
            },
        
        
            _getNavigationActionDefinitions: function(){
                return [
                {
                    i18nButtonLabelPointer: 'npCreatePageAction',
                    iconClass:'dijitIconEdit',
                    init: function(initCallback,navigationPane){
                        var context = this;
                        require(
                            ['azfcms/view/navigation/CreatePageDialog','azfcms/controller/navigation/CreatePage',
                            'azfcms/model'],function
                            (CPD,CPC,
                            model){
                                context.cpd = new CPD({
                                    store:model.prepareLangStore('cms.pluginDescriptor.getContentPlugins()')
                                });
                                context.cpc = new CPC(context.cpd);
                            
                                initCallback();
                            })
                    },
                    callback: function(item){
                        if(item){
                            this.cpc.show(item);
                        }
                        
                    }
                }
                ];
            }
        });
    
        var instance = new _class();
        // Store class reference
        instance._class = _class;
        return instance;
    })


