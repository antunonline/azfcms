/* 
 * @author antunonline@gmail.com
 */

define(
    ['dojo/_base/declare','dijit/Dialog','azfcms/view/ConfirmPane',
    'dojo/i18n!azfcms/resources/i18n/cms/common/nls/common','dojo/_base/lang','azfcms/store/Filesystem',
    'azfcms/view/FileSelectPane','dojo/string'],function
    (declare,Dialog,ConfirmPane,
        nls,lang,Filesystem,
        FileSelectPane,string)
        {
        return declare([],{
            FILE_TYPE_ALL:"all",
            FILE_TYPE_IMAGES:"images",
            FILE_TYPE_AUDIO:"audio",
            FILE_TYPE_VIDEO:"video",
            FILE_TYPE_DIRECTORY:"directory",
    
            $_FILE_TYPE_ALL:[],
            $_FILE_TYPE_IMAGES:['jpg','jpeg','gif','png'],
            $_FILE_TYPE_AUDIO:['mp3','ogg','wav'],
            $_FILE_TYPE_VIDEO:['avi','mpeg4','mpeg2','flv','mkv'],
    
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
            confirm:function(callback,message,messageMap,acceptLabel,rejectLabel){
                messageMap=messageMap||{};
                
                var confirmDialog = this.getConfirmDialog();
                this.getConfirmPane().confirm(function(value){
                    callback(value);
                    confirmDialog.hide();
                },string.substitute(message,messageMap),acceptLabel,rejectLabel);
                this.getConfirmDialog().show();
            },
            alert:function(message,messageMap){
                messageMap=messageMap||{};
                window.alert(string.substitute(message,messageMap));
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
                    var typePropName = "$_"+fileType;
                    if(this[typePropName]){
                        fileFilter.extensions = this[typePropName];
                    } else if(fileType == this.FILE_TYPE_DIRECTORY){
                        fileFilter.file = false;
                    }
                } else if(lang.isArray(fileType)){
                    fileFilter.extensions = fileType;
                }
                
                var fileBrowser = this.$_getFileSelectPane();
                var fileDialog = this.$_getFileSelectDialog();
                var self = this;
                this.$_fileSelectStore.getOptions = fileFilter;
                
                fileDialog.set("title",message);
                
                this.$_fileSelectConnects.push(fileBrowser.on("select",function(selection){
                    fileDialog.hide();
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


