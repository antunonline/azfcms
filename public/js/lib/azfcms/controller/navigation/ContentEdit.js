define(
    ['dojo/_base/declare','dojo/_base/Deferred','azfcms/model',
        
    'dojo/_base/lang','azfcms/model/navigation'],function
    (declare, Deferred, model,
        lang, navigationModel)
        {
        var _class = declare([],{
        
            constructor: function(){
                // Reference of navigation node id
                this.nodeId = null;
                /**
             * Reference of content edit pane
             */
                this.cep = null;
            
                this.staticParams = null;
            
                this.dynamicParams = null;
                
                this.node = null;
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
                var cec = this; 
                // Load static and dynamic plugin params
                navigationModel.getNodeParams(nodeId).then(function
                    (params){
                        var staticParams = params[0];
                        var dynamicParams = params[1];
               
                        cec.staticParams = staticParams;
                        cec.dynamicParams = dynamicParams;
                        cec._onParamsLoad();
               
                        cec._build(staticParams,dynamicParams).then(function(controller){
                            d.callback(controller)
                        });
                    });
           
                // Initialize pane listeners
                this._initListeners();
                
           
                return d;
            },
       
            _initListeners: function(){
                this.cep.on("metadataSave",lang.hitch(this,this.onMetadataSave));
            },
       
            _build: function(staticParams, dynamicParams){
                // Create deferred
                var d = new Deferred();
                // Create closure accessable this 
                var cec = this;
                // Create clusure accessable editor pane
                var cep = this.cep;
                // Get plugin identifier
                var pluginIdentifier = staticParams.pluginIdentifier;
                pluginIdentifier = pluginIdentifier[0].toUpperCase()+pluginIdentifier.substring(1);
           
                require(['azfcms/controller/content/'+pluginIdentifier, 'azfcms/view/content/'+pluginIdentifier],
                    function(EC, EP){
                        var ep = cec._buildEditorPane(EP,cep);
                        cec._buildController(EC,this.nodeId,EC).then(function(controller){
                            d.callback(controller)
                        });
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
            _buildController: function(EditorController, ep,nodeId){
                var ec = new EditorController();
                var d = ec.initDependencies(nodeId, ep);
                return d;
            },
            
            /**
             * This method will be invoked when the parameters are loaded from the backend
             */
            _onParamsLoad: function(){
                this.cep.set("title",this.node.title);
                this.cep.set("description",this.dynamicParams.metaDescription);
                this.cep.set("keywords",this.dynamicParams.metaKeywords);
            },
       
       
            onMetadataSave: function(title, description, keywords){
                var nid = this.nodeId;
                navigationModel.setMetaValues(nid,title,description,keywords);
            }
        });
    
        return _class;
    })