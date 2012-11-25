define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UploadPane.html','dojo/i18n!../resource/i18n/nls/Fs',
    'dojo/_base/lang','azfcms/module/default/view/AbstractEditPane',
    'dijit/form/Button','dojo/request/iframe',
    
    'dijit/form/Form','dijit/Toolbar','dijit/layout/BorderContainer',
    'dijit/layout/ContentPane','dojox/form/Uploader','dojox/form/uploader/plugins/Flash',
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,AbstractEditPane,
    Button, request)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate, AbstractEditPane],{
        constructor:function(args){
            /**
             * Toolbar reference
             */
            this.toolbar;
            
            this.basePath = args.basePath;
            
            this.title = "Učitavanje dadoteka"; // nls.tabTitle
            
            this.model = args.model;
            
            this.init();
        },
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        templateString: templateString,
        closable:true,
        title:nls.tabName,
        nls:nls,
        init:function(){
            
        },
        
        postCreate:function(){
            this.inherited(arguments);
            var UploadPane = this;
            this.uploader.on("change",function(){
                var fileList = UploadPane.uploader.getFileList();
                UploadPane.displaySelectedFiles(fileList);
            });
        },
        
        
        resize:function(){
            this.inherited(arguments);
        },
        
        displaySelectedFiles:function(selectedFiles){
            var html = "<ul>";
            for(var i = 0, len = selectedFiles.length; i < len;i++){
                html+="<li>"+selectedFiles[i].name+"</li>";
            }
            html+="</ul>"
            this.fileListNode.innerHTML = html;
        },
        
        doUpload:function(){
            this.toolbar.set("disabled",true);
            var UploadPane = this;
            this.model.upload(this.path,this.form.domNode)
            .then(function(){
                UploadPane.onUploadComplete(UploadPane,UploadPane.path);
            })
        },
        
        onUploadComplete: function(path, UploadPane){
            
        }
        
            
    });
    
    return _class;
})