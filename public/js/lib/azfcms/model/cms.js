define(['azfcms/model','dojo/_base/Deferred','dojo/_base/declare',
    'dojo/store/Memory','azfcms/store/QueryLangStore'],
    function(model,Deferred, declare,
        Memory,QueryLangStore){
        
        var _class = declare([],{
            constructor:function(){
                this.model = model;
            },
        
            createCall:function(method,args){
                return this.model.createCall(method,args);
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
                var store = this.model.prepareLangStore(call);
                store.idProperty = "identifier";
                return store;
            },
            
            getExtensionPluginStore: function(){
                var call = "cms.pluginDescriptor.getExtensionPlugins()";
                var store = this.model.prepareLangStore(call);
                store.idProperty="name"
                return store;
            },
            
            setExtensionPluginValues: function(navigationId,pluginId,name,description,region,weight,enable){
                var call = this.createCall("cms.extensionPlugin.setExtensionPluginValues",[navigationId,pluginId,name,description,region,weight,enable])
                return this.model.invoke(call);
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
                var store = this.model.prepareLangStore(call);
                store.idProperty = "identifier";
                return store;
            },
            
            findPluginsByNavigationAndRegion:function(navigationId,region){
                var call = this.createCall("cms.extensionPlugin.findPluginsByNavigationAndRegion",[navigationId,region]);
                return this.model.invoke(call);
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
            },
            
            /**
             * @param {Form} form
             * @return {dojo.Deferred}
             */
            uploadFiles:function(directory,form){
                var method = "cms.filesystem.uploadFiles";
                var call = this.createCall(method,[directory]);
                
                var promise =  this.model.invokeWithForm(call,form);
                promise.then(function(){
                    require.signal("azfcms/store/Filesystem/childrenChange",[directory]); 
                })
                return promise;
            },
            
            /**
             * Delete provided JS files
             */
            deleteFiles:function(files){
                var method = "cms.filesystem.deleteFiles";
                var call = this.createCall(method,[files]);
                var promise =  this.model.invoke(call);
                promise.then(function(){
                    if(files.length >0){
                        var file = files[0];
                        file.name = "";
                        require.signal("azfcms/store/Filesystem/childrenChange",file);
                    }
                })
                return promise;
            },
            
            createDirectory:function(name){
                var method = "cms.filesystem.createDirectory";
                var call = this.createCall(method,[name]);
                var promise =  this.model.invoke(call);
                promise.then(function(){
                    name.name  ="";
                    require.signal("azfcms/store/Filesystem/childrenChange",name);
                })
                return promise;
            },
            
            
            /**
             * @param {Number} nodeId
             * @return {dojo.Deferred}
             */
            setHomePage:function(nodeId){
                var call = this.model.createCall("cms.navigation.setHomePage",[nodeId]);
                return this.model.invoke(call);
            },
            
            /**
             * @param {Number} pluginId
             * @param {Number|String} key
             * @param {mixed} value
             * @return {dojo.Deferred} (boolean)
             */
            setExtensionValue:function(pluginId,key,value){
                var call = this.model.createCall("cms.extensionPlugin.setExtensionValue",[pluginId, key, value]);
                return this.model.invoke(call);
            },
            
            /**
             * @param {Number} pluginId
             * @param {Object} values
             * @return {dojo.Deferred} (boolean)
             */
            setExtensionValues:function(pluginId,values){
                var call = this.model.createCall("cms.extensionPlugin.setExtensionValues",[pluginId, values]);
                return this.model.invoke(call);
            },

            /**
             * @param {Number} pluginId
             * @param {Number|String} key
             * @return {Dojo.deferred} (mixed)
             */
            getExtensionValue:function(pluginId,key){
                var call = this.model.createCall("cms.extensionPlugin.getExtensionValue",[pluginId, key]);
                return this.model.invoke(call);
            },
            
            /**
             * @param {Number} pluginId
             * @return {Dojo.deferred} (Object)
             */
            getExtensionValues:function(pluginId){
                var call = this.model.createCall("cms.extensionPlugin.getExtensionValues",[pluginId]);
                return this.model.invoke(call);
            }
        });
    
    
        var instance = new _class();
        instance._class = _class;
        return instance;
    
    })
