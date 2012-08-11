define(
    ['dojo/_base/declare','azfcms/store/QueryLangStore','dojo/_base/Deferred',
    'dojo/store/Cache','dojo/store/Memory'],
    function
    (declare,QueryLangStore,Deferred,
        Cache,Memory){
        var __class = declare([],{
        
            constructor:function(){
                this.instances= {};
            },
            getStoreInstance:function(name,newInstance){
                var deferred = new Deferred();
                var ucName = this.ucFirst(name);
                if(this[ucName]){
                    if(newInstance||!this.instances[ucName]){
                        deferred.resolve(this[ucName]());
                    } else {
                        deferred.resolve(this.instances[ucName]);
                    }
                } else {
                    var self = this;
                    if(newInstance||!this.instances[ucName]){
                        require(['azfcms/store/'+ucName],function(Store){
                            self.instances[ucName] = new Store();
                            deferred.resolve(self.instances[ucName]);
                        })
                    } else {
                        deferred.resolve(this.instances[ucName]);
                    }
                
                }
                return deferred;
            },
            ucFirst:function(name){
                return name.substring(0,1).toUpperCase()+name.substring(1);
            },
            
            
            /**
            * Following methods names will identify build-in stores, available by default to 
            * AZFCMS JS framework
            */ 
            ExtensionPluginStatusStore:function(){
                return QueryLangStore({
                    queryMethod:"cms.extensionPlugin.getExtensionPluginStatusMatrix",
                    putMethod:"cms.extensionPlugin.setExtensionPluginStatus",
                    idProperty:"rowId",
                    useQueryAsGetCache:true,
                    enablePluginGlobally:function(item){
                        return this.model.singleInvoke("cms.extensionPlugin.bindPluginGlobally",[item]);
                    },
                    disablePluginGlobally:function(item){
                        return this.model.singleInvoke("cms.extensionPlugin.unbindPluginGlobally",[item]);
                    }
                });
            },
            
            TemplateRegionsStore:function(){
                return QueryLangStore({
                    idProperty:"identifier",
                    useQueryCache:true,
                    useQueryAsGetCache:true,
                    useQueryForGetRetrieval:true,
                    queryMethod:"cms.template.getDefaultTemplateRegions"
                });
            },
            
            ExtensionPluginTypeStore:function(){
                return QueryLangStore({
                    idProperty:"name",
                    useQueryCache:true,
                    useQueryAsGetCache:true,
                    useQueryForGetRetrieval:true,
                    queryMethod:"cms.pluginDescriptor.getExtensionPlugins"
                });
            },
            
            ExtensionPluginStore:function(){
                return QueryLangStore({
                    idProperty:"name",
                    useQueryAsGetCache:true,
                    queryMethod:"cms.extensionPlugin.getExtensionPlugins",
                    addMethod:"cms.extensionPlugin.addExtensionPlugin",
                    putMethod:"cms.extensionPlugin.setExtensionPluginValues",
                    removeMethod:"cms.extensionPlugin.removeExtensionPlugin"
                });
            },
            
            ContentPluginTypeStore:function(){
                return QueryLangStore({
                    idProperty:"name",
                    useQueryCache:true,
                    useQueryAsGetCache:true,
                    useQueryForGetRetrieval:true,
                    queryMethod:"cms.pluginDescriptor.getContentPlugins"
                });
            },
            
            UsersStore:function(){
                return QueryLangStore({
                    idProperty:"name",
                    queryMethod:"cms.user.queryUsers"
                });
            }
        })
        
        
        
        var instance = new __class();
        instance.__class = __class;
        
        
        instance.load = function(
            id,        // the string to the right of the !
            require,   // AMD require; usually a context-sensitive require bound to the module making the plugin request
            callback,  // the function the plugin should call with the return value once it is done
            errback
            ){ 
            instance.getStoreInstance(id).then(function(store){
                callback(store);
            })
        }
        
        return instance;
    })


