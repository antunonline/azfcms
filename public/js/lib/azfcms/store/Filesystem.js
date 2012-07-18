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
            constructor:function(){
                var self = this;
                
                require.on("azfcms/store/Filesystem/change",function(item){
                    self.get(item).then(function(newItem){
                        self.onChange(newItem);
                    })
                });
                
                require.on("azfcms/store/Filesystem/childrenChange",function(parentItem){
                    self.query(parentItem).then(function(items){
                        self.onChildrenChange(parentItem, items);
                    })
                });
                
                
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
            
            onChange:function(item){
                
            }, 
            
            onChildrenChange:function(parentIte, children){
                
            }
        })
    })

