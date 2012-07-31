/* 
 * @author Antun Horvat
 */


define(
    ['dojo/_base/declare','azfcms/model','dojo/_base/lang'],function
    (declare, model,lang){
        return declare([],{
            idProperty:"",
            queryMethod:"",
            queryOptions:{},
            addMethod:"",
            addOptions:{},
            getMethod:"",
            getOptions:{},
            putMethod:"",
            putOptions:{},
            removeMethod:"",
            removeOptions:{},
            model:null,
            constructor:function(args){
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
            },
            
            getIdentity:function(){
                return this.idProperty;
            },
            
            query:function(query){
                if(!this.queryMethod)
                    throw "QueryLangStore:query: QueryLangStore.queryMethod is not specified";
                
                var args = [
                query,
                this.queryOptions
                ];
                
                var call = this.model.createCall(this.queryMethod,args);
                return this.model.invoke(call);
            },
            
            add:function(obj){
                if(!this.addMethod)
                    throw "QueryLangStore.add: QueryLangStore.addMethod is not specified";
                
                var args = [
                    obj,
                    this.addOptions
                ];
                
                var call = this.model.createCall(this.addMethod,args);
                return this.model.invoke(call);
            },
            
            get:function(id){
                if(!this.getMethod)
                    throw "QueryLangStore.get: QueryLangStore.getMethod is not specified";
                
                var args = [
                    id,
                    this.getOptions
                ];
                
                var call = this.model.createCall(this.getMethod,args);
                return this.model.invoke(call);
            },
            
            
            put:function(obj){
                if(!this.putMethod)
                    throw "QueryLangStore.put: QueryLangStore.putMethod is not specified";
                
                var args = [
                    obj,
                    this.putOptions
                ];
                
                var call = this.model.createCall(this.putMethod,args);
                return this.model.invoke(call);
            },
            
            remove:function(obj){
                if(!this.removeMethod)
                    throw "QueryLangStore.remove: QueryLangStore.removeMethod is not specified";
                
                var args = [
                    obj,
                    this.removeOptions
                ];
                
                var call = this.model.createCall(this.removeMethod,args);
                return this.model.invoke(call);
            }
        })    
    })

