define(
    ['dojo/_base/declare','dojo/_base/Deferred','azfcms/model',
        
    'dojo/_base/lang','azfcms/model/navigation','azfcms/module/default/view/util',
    'dojo/i18n!azfcms/resources/i18n/cms/common/nls/common'],function
    (declare, Deferred, model,
        lang, navigationModel,util,
        nls)
        {
        var _class = declare([],{
        
            constructor: function(args){
                
                for(var name in nls){
                    if(name.indexOf("nce")==0){
                        this[name] = nls[name];
                    }
                }
                
                // Reference of navigation node id
                this.nodeId = null;
                /**
             * Reference of content edit pane
             */
                this.cep = null;
            
                this.staticParams = null;
            
                this.dynamicParams = null;
                
                this.node = null;
                
                if(args&&args.navigationModel){
                    this.navigationModel = args.navigationModel;
                } else {
                    this.navigationModel = navigationModel;
                }
                if(args&&args.util){
                    this.util = args.util;
                } else {
                    this.util = util;
                }
            },
        
            init: function(node, cep){
                var nodeId = node.id;
                var d = new Deferred();
                // Store nodeId ref.
                this.nodeId = nodeId;
                // Store node ref.
                this.node = node;
                // Store cep ref.
                this.cep = cep;
                // Create closure referencable this
                var self = this; 
                // Load static and dynamic plugin params
                this.navigationModel.getNodeParams(nodeId).then(function
                    (params){
                        var staticParams = params[0];
                        var dynamicParams = params[1];
               
                        self.staticParams = staticParams;
                        self.dynamicParams = dynamicParams;
                        self._onParamsLoad();
               
                        self._build(staticParams,dynamicParams).then(function(controller){
                            d.callback(controller)
                        });
                    });
           
                // Initialize pane listeners
                this._initListeners();
                
           
                return d;
            },
       
            _initListeners: function(){
                this.cep.on("metadataSave",lang.hitch(this,this.onMetadataSave));
                this.cep.on("typeChange",lang.hitch(this,this.onTypeChange));
            },
       
            _build: function(staticParams, dynamicParams){
                var self = this;
                // Create deferred
                var d = new Deferred();
                // Create closure accessable this 
                var cec = this;
                // Create clusure accessable editor pane
                var cep = this.cep;
                // Get plugin identifier
                var pluginIdentifier = staticParams.pluginIdentifier;
                var ucPluginIdentifier = pluginIdentifier.substring(0,1).toUpperCase()+pluginIdentifier.substring(1);
                var module = staticParams.module;
           
                require(['azfcms/store/registry!ContentPluginTypeStore'],function(contentPluginStore){
                    contentPluginStore.get(pluginIdentifier).then(function(plugin){
                        var module = plugin.module;
                        
                        require(['azfcms/module/'+module+'/contentPlugin/'+pluginIdentifier+'/controller/'+ucPluginIdentifier, 'azfcms/module/'+module+'/contentPlugin/'+pluginIdentifier+'/view/'+ucPluginIdentifier],
                            function(EC, EP){
                                var ep = cec._buildEditorPane(EP,cep);
                                cec._buildController(EC,self.nodeId,ep).then(function(controller){
                                    d.callback(controller)
                                });
                            })
                    })
                })
           
                
                return d;
            },
       
            /**
        * This method will create instance of editor pane and attach it to
        * content editor pane
        * @return {dijit.layout.ContentPane}
        */
            _buildEditorPane: function(EditorPane,cep){
                var ep = new EditorPane();
                cep.addChild(ep);
                return ep;
            },
       
            /**
        * THis method will initialize editor controller and initialize it.
        * @return {dojo.Deferred}
        */
            _buildController: function(EditorController, nodeId, ep){
                var ec = new EditorController();
                var d = ec.initDependencies(nodeId, ep);
                return d;
            },
            
            /**
             * This method will be invoked when the parameters are loaded from the backend
             */
            _onParamsLoad: function(){
                this.cep.set("title",this.node.title);
                this.cep.set("url",this.node.url);
                this.cep.set("description",this.dynamicParams.metaDescription);
                this.cep.set("keywords",this.dynamicParams.metaKeywords);
                this.cep.pageType.set('value',this.staticParams.pluginIdentifier);
            },
       
       
            onMetadataSave: function(title, url, description, keywords){
                var nid = this.nodeId;
                this.navigationModel.setMetaValues(nid,title, url,description,keywords);
            },
            
            onTypeChange:function(newType){
                var self = this;
                if(newType == this.staticParams.pluginIdentifier){
                    return;
                }
                
                this.util.confirm(function(confirmed){
                    if(!confirmed){
                        return false;
                    }
                    
                    self.navigationModel.changePageType(self.node.id,newType).
                    then(function(result){
                        if(!result){
                            return;
                        }
                        self.navigationModel.getNodeParams(self.node.id).
                        then(function(params){
                            var staticParams = params[0];
                            var dynamicParams = params[1];
                            self.staticParams = staticParams;
                            self.dynamicParams = dynamicParams;
                            self._onParamsLoad();
                            
                            self._build(staticParams, dynamicParams)
                        })
                        
                    })
                },self.nceTypeChangeMsg)
                
                
            }
        });
    
        return _class;
    })