/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define(['dojo/_base/declare','azfcms/store/QueryLangStore'],
    function(declare,QueryLangStore){
        return declare([QueryLangStore],{
            idProperty:"inode",
            queryMethod:"cms.filesystem.getFileList",
            addMethod:"cms.filesystem.uploadFiles",
            getMethod:"cms.filesystem.getFileList",
            putMethod:"cms.filesystem.uploadFiles",
            removeMethod:"cms.filesystem.deleteFiles",
            isTreeModel:false,
            constructor:function(){
                var self = this;
                this._connects = [];
                
                if(this.isTreeModel){
                    this._connects.push(require.on("azfcms/store/Filesystem/deleteFiles",function(item){
                        self.getParentDirectory(item).then(function(parentItem){
                            self.get(parentItem).then(function(children){
                                self.onChildrenChange(parentItem, children)
                            });
                        })
                    }));
                
                    this._connects.push(require.on("azfcms/store/Filesystem/createDirectory",function(parentDirectory){
                        self.get(parentDirectory).then(function(children){
                            self.onChildrenChange(parentDirectory, children)
                        })
                    }));
                }
                
                
                
            },
            destroy:function(){
                for(var i =0, len=this._connects.length;i<len;i++){
                    this._connects[i].remove();
                }
            },
            getRoot:function(callback){
                this.model.singleInvoke("cms.filesystem.getRoot",[]).then(function(rootNode){
                    callback(rootNode)
                })
            },
            mayHaveChildren:function(){
                return true;
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
            
            remove:function(){
                
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
                    require.signal("azfcms/store/Filesystem/createDirectory",inDirectory);
                })
                return promise;
            },
            
            getParentDirectory:function(item){
                return this.model.singleInvoke("cms.filesystem.getParentDirectory",[item])
            },
            
            onChange:function(item){}, 
            
            onChildrenChange:function(parentIte, children){}
        })
    })

