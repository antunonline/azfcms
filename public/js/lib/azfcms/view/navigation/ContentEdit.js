/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(
    ['dojo/_base/declare','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dijit/_Widget',
    'dojo/text!./templates/ContentEdit.html','dojo/i18n!azfcms/resources/nls/view',
    'dijit/layout/BorderContainer','dijit/layout/ContentPane',
    'dijit/form/FilteringSelect','dijit/form/TextBox','dijit/form/Textarea'
    ],function
    (declare, _TemplatedMixin,_WidgetsInTemplate,_Widget,
        templateString,nls)
        {
        var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString:templateString,
            closable:true,
            editorPlugin:null,
            constructor: function(args){
                // Set template substitution values
                this.cepContentType = nls.cepContentType;
                this.cepPageTitle = nls.cepPageTitle;
                this.cepPageDescription = nls.cepPageDescription;
                this.cepPageKeywords = nls.cepPageKeywords;
                this.cepChangeType = nls.cepChangeType;
                this.cepSave = nls.cepSave;
           
           
                this.typeStore = args.store;
            //           this.typeStore = args.typeStore;
            },
       
            postCreate: function(){
                this.inherited(arguments);
           
                // Set filteringselect store
                this.pageType.set('store',this.typeStore);
            },   
            resize:function(){
                this.borderContainer.resize();
            },
       
            /**
        * UI events
        */
            _save: function(){
                var title = this.pageName.get("value");
                var description = this.pageDescription.get("value");
                var keywords = this.pageKeywords.get("value");
           
                this.onMetadataSave(title,description,keywords);
            },
            
            _onChangeType:function(){
                var type = this.pageType.get("value");
                this.onTypeChange(type);
            },
       
            addChild: function(child){
                if(this.editorPlugin){
                    this.borderContainer.removeChild(this.editorPlugin);
                    
                    if(this.editorPlugin.destroyRecursive){
                        try{
                            this.editorPlugin.destroyRecursive();
                        }catch(e){
                            this.editorPlugin.destroy()
                        }
                    }else {
                        this.editorPlugin.destroy();
                    }
                    
                }
                this.editorPlugin = child;
                this.borderContainer.addChild(child);
            },
       
       
       
            _setTitleAttr: function(title){
                this.pageName.set("value",title);
            },
       
            _setDescriptionAttr: function(description){
                this.pageDescription.set("value",description);
            },
       
            _setKeywordsAttr: function(keywords){
                this.pageKeywords.set("value",keywords);
            },
       
            onMetadataSave: function(title, description, keywords){
           
            },
            
            onTypeChange:function(type){
                
            }
        }) ;
   
        return _class;
    });


