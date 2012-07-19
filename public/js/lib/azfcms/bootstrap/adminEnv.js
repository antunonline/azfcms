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
                        },adminDialog,item);
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
                    i18nButtonLabelPointer: 'npCreatePageAction',
                    iconClass:'dijitIconNewTask',
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
                },
                {
                    i18nButtonLabelPointer: 'npEditPageAction',
                    iconClass:'dijitIconEdit',
                    init: function(initCallback, adminDialog){
                        this.ad = adminDialog;
                        var self = this;
                        require(['azfcms/view/navigation/ContentEdit','azfcms/controller/navigation/ContentEdit','azfcms/model'],function
                            (cep,cec,model){
                                self.model = model;
                                self.CEP = cep;
                                self.CEC = cec;
                                
                                self.typeStore = model.prepareLangStore('cms.pluginDescriptor.getContentPlugins()');
                                self.typeStore.idProperty = "pluginIdentifier";
                                self.typeStore.query().then(function(){
                                    initCallback();
                                })
                                
                            })
                        
                    },
                    callback: function(item){
                        var cep = new this.CEP({
                            typeStore:this.typeStore,
                            title:"Editor za \""+item.title+"\""
                        });
                        var cec = new this.CEC();
                        cec.init(item,cep);
                        this.ad.addChild(cep);
                    }
                },
                {
                    i18nButtonLabelPointer: 'npHomeChange',
                    iconClass:'dijitIconBookmark',
                    init: function(initCallback,adminDialog, item){
                        var self = this;
                        require(
                            ['azfcms/controller/navigation/HomePage'],function
                            (HPC){
                                self.hpc = new HPC();
                                initCallback()
                            })
                    },
                    callback: function(item){
                        this.hpc.onHomeChange(item);
                    }
                },
                
                {
                    i18nButtonLabelPointer: 'npDeletePageAction',
                    iconClass:'dijitIconDelete',
                    init: function(initCallback,adminDialog){
                        var self = this;
                        require(
                            ['azfcms/view/navigation/DeletePageDialog','azfcms/controller/navigation/DeletePage',
                            'azfcms/model'],function
                            (DPD,DPC,
                                model){
                                var view = new DPD();
                                self.controller = new DPC({
                                    view:view
                                });
                            
                                initCallback();
                            })
                    },
                    callback: function(item){
                        if(item){
                            this.controller.activate(item);
                        }
                        
                    }
                },
                {
                    i18nButtonLabelPointer: 'npPagePluginsAction',
                    iconClass:'dijitIconDocument',
                    init: function(initCallback,adminDialog, item){
                        var self = this;
                        require(
                            ['azfcms/view/ExtensionEditorPane','azfcms/controller/ExtensionEditorController',
                            'azfcms/model','azfcms/model/cms','dojo/query'],function
                            (EEP,EEC,
                                model,cms,query){
                                var requireLink = "<link rel='stylesheet' type='text/css' href='"+require.toUrl('')+'/dojox/grid/resources/claroGrid.css'+"' />";
                                requireLink += "<link rel='stylesheet' type='text/css' href='"+require.toUrl('')+'/dojox/grid/resources/Grid.css'+"' />";
                                query("head").addContent(requireLink);
                                self.EEP = EEP;
                                self.EEC = EEC;
                                self.cms = cms;
                                self.adminDialog = adminDialog;
                                self.typeStore = cms.getExtensionPluginStore();
                                self.typeStore.query({}).then(function(){
                                    initCallback();
                                    
                                })
                                
                            })
                    },
                    callback: function(item){
                        if(item){
                            var cms = this.cms;
                            var view = new this.EEP({
                                regionStore:cms.getTemplateRegionsForNavigationStore(item.id),
                                gridStore:cms.getRegionPluginsStore(item.id, ""),
                                typeStore:this.typeStore,
                                closable:true,
                                title:"Pluginovi za \""+item.title+"\""
                            });
                            this.adminDialog.addChild(view);
                            new this.EEC({
                                editorPane:view,
                                navigationId:item.id,
                                model:cms
                            });
                        }
                        
                    }
                },
                {
                    i18nButtonLabelPointer: 'npFilesystemAction',
                    iconClass:'dijitIconDocument',
                    init: function(initCallback,adminDialog, item){
                        var self = this;
                        require(
                            ['azfcms/view/FilesystemPane','azfcms/controller/FilesystemPaneController',
                            'azfcms/store/Filesystem','dojo/store/Observable'],function
                            (FP,FPC,FSStore, Observable){
                                self.adminDialog = adminDialog;
                                self.FP = FP
                                self.FPC = FPC;
                                self.gridStore = new FSStore({
                                    
                                    })
                                self.treeStore =new Observable( new FSStore({
                                    getOptions:{
                                        file:false
                                    },
                                    queryOptions:{
                                        file:false
                                    },
                                    isTreeModel:true
                                }));
                                initCallback()
                            })
                    },
                    callback: function(item){
                        var fp = new this.FP({
                            closable:true,
                            title:nls.npFilesystemAction,
                            gridStore:this.gridStore,
                            treeStore:this.treeStore
                        });
                        var fpc = new this.FPC({
                            view:fp
                        })
                        
                        this.adminDialog.addChild(fp);
                        fp.resize();
                        
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


