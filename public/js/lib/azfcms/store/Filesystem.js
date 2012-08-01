/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define(['dojo/_base/declare','azfcms/store/QueryLangStore','dojo/_base/lang'],
    function(declare,QueryLangStore,lang){
        return declare([QueryLangStore],{
            idProperty:"inode",
            queryMethod:"cms.filesystem.queryFileList",
            addMethod:"cms.filesystem.uploadFiles",
            getMethod:"cms.filesystem.getFileList",
            putMethod:"cms.filesystem.uploadFiles",
            removeMethod:"cms.filesystem.deleteFiles",
            constructor:function(args){
                /**
                 * Set to true if this store will be used as tree model
                 */
                this.isTreeModel=false;
                
                lang.mixin(this,args||{});
                
                var self = this;
                this._connects = [];
                this.$_treeItems={};
                this.$_rootItem=null;
                
                
                
                if(this.isTreeModel){
                    this._connects.push(require.on("azfcms/store/Filesystem/deleteFiles",function(){
                        self.updateModelRootChildren();
                    }));
                
                    this._connects.push(require.on("azfcms/store/Filesystem/createDirectory",function(){
                        self.updateModelRootChildren();
                    }));
                    
                    this._connects.push(require.on("azfcms/store/Filesystem/moveFiles",function(){
                        self.updateModelRootChildren();
                    }));
                    
                    this._connects.push(require.on("azfcms/store/Filesystem/renameFile",function(){
                        self.updateModelRootChildren();
                    }));
                    
                    this._connects.push(require.on("azfcms/store/Filesystem/copyFiles",function(){
                        self.updateModelRootChildren();
                    }));
                }
                
                
                
            },
            destroy:function(){
                for(var i =0, len=this._connects.length;i<len;i++){
                    this._connects[i].remove();
                }
            },
            getRoot:function(callback){
                var self = this;
                this.model.singleInvoke("cms.filesystem.getRoot",[]).then(function(rootNode){
                    self.$_rootItem = rootNode;
                    callback(rootNode)
                })
            },
            mayHaveChildren:function(item){
                if(item.type == "directory"){
                    return true;
                }else {
                    return false;
                }
            },
            getChildren:function(parentItem,callback){
                this.get(parentItem).then(function(value){
                    callback(value);
                });
            },
            getLabel:function(item){
                return  item.name;
            },
            
            getIdentity:function(item){
                return item.inode;
            },
            get:function(obj){
                var promise = this.inherited(arguments);
                
                if(this.isTreeModel){
                    var self = this;
                    promise.then(function(children){
                        for(var i = 0, len = children.length; i < len;i++){
                            if(children[i].type == "directory"){
                                self.$_treeItems[String(children[i].inode)] = children[i];
                            }
                        }
                    })
                }
                
                return promise;
                
            },
            isItem:function(){
                return true;
            },
            
            
            updateModelRootChildren:function(){
                if(!this.isTreeModel){
                    return false;
                }
                var self = this;
                
                if(this.$_rootItem){
                    self.onChildrenChange(self.$_rootItem,[]);
                    for(var name in this.$_treeItems){
                        this.onChildrenChange(this.$_treeItems[name], []);
                        this.onDelete(this.$_treeItems[name]);
                        delete this.$_treeItems[name];
                    }
                    this.get(this.$_rootItem).then(function(children){
                        self.onChildrenChange(self.$_rootItem,children);
                    })
                }
            },
            
            
            /**
             * @param {Form} form
             * @return {dojo.Deferred}
             */
            uploadFiles:function(directory,form){
                var method = "cms.filesystem.uploadFiles";
                var call = this.model.createCall(method,[directory]);
                
                var promise =  this.model.invokeWithForm(call,form);
                return promise;
            },
            
            /**
             * Delete provided JS files
             */
            deleteFiles:function(files){
                var method = "cms.filesystem.deleteFiles";
                var call = this.model.createCall(method,[files]);
                var promise =  this.model.invoke(call);
                promise.then(function(){
                    if(files.length >0){
                        var file = files[0];
                        require.signal("azfcms/store/Filesystem/deleteFiles",file);
                    }
                })
                return promise;
            },
            
            createDirectory:function(inDirectory, name){
                var method = "cms.filesystem.createDirectory";
                var call = this.model.createCall(method,[inDirectory, name]);
                var promise =  this.model.invoke(call);
                promise.then(function(response){
                    if(!response){
                        return;
                    }
                    require.signal("azfcms/store/Filesystem/createDirectory");
                })
                return promise;
            },
            
            getParentDirectory:function(item){
                return this.model.singleInvoke("cms.filesystem.getParentDirectory",[item])
            },
            
            moveFiles:function(srcFiles,dst){
                var self = this;
                var promise = this.model.singleInvoke('cms.filesystem.moveFiles',[srcFiles,dst]);
                promise.then(function(){
                    require.signal("azfcms/store/Filesystem/moveFiles");
                })
                return promise;
            },
            
            renameFile:function(file, newName){
                var self = this;
                var promise = this.model.singleInvoke('cms.filesystem.renameFile',[file, newName]);
                promise.then(function(outcome){
                    if(outcome){
                        require.signal("azfcms/store/Filesystem/renameFile");
                    }
                    
                })
                return promise;
            },
            
            copyFiles:function(srcFileList, destinationDirectory){
                var self = this;
                var promise = this.model.singleInvoke('cms.filesystem.copyFiles',[srcFileList, destinationDirectory]);
                promise.then(function(outcome){
                    require.signal("azfcms/store/Filesystem/copyFiles");
                    
                })
                return promise;
            },
            
            onChange:function(item){}, 
            
            onChildrenChange:function(parentIte, children){},
            onDelete:function(item){},
            onDeleteItem:function(item){}
        })
    })

