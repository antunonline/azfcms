
define(
    ['dojo/_base/declare','dijit/Dialog',
    'dojo/i18n!azfcms/resources/nls/view','dijit/_TemplatedMixin','dijit/_Widget',
    'dojo/text!./templates/DeletePageDialog.html','dijit/_WidgetsInTemplateMixin',
    'dojo/string',
    'dijit/form/Button','dijit/form/TextBox'],function
    (declare,Dialog,
        nls,_TemplatedMixin,_Widget,
        templateString, _WidgetsInTemplateMixin,
        string){
            
        var _Form = declare([_Widget,_TemplatedMixin,_WidgetsInTemplateMixin],{
            templateString:templateString,
            widgetsInTemplate:true,
            constructor: function(){
                // Set label values
                this.newPageLabel = nls.cpdNewPageNameLabel;
                this.newPageType = nls.cpdNewPageTypeLabel;
            },
            
            disable: function(){
                this.confirmButton.set("disabled",true);
                this.cancelButton.set("disabled",true);
            },
            enable: function(){
                this.confirmButton.set("disabled",false);
                this.cancelButton.set("disabled",false);
            },
            _setMessageAttr: function(message){
                var template = nls.dpdMessage;
                this.message.innerHTML = string.substitute(template,[message]);
            },
            
            onCancel: function(){
                
            },
            onConfirm: function(){
                
            }
            
        })    
            
        var _class = declare([Dialog],{
            constructor: function(args){
                var context = this;
                
                // Set style
                this.style = "width:400px;"
                this.title = nls.cpdTitle
                
                // Create form widget
                this.form = new _Form();
                
                
            },
            
            postCreate: function(){
                this.inherited(arguments);
                // Set form widget body
                this.set("content",this.form);
                
            }
        });
    
        // return class
        return _class;
    })
