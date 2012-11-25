define('',[
    'dojo/_base/lang',
    'dojo/_base/declare',
    'azfcms/store/QueryLangStore',
    'dojo/topic',
    'dojo/Deferred',
    'dojo/store/util/QueryResults',
    'dojo/request/iframe'
],
function(lang, declare, QueryLangStore, topic, Deferred,QueryResults, iframeRequest){
    
    return declare([QueryLangStore],{
        STATUS_SANDBOX_VIOLATION_ERROR:1,
        STATUS_OK:2,
        STATUS_DOES_NOT_EXIST:3,
        STATUS_REQUEST_NOT_COMPATIBLE:7,
        FILTER_DIR:4,
        FILTER_FILE:5,
        FILTER_NONE:6,
        idProperty:'path',
        constructor:function(){
            this._events = [];
            var FSStore = this;
            topic.subscribe('azfcms/FSStore:updateChildren',function(parent,children){
                FSStore.onChildrenChange(parent, children);
            })
        },
        on:function(event, handler){
            if(typeof this._events[event] == 'undefined'){
                this._events[event] = new Array();
            }
            this._events[event].push(handler);
        },
        _triggerAnyChange:function(){
            for(var i=0,len = this._events['anyChange'].length;i<len;i++){
                try{
                    this._events['anyChange'][i]();
                }catch(e){
                    
                }
            }
        },
        _publishChildrenUpdate:function(path, updateParent){
            if(updateParent){
                var d = this.model.multipleInvoke([
                    ['cms.fs.getParentPathInfo',[path]],
                    ['cms.fs.getParentChildren',[path,{filter:this.FILTER_FILE}]]
                ])
            } else {
                var d = this.model.multipleInvoke([
                    ['cms.fs.getPathInfo',[path]],
                    ['cms.fs.getChildren',[path,{filter:this.FILTER_FILE}]]
                ])
            }
            
            var FSStore = this;
            d.then(function(response){
                var pathInfoResponse = response[0];
                var childrenResponse = response[1];
                if(pathInfoResponse.status != FSStore.STATUS_OK || childrenResponse.status != FSStore.STATUS_OK) {
                    return;
                } else {
                    topic.publish('azfcms/FSStore:updateChildren',pathInfoResponse.data, childrenResponse.data);
                }
            })
        },
        rpcCall:function(method){
            var args = []
            for(var i = 1, len = arguments.length;i<len;i++){
                args.push(arguments[i]);
            }
            
            return this.model.singleInvoke("cms.fs."+method, args);
        },
        query:function(path, args){ 
            path = this._fixDelegatedObj(path);
            args = this._fixDelegatedObj(args);
            var d = new Deferred();
            
            var FSStore = this;
            var call = this.rpcCall('queryDetailedDirectoryContents',path,args);
            call.then(function(response){
                if(response.status!=FSStore.STATUS_OK){
                    d.reject(response.status);
                } else {
                    d.resolve(response.data)
                }
            });
            
            d.total = call.then(function(response){
                return response.metadata.total;
            })
            return new  QueryResults(d);
        },
        get:function(path){
            return this.rpcCall('get',path);
        },
        mayHaveChildren:function(item){
            return item.isDir;
        },
        getChildren:function(path, onComplete){
            var d = this.rpcCall('getChildren',path,{filter:this.FILTER_FILE});
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
        },
        getIdentity:function(item){
            return item.path;
        },
        getLabel:function(item){
            return item.name;
        },
        getRoot:function(callback){
            var d = this.rpcCall('getRoot');
            if(callback){
                d.then(function(result){
                    callback(result.data);
                });
            }
            return d;
        },
        remove:function(path){
            var d =  this.rpcCall('recursivelyDelete',path);
            var FSStore = this;
            d.then(function(){
                FSStore._publishChildrenUpdate(path,true);
                FSStore._triggerAnyChange()
            });
            return d;
        },
        rename:function(item, newName){
            var d = this.rpcCall('rename',item,newName);
            var FSStore = this;
            d.then(function(response){
                if(response.status == FSStore.STATUS_OK){
                    FSStore._publishChildrenUpdate(item,true);
                    FSStore._triggerAnyChange()
                } else {
                    throw new Error(response);
                }
                
            })
            return d;
        },
        cloneDirectory:function(srcPath, dstPath){
            var d =  this.rpcCall('cloneDirectory',srcPath, dstPath);
            var FSStore = this;
            d.then(function(response){
                if(response.status==FSStore.STATUS_OK){
                    FSStore._publishChildrenUpdate(dstPath);
                    FSStore._triggerAnyChange()
                } else {
                    throw new Error(response.status, response.errors);
                }
            })
            return d
        },
        /**
         * @param {Object} args {path:<String>,content:<Null|False|Int> for dir|<String> for file>}
         */
        add:function(name,args){
            var d = this.rpcCall('add',name,args);
            var FSStore = this;
            d.then(function(resp){
                if(resp.status==FSStore.STATUS_OK){
                    FSStore._publishChildrenUpdate(args.path);
                    FSStore._triggerAnyChange()
                }
            })
            return d;
            
        },
        
        upload :function(path, form){
            var d = iframeRequest("/json-lang.php",{
                form:form,
                data:{
                    "render-type":"render-in-textarea",
                    "expr":this.model.createCall('cms.fs.upload',[path])
                }
            });
            
            var FSStore =this;
            d.then(function(){
                FSStore._triggerAnyChange();
            })
            
            return d;
        },
        
        put:function(path, content){
            return this.rpcCall('put',path,content);
        },
        
        move:function(from, to){
            var d = this.rpcCall('move',from,to);
            var FSStore = this;
            d.then(function(response){
                if(response.status == FSStore.STATUS_OK){
                    FSStore._publishChildrenUpdate(from,true);
                    FSStore._publishChildrenUpdate(to,false);
                    FSStore._triggerAnyChange();
                }
            })
            return d;
        },
        
        getFileContents:function(path){
            return this.rpcCall('getFileContents',path);
        },
        onChildrenChange:function(parent, children){
            
        }
    })
})