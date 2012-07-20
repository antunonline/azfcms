/* 
 * @author antunonline@gmail.com
 */

define(
    ['dojo/_base/declare','dijit/Dialog','azfcms/view/ConfirmPane',
    'dojo/i18n!azfcms/resources/nls/view','dojo/_base/lang','azfcms/store/Filesystem',
    'azfcms/view/FileSelectPane'],function
    (declare,Dialog,ConfirmPane,
        nls,lang,Filesystem,
        FileSelectPane)
        {
        return declare([],{
            FILE_FILTER_ALL:"all",
            FILE_FILTER_IMAGES:"images",
            FILE_FILTER_AUDIO:"audio",
            FILE_FILTER_VIDEO:"video",
    
            $_FILE_FILTER_ALL:[],
            $_FILE_FILTER_IMAGES:['jpg','jpeg','gif','png'],
            $_FILE_FILTER_AUDIO:['mp3','ogg','wav'],
            $_FILE_FILTER_VIDEO:['avi','mpeg4','mpeg2','flv','mkv'],
    
            $_fileSelectStore:null,
            $_fileSelectDialog:null,
            $_getFileSelectionPane:null,
            $_fileSelectConnects:[],
    
            _confirmDialog:null,
            _confirmPane:null,
            alert:function(message){},
            getConfirmPane:function(){
                if(this._confirmPane)
                    return this._confirmPane;
                this._confirmDialog = new Dialog({
                    title:nls.utlConfirmDialogTitle
                });
                var pane = this._confirmPane= new ConfirmPane();
                this._confirmDialog.on("cancel",function(){
                    pane.fireReject();
                })
                this._confirmDialog.set("content",this._confirmPane);
                return this._confirmPane;
            },
            getConfirmDialog:function(){
                if(!this._confirmDialog)
                    this.getConfirmPane();
                return this._confirmDialog;
            },
            confirm:function(callback,message,acceptLabel,rejectLabel){
                var confirmDialog = this.getConfirmDialog();
                this.getConfirmPane().confirm(function(value){
                    callback(value);
                    confirmDialog.hide();
                },message,acceptLabel,rejectLabel);
                this.getConfirmDialog().show();
            },
            alert:function(message){
                window.alert(message);
            },
            
            $_getFileSelectPane:function(){
                if(this.$_fileSelectPane){
                    return this.$_fileSelectPane;
                }
                
                this.$_fileSelectStore = new Filesystem({
                    isTreeModel:true
                })
                
                this.$_fileSelectPane = new FileSelectPane({
                    treeStore:this.$_fileSelectStore
                });
                
                return this.$_fileSelectPane;
            },
            
            $_getFileSelectDialog:function(){
                if(!this.$_fileSelectDialog){
                    this.$_fileSelectDialog = new Dialog();
                    this.$_fileSelectDialog.set("content",this.$_getFileSelectPane());
                }
                return this.$_fileSelectDialog;
            },
            
            $_clearFileSelectConnections: function(){
                var connect;
                while(connect = this.$_fileSelectConnects.pop()){
                    connect.remove();
                }
            },
            
            selectFiles:function(callback,fileType,message){
                var fileFilter = {}
                
                if(lang.isString(fileType)){
                    var filterPropName = "$_"+fileType;
                    if(this[filterPropName]){
                        fileFilter.extensions = this[filterPropName];
                    }
                } else if(lang.isArray(fileType)){
                    fileFilter.extensions = fileType;
                }
                
                var fileBrowser = this.$_getFileSelectPane();
                var fileDialog = this.$_getFileSelectDialog();
                var self = this;
                
                fileDialog.set("message",message);
                
                this.$_fileSelectConnects.push(fileBrowser.on("select",function(selection){
                    callback(selection);
                    self.$_clearFileSelectConnections();
                }))
                
                this.$_fileSelectConnects.push(fileBrowser.on("cancel",function(){
                    fileDialog.hide();
                    callback([]);
                    self.$_clearFileSelectConnections();
                }))
                
                this.$_fileSelectConnects.push(fileBrowser.on("reload",function(){
                    self.$_fileSelectStore.updateModelRootChildren();
                }))
                
                this.$_fileSelectConnects.push(fileDialog.on("cancel",function(){
                    callback([]);
                    self.$_clearFileSelectConnections();
                }))
                
                fileDialog.show();
                this.$_fileSelectStore.updateModelRootChildren();
                
            }
        });
    })


