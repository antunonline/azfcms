/* 
 * @author Antun Horvat
 */


define(
    ['dojo/_base/declare','azfcms/model','dojo/_base/lang','dojo/store/util/QueryResults',
    'dojo/_base/json','dojo/_base/Deferred'],function
    (declare, model,lang, QueryResults,
        json, Deferred){
        return declare([],{
            idProperty:"",
            queryMethod:"",
            addMethod:"",
            getMethod:"",
            putMethod:"",
            removeMethod:"",
            constructor:function(args){
                if(!args){
                    args = {}
                }
                
                this.queryOptions={};
                this.addOptions={};
                this.getOptions={};
                this.putOptions={};
                this.removeOptions={};
                this.model=null;
                /**
                 * If this is set to true, query results
                 * will be stored to queryGetCache object. Item keys
                 * will be retrieved by getIdentity() method. 
                 * 
                 * Then all get(id) calls will first lookup the cache and return 
                 * the result from the cache if it is found. If not result will 
                 * be loaded from the server.
                 * 
                 */
                this.useQueryAsGetCache = false;
                /**
                 * Query cache that is used if useQueryAsGetCache is enabled.
                 */
                this.queryGetCache = {}
                
                /**
                 *  If this property is set to true, all not yet loaded query
                 *  calls will be cached and served on next equal query call .
                 *  
                 *  Query calls and cached results will be compared by md5 json serialized
                 *  query method arguments.
                 */
                this.useQueryCache = false;
                
                /**
                 * Query cache 
                 */
                this.queryCache = {};
                
                /**
                 * If this property is set to true all get calls which are not
                 * present in queryGetCache will initiate query calls, and skip
                 * getMethod calls. 
                 * 
                 * All query calls will be done in incremental pagination fasion 
                 * which means that we will paginate and cache query results untin
                 * the item with provided id is found. 
                 * 
                 * If set to true, useQueryAsGetCache property will also be implicitly
                 * enabled. 
                 */
                this.useQueryForGetRetrieval = false;
                
                this.queryForGetRetrievalMethod = "default";
                
                
                
                lang.mixin(this,args);
                if("idProperty" in args == false && this.idProperty == false){
                    throw "QueryLangStore.constructor: idPropery value is not provided to the class constructor";
                }
                this.idProperty = args.idProperty;
                
                if(typeof args.model != 'undefined'){
                    this.model = args.model;
                } else {
                    this.model = model;
                }
                
                if(this.useQueryForGetRetrieval){
                    this.useQueryAsGetCache=true;
                }
                
                
            },
            
            getIdentity:function(){
                return this.idProperty;
            },
            
            query:function(query, queryArgs){
                if(typeof queryArgs === 'undefined'){
                    queryArgs = null
                }
                if(!this.queryMethod)
                    throw "QueryLangStore:query: QueryLangStore.queryMethod is not specified";
                
                if(typeof query == 'object'){
                    query = this._fixDelegatedObj(query);
                }
                
                if(typeof queryArgs == 'object'){
                    queryArgs = this._fixDelegatedObj(queryArgs);
                }
                
                
                var args = [
                query,
                queryArgs,
                this.queryOptions
                ];
                
                if(this.useQueryCache){
                    var key = json.toJson(query)+json.toJson(queryArgs);
                    if(this.queryCache[key]){
                        this.queryCache[key];
                    }
                }
                
                var call = this.model.createCall(this.queryMethod,args);
                var deferred = this.model.invoke(call);
                var QueryResultResolver = new Deferred();
                
                if(this.useQueryAsGetCache){
                    var self = this;
                    deferred.then(function(results){
                        for(var qgKey in results.data){
                            self.queryGetCache[self.getIdentity(results.data[qgKey])] = results.data[qgKey];
                        }
                    })
                }
                
                deferred.then(function(response){
                    QueryResultResolver.resolve(response.data);
                })
                QueryResultResolver.total = deferred.then(function(results){
                    return results.total;
                })
                
                var queryResults = new QueryResults(QueryResultResolver);
                if(this.useQueryCache){
                    this.queryCache[key] = queryResults;
                }
                
                return queryResults;
                
            },
            
            add:function(obj, directives){
                if(typeof directives == 'undefined'){
                    directives = {}
                }
                
                if(!this.addMethod)
                    throw "QueryLangStore.add: QueryLangStore.addMethod is not specified";
                
                if(typeof obj == "object"){
                    obj = this._fixDelegatedObj(obj);
                }
                
                if(typeof directives == 'undefined'){
                    directives = null;
                }
                else if(typeof directives == 'object'){
                    directives = this._fixDelegatedObj(directives);
                }
                
                var args = [
                obj,
                directives,
                this.addOptions
                ];
                
                var call = this.model.createCall(this.addMethod,args);
                return this.model.invoke(call);
            },
            
            get:function(id){
                if(this.useQueryAsGetCache){
                    if(this.queryGetCache[id]){
                        var deferred = new Deferred();
                        deferred.resolve(this.queryGetCache[id]);
                        return deferred;
                    }
                }
                
                if(this.useQueryForGetRetrieval){
                    var deferred = new Deferred();
                    var start = 0;
                    var count = 25;
                    var self = this;
                    var queryResults = this.query(id,{
                        start:start,
                        count:count
                    });
                    var resolveFnc = function(queryResults,totalCount){
                        queryResults.then(function(){
                            if(self.queryGetCache[id]){
                                deferred.resolve(self.queryGetCache[id]);
                                return true;
                            } else {
                                if(totalCount&&totalCount>(start+count)){
                                    start = start+count;
                                    queryResults = self.query(id,{
                                        start:start,
                                        count:count
                                    });
                                    Deferred.when(queryResults.total,function(total){
                                        if(typeof total == 'undefined'){
                                            total = 0;
                                        }
                                        resolveFnc(queryResults,total);
                                    })
                                } else {
                                    deferred.reject("Requested record does not exist")
                                }
                            }
                        })
                        
                    }
                    
                    Deferred.when(queryResults.total,function(total){
                        if(typeof total == 'undefined'){
                            total = 0;
                        }
                        resolveFnc(queryResults,total);
                    });
                    
                    return deferred;
                }
                
                if(!this.getMethod)
                    throw "QueryLangStore.get: QueryLangStore.getMethod is not specified";
                
                var args = [
                id,
                this.getOptions
                ];
                
                var call = this.model.createCall(this.getMethod,args);
                return this.model.invoke(call);
            },
            
            
            put:function(obj, putDirectives){
                if(!this.putMethod)
                    throw "QueryLangStore.put: QueryLangStore.putMethod is not specified";
                
                if(typeof putDirectives == 'object'){
                    putDirectives = this._fixDelegatedObj(putDirectives);
                }
                else if(typeof putDirectives == 'undefined'){
                    putDirectives = null;
                }
                var args = [
                obj,
                putDirectives,
                this.putOptions
                ];
                
                var call = this.model.createCall(this.putMethod,args);
                return this.model.invoke(call);
            },
            
            remove:function(obj){
                if(!this.removeMethod)
                    throw "QueryLangStore.remove: QueryLangStore.removeMethod is not specified";
                
                if(this.useQueryAsGetCache){
                    delete this.queryGetCache[this.getIdentity(obj)];
                }
                
                var args = [
                obj,
                this.removeOptions
                ];
                
                var call = this.model.createCall(this.removeMethod,args);
                return this.model.invoke(call);
            },
            
            _fixDelegatedObj:function(obj){
                var deDelegatedObj = {}
                for(var name in obj){
                    if(obj[name] instanceof RegExp){
                        deDelegatedObj[name] = obj[name].toString();
                    } else {
                        deDelegatedObj[name] = obj[name];
                    }
                    
                }
                return deDelegatedObj;
            },
            
            getIdentity:function(obj){
                return obj[this.idProperty];
            }
        })    
    })

