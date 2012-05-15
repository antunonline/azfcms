define(['azfcms/model','dojo/_base/Deferred','dojo/_base/declare',
        'dojo/store/util/QueryResults'],
    function(model,Deferred, declare,
    QueryResults){
        
    var _class = declare([],{
        
        /**
         * @return {dojo.store.util.QueryResults}
         */
        getContentPluginStore: function(){
            // If store is already loaded
            if(typeof this.getContentPluginStoreLoaded != 'undefined'){
                return this.contentPluginStoreQueryResults;
            }
            
            var cms = this;
            var promise = model.invoke("cms.pluginDescriptor.getContentPlugins()").
                then(function(results){
                    cms.getContentPluginStoreLoaded = true;
                });
                
            this.contentPluginStoreQueryResults = new QueryResults(promise);
            return this.contentPluginStoreQueryResults;
        }
    });
    
    
    var instance = new _class();
    instance._class = _class;
    return instance;
    
})
