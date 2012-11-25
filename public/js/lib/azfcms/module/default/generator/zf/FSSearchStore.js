define([
    'dojo/_base/declare',
    'azfcms/module/default/store/FSStore',
    'dojo/store/util/QueryResults',
    'dojo/Deferred'
],function(declare, FSStore, QueryResults, Deferred){
    return declare([FSStore],{
        query:function(query, params){
            var FSStore = this;
            
            params = this._fixDelegatedObj(params);
            
            var d = new Deferred();
            var call = this.model.singleInvoke('cms.fs. detailedRecursiveSearchQuery',[query,params])
            call.then(function(response){
                if(response.status == FSStore.STATUS_OK){
                    d.resolve(response.data);
                } else {
                    d.reject(response.status);
                    throw new Error(response.status, response.errors); 
                }
            });
            
            d.total = call.then(function(response){
                if(response.status==FSStore.STATUS_OK) {
                    return response.metadata.total;
                } else {
                    throw new Error(response.status);
                }
            });
            
            return new QueryResults(d);
        },
        getChildren:function(path, onComplete){
            var d = this.rpcCall('getChildren',path,{filter:this.FILTER_NONE});
            var FSStore = this;
            if(onComplete){
                d.then(function(result){
                    if(result.status == FSStore.STATUS_OK) {
                        onComplete(result.data);
                    } else {
                        FSStore._publishChildrenUpdate(path,true);
                        throw new Error(result.status, result.errors);
                    }
                    
                })
            }
            return d;
        }
    })
})


