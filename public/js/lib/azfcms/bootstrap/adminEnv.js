define(['dojo/_base/declare','azfcms/module/default/view/AdminDialog','azfcms/module/default/view/NavigationPane',
    'azfcms/model/navigation','azfcms/module/default/controller/Context','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common',
    'dojo/_base/Deferred','dojo/on','azfcms/model!cms.admin.getConfigurationActionDefinitions()',
    'dojo/_base/json','dojo/on','dojo/dom-geometry'],function
    (declare, AdminDialog, NavigationPane,
        navigationModel, ContextController, nls,
        Deferred,on, serializedConfigurationDefinitions,
        json, on, domGeometry)
        {
        var _class = declare([],{
            startup: function(dstNode){
            
                // Admin dialog
                this.adminDialog = new AdminDialog({
                    style:"width:100%;height:100%"
                },dstNode);
                this.adminDialog.startup();
            
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
                var d = new Deferred();
                // Delay adminDialog resize call until the rootNode is loaded
                var self = this;
                navigationModel.getRoot(function(){
                    self.adminDialog.resize();
                });
                
                var self = this;
                // Setup onresize event
                on(window,'resize',function(){
                    self.onWindowResize();
                })
                
                return d;
            },
            
            
            onWindowResize: function(){
                if(this._resizing){
                    return;
                } else {
                    this._resizing =true;
                }
                var self = this;
                
                window.setTimeout(function(){
                    
                    self.resize(domGeometry.getContentBox(document.body));
                    self._resizing = false;
                },400);
                
                return ;
            },
            
            resize:function(){
                this.adminDialog.resize(arguments.length>0?arguments[0]:null);
            },
        
            /**
         * Build navigation actions
         */
            _constructNavigationActions: function(){
            
                var actionDefinitions = this._getConfigurationActionDefinitions();
                var adminDialog = this.adminDialog;
                for(var i in actionDefinitions){
                    // Load current definition
                    var definition = actionDefinitions[i];
                    // Load label
                    if(nls[definition.i18nButtonLabelPointer]){
                        var label = nls[definition.i18nButtonLabelPointer];
                    } else {
                        var label = definition.i18nBbuttonFallbackLabel;
                    }
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
        
        
            _getConfigurationActionDefinitions: function(){
                var definitions = [];
                var deserialized = [];
                for(var i = 0, len = serializedConfigurationDefinitions.length;i<len;i++){
                    deserialized = json.fromJson(serializedConfigurationDefinitions[i]);
                    
                    if(deserialized instanceof Array){
                        for(var i1 = 0, len1=deserialized.length;i1<len1;i1++){
                            definitions.push(deserialized[i1]);
                        }
                    } else if(typeof deserialized == 'object'){
                        definitions.push(deserialized);
                    }
                    
                }
                
                return definitions;
            }
        });
    
        var instance = new _class();
        // Store class reference
        instance._class = _class;
        return instance;
    })


