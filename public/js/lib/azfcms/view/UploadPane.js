/* 
 * @author antunonline@gmail.com
 */

define(
    ['dojo/_base/declare','dojo/text!./templates/UploadPane.html','dojo/_base/lang',
    'dijit/_TemplatedMixin','dijit/_Widget','dijit/_WidgetsInTemplateMixin',
    'dojo/i18n!azfcms/resources/nls/view',

    'dijit/form/Button','dijit/form/TextBox'],function
    (declare, templateString, lang,
    _TemplatedMixin,_Widget,_WidgetsInTemplateMixin,
    nls, Uploader)
    {
        return declare([_Widget,_TemplatedMixin,_WidgetsInTemplateMixin],{
            widgetsInTemplate:true,
            templateString:templateString,
            uplMessage:"",
            cancelLabel:"",
            uploadLabel:"",
            uploadButton:null,
            cancelButton:null,
            file1Input:null,
            file2Input:null,
            file3Input:null,
            form:null,
            constructor:function(args){
                this.upUploadMessage =nls.upUploadMessage;
                this.upSubmitMessage =nls.upSubmitMessage;
                this.upCancelMessage =nls.upCancelMessage;
            },
            destroy:function(){
                this.inherited(arguments);
            },
            enable:function(){
                this._disable(false);
            },
            disable:function(){
                this._disable(true);
            },
            _disable:function(disable){
                this.file1Input.disabled = disable?"disabled":"";
                this.file2Input.disabled = disable?"disabled":"";
                this.file3Input.disabled = disable?"disabled":"";
            },
            _onUpload:function(e){
                e.preventDefault();
                e.stopPropagation();
                this.onUpload(this.getForm());
            },
            _onCancel:function(e){
                e.preventDefault();
                e.stopPropagation();
                this.onCancel()
            },
            onUpload:function(form){},
            onCancel:function(){},
            reset:function(){
                this.getForm().reset();
            },
            getForm:function(){
                return this.form;
            }
        });
    })


