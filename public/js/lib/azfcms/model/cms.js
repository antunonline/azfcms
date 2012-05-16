define(['azfcms/model','dojo/_base/Deferred','dojo/_base/declare',
        'dojo/store/Memory'],
    function(model,Deferred, declare,
    Memory){
        
    var _class = declare([],{
        
        /**
         * @return {dojo.store.util.QueryResults}
         */
        getContentPluginStore: function(){
            // If store is already loaded
            if(typeof this.getContentPluginStoreLoaded != 'undefined'){
                return this.contentPluginStore;
            }
            
            var cms = this;
            var promise = model.invoke("cms.pluginDescriptor.getContentPlugins()")
                
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
