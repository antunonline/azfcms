
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
                
                // Set labels
                this.createButton.set("label",nls.cpdNewPageCreateButton);
            },
            
            _save: function(){
                this.onAction(this.pageName.get("value"),this.pageType.get("item").pluginIdentifier);
            },
            
            onAction: function(title, type){
                
            }
        })    
            
        var _class = declare([Dialog],{
            constructor: function(args){
                var context = this;
                
                // Set style
                this.style = "width:400px;height:200px;"
                this.title = nls.cpdTitle
                
                // Create form widget
                this.form = new _Form();
                this.store = args.store;
                // Bind events
                this.form.on("action",function(name,type){
                    context.onAction(name,type);
                });
                
                // Set select types
                this.form.pageType.set("store",args.store);
                
            },
            
            postCreate: function(){
                this.inherited(arguments);
                // Set form widget body
                this.set("content",this.form);
                
            },
            
            onAction: function(title,type){
                
            }
        });
    
        // return class
        return _class;
    })
