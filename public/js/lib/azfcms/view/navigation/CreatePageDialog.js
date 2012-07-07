
define(
    ['dojo/_base/declare','dijit/Dialog',
    'dojo/i18n!azfcms/resources/nls/view','dijit/_TemplatedMixin','dijit/_Widget',
    'dojo/text!./templates/CreatePageDialog.html','dijit/_WidgetsInTemplateMixin','dijit/form/FilteringSelect',
    'dijit/form/Button'],function
    (declare,Dialog,
        nls,_TemplatedMixin,_Widget,
        templateString, _WidgetsInTemplateMixin){
        var _Form = declare([_Widget,_TemplatedMixin,_WidgetsInTemplateMixin],{
            templateString:templateString,
            widgetsInTemplate:true,
            constructor: function(){
                // Set label values
                this.newPageLabel = nls.cpdNewPageNameLabel;
                this.newPageType = nls.cpdNewPageTypeLabel;
            },
            
            postCreate: function(){
                this.inherited(arguments);
                
                // Set style
                this.set("style","height:100%");
                
                // Set labels
                this.createButton.set("label",nls.cpdNewPageCreateButton);
            },
            
            _save: function(){
                if(this.pageType.isValid()){
                    this.onAction(this.pageName.get("value"),this.pageType.get("item").pluginIdentifier);
                }
                
            },
            
            disable: function(){
                this.pageName.set("disabled",true);
                this.pageType.set("disabled",true);
                this.createButton.set("disabled",true);
            },
            enable: function(){
                this.pageName.set("disabled",false);
                this.pageType.set("disabled",false);
                this.createButton.set("disabled",false);
            },
            
            onAction: function(title, type){
                
            },
            
            reset: function(){
                this.pageName.set("value","");
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
                this.store = args.store;
                
                // Set select types
                this.form.pageType.set("store",args.store);
                
            },
            
            postCreate: function(){
                this.inherited(arguments);
                // Set form widget body
                this.set("content",this.form);   
            },
            
            show: function(title){
                this.form.set("message",title);
                this.inherited(arguments);
            }
        });
    
        // return class
        return _class;
    })
