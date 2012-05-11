define(
['dojo/_base/declare','dojo/_base/Deferred','azfcms/model'],function
(declare, Deferred, model)
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
        },
        
       init: function(nodeId, cep){
           var d = new Deferred();
           // Store nodeId ref.
           this.nodeId = nodeId;
           // Store cep ref.
           this.cep = cep;
           // Create closure referencable this
           var cec = this; 
           // Load static and dynamic plugin params
           require(['azfcms/model![navigation.getStaticParams('+nodeId+'),navigation.getDynamicParams('+nodeId+')]'], function
           (params){
               var staticParams = params[0];
               var dynamicParams = params[1];
               
               cec.staticParams = staticParams;
               cec.dynamicParams = dynamicParams;
               
               cec._build(staticParams,dynamicParams).then(d.callback);
           })
           
           return d;
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
              cec._buildController(EC,this.nodeId,EC).then(d.callback);
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
       }
    });
    
    return _class;
})