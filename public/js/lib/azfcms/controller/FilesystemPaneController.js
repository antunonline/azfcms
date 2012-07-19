/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define(['dojo/_base/declare','dojo/_base/lang','azfcms/view/UploadPane',
    'dijit/Dialog','azfcms/store/Filesystem','azfcms/view/util','dojo/i18n!azfcms/resources/nls/view'],function
    (declare,lang,UploadPane,
        Dialog,FilesystemStore,viewUtil, nls){
        return declare([],{
            view:null,
            uploadDialog:null,
            uploadPane:null,
            constructor:function(args){
                lang.mixin(this,args);
                this.filesystem = new FilesystemStore({});
                this.attachEventHandlers();
            },
            attachEventHandlers:function(){
                this.view.on("treeselect",lang.hitch(this,this.onTreeSelect));
                this.view.addAction(nls.fspUploadLabel,lang.hitch(this,this.onUpload));
                this.view.addAction(nls.fspDeleteLabel,lang.hitch(this,this.onDelete));
                this.view.addAction(nls.fspCreateFolderLabel,lang.hitch(this,this.onFolderCreate));
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
                this.filesystem.uploadFiles(this.view.getTreeSelection(), form).then(function(){
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
                this.filesystem.deleteFiles(files).then(function(){
                    self.view.reload();
                    self.view.enable();
                })
            },
            onTreeSelect:function(item){
                this.view.reloadGrid(item);
            },
            onFolderCreate:function(){
                var treeSelection = this.view.getTreeSelection();
                if(!treeSelection){
                    return ;
                }
                
                var name = window.prompt("Unesite naziv novog direktorija");
                if(name){
                    this.doFolderCreate(treeSelection, name);
                }
            },
            doFolderCreate:function(inDirectory, name){
                var self = this;
                self.view.disable();
                this.filesystem.createDirectory(inDirectory,name).then(function(){
                    self.view.reload();
                    self.view.enable();
                })
            }
        })
    })
