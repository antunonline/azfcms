/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define(['dojo/_base/declare','azfcms/store/QueryLangStore'],
    function(declare,QueryLangStore){
        return declare([QueryLangStore],{
            idProperty:"name",
            queryMethod:"cms.filesystem.getFileList",
            addMethod:"cms.filesystem.uploadFiles",
            getMethod:"cms.filesystem.getFileList",
            putMethod:"cms.filesystem.uploadFiles",
            removeMethod:"cms.filesystem.deleteFiles",
            getRoot:function(callback){
                return callback({name:"",dirname:""});
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
            }
        })
    })

