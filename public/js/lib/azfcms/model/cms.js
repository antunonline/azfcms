define(['azfcms/model','dojo/_base/Deferred','dojo/_base/declare',
    'dojo/store/Memory'],
    function(model,Deferred, declare,
        Memory){
        
        var _class = declare([],{
            constructor:function(){
                this.model = model;
            },
        
            createCall:function(method,args){
                var call = [method];
                var callArgs = [];
                var arg;
                for(var i = 0; args.length>i;i++){
                    arg = args[i];
                
                    if(typeof arg == 'string'){
                        callArgs.push("'"+this.model._s(arg)+"'")
                    }else if(typeof arg=="number") {
                        callArgs.push(String(arg));
                    } else {
                        if(arg) callArgs.push("true");
                        else callArgs.push("false");
                    }
                }
            
                call.push("(");
                if(callArgs.length) call.push(callArgs.join(","));
                call.push(")");
                return call.join("");
            },
        
            /**
         *
         */
            addExtensionPlugin: function(navigationId, name,description,type,region,weight,enable){
                var call = this.createCall("cms.extensionPlugin.addExtensionPlugin",[
                    navigationId,name,description,type,region,weight,enable
                    ]);
                    
                return this.model.invoke(call);
            },
            
            getRegionPluginsStore:function(navigationId, region){
                var call = this.createCall('cms.extensionPlugin.getRegionExtensionPlugins',[navigationId,region]);
                return this.model.prepareLangStore(call);
            },
            
            getTemplateRegionsStore:function(navigationId){
                var call = this.createCall('cms.template.getTemplateRegions',[navigationId]);
                return this.model.prepareLangStore(call);
            },
            
            getExtensionPluginStore: function(){
                var call = "cms.pluginDescriptor.getExtensionPlugins()";
                return this.model.prepareLangStore(call);
            },
            
            setExtensionPluginValues: function(navigationId,pluginId,name,description,region,weight,enable){
                var call = this.createCall("cms.extensionPlugin.setExtensionPluginValues",[navigationId,pluginId,name,description,region,weight,enable])
                return this.model.prepareLangStore(call);
            },
            
            removeExtensionPlugin:function(pluginId){
                var call = this.createCall('cms.extensionPlugin.removeExtensionPlugin',[pluginId]);
                return this.model.invoke(call);
            },
            
            disableExtensionPlugin:function(nodeId,pluginId){
                var call = this.createCall("cms.extensionPlugin.disableExtensionPlugin",[nodeId, pluginId]);
                return this.model.invoke(call);
            },
            
            enableExtensionPlugin:function(nodeId, pluginId,weight){
                var call = this.createCall('cms.extensionPlugin.enableExtensionPlugin',[nodeId,pluginId,weight]);
                return this.model.invoke(call);
            },
            
            getTemplateRegionsForNavigationStore:function(navigationId){
                var call = this.createCall("cms.template.getTemplateRegionsForNavigation",[navigationId]);
                return this.model.prepareLangStore(call);
            },
        
            /**
         * @return {dojo.store.util.QueryResults}
         */
            getContentPluginStore: function(){
                // If store is already loaded
                if(typeof this.getContentPluginStoreLoaded != 'undefined'){
                    return this.contentPluginStore;
                }
            
                var cms = this;
                var promise = this.model.invoke("cms.pluginDescriptor.getContentPlugins()")
                
                this.contentPluginStore = new Memory({
                    data:promise
                });
                return this.contentPluginStore;
            }
        });
    
    
        var instance = new _class();
        instance._class = _class;
        return instance;
    
    })
