/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define(['dojo/_base/declare','dojo/_base/lang','azfcms/view/UploadPane',
    'dijit/Dialog','azfcms/model/cms','azfcms/view/util'],function
    (declare,lang,UploadPane,
        Dialog,cmsModel,viewUtil){
        return declare([],{
            view:null,
            uploadDialog:null,
            uploadPane:null,
            constructor:function(args){
                lang.mixin(this,args);
                this.attachEventHandlers();
            },
            attachEventHandlers:function(){
                this.view.on("treeselect",lang.hitch(this,this.onTreeSelect));
                this.view.addAction("Upload files",lang.hitch(this,this.onUpload));
                this.view.addAction("Delete",lang.hitch(this,this.onDelete));
                this.view.addAction("New folder",lang.hitch(this,this.onFolderCreate));
            },
            onUpload:function(selectedTreeItem,selectedGridItems){
                if(!selectedTreeItem)
                    return;
                this.getUploadDialog().show();
                
            },
            getUploadDialog:function(){
                if(this.uploadDialog)
                    return this.uploadDialog;
                var self = this;
                this.uploadDialog = new Dialog();
                this.uploadPane = new UploadPane();
                this.uploadDialog.set("content",this.uploadPane);
                this.uploadPane.on("upload",function(form){
                    self.doUpload(form);
                });
                this.uploadPane.on("cancel",function(){
                    self.uploadDialog.hide();
                });
                
                return this.uploadDialog;
            },
            doUpload:function(form){
                var self = this;
                this.view.disable();
                cmsModel.uploadFiles(this.view.getTreeSelection(), form).then(function(){
                    self.view.enable();
                    self.view.reload();
                    form.reset();
                });
            },
            onDelete:function(){
                var gridSelection = this.view.getGridSelection();
                if(gridSelection.length==0)
                    return;
                var self = this;
                viewUtil.confirm(function(confirmed){
                    self.doDelete(gridSelection)
                    }, "Do you want to delete selected files?")
                
            },
            doDelete:function(files){
                var self = this;
                self.view.disable();
                cmsModel.deleteFiles(files).then(function(){
                    self.view.reload();
                    self.view.enable();
                })
            },
            onTreeSelect:function(item){
                this.view.reloadGrid(item);
            },
            onFolderCreate:function(){
                var name = window.prompt("Unesite naziv novog direktorija");
                if(name){
                    this.doFolderCreate(name);
                }
            },
            doFolderCreate:function(name){
                var self = this;
                self.view.disable();
                cmsModel.createDirectory(name).then(function(){
                    self.view.reload();
                    self.view.enable();
                })
            }
        })
    })
