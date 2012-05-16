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
                var adminDialog = this.adminDialog;
                for(var i in actionDefinitions){
                    // Load current definition
                    var definition = actionDefinitions[i];
                    // Load label
                    var label = nls[definition.i18nButtonLabelPointer];
                    // Load icon class
                    var iconClass = definition.iconClass;
                    // Create callback
                    var callback = this._buildCallback(definition,adminDialog);
                
                    // 
                    this.navigationPane.addButton(label,callback,iconClass);
                }
            },
            
            _buildCallback: function(definition,adminDialog){
                return function(item){
                    // If init is not called
                    if('isInit' in definition == false){
                        definition.isInit=false;
                        definition.d = new Deferred();
                        // Call init with callback function as the first arg
                        definition.init(function(){
                            definition.d.isInit=false;
                            definition.d.callback()
                        },adminDialog);
                        // When action is initialize, execute callback
                        definition.d.then(function(){
                            definition.i=true;
                            definition.callback(item);
                        })
                        
                        // If init is called
                    } else {
                        if(definition.isInit==false){
                            definition.d.then(function(){
                                definition.callback(item);
                            })
                            definition.isInit=true;
                        } else {
                            definition.callback(item);
                        }
                    }
                }
            },
        
        
            _getNavigationActionDefinitions: function(){
                return [
                {
                    i18nButtonLabelPointer: 'npEditPageAction',
                    iconClass:'dijitIconEdit',
                    init: function(initCallback, adminDialog){
                        this.ad = adminDialog;
                        var self = this;
                        require(['azfcms/view/navigation/ContentEdit','azfcms/controller/navigation/ContentEdit'],function
                            (cep,cec){
                                self.CEP = cep;
                                self.CEC = cec;
                                initCallback();
                            })
                        
                    },
                    callback: function(item){
                        var cep = new this.CEP();
                        var cec = new this.CEC();
                        cec.init(item,cep);
                        this.ad.addChild(cep);
                    }
                },
                {
                    i18nButtonLabelPointer: 'npCreatePageAction',
                    iconClass:'dijitIconDocuments',
                    init: function(initCallback,adminDialog){
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


