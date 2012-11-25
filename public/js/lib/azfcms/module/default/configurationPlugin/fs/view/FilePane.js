define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/FilePane.html','dojo/i18n!../resource/i18n/nls/Fs',
    'dojo/_base/lang','azfcms/module/default/view/AbstractEditPane',
    'dijit/form/Button',
    
    'dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane','dijit/form/TextBox','dijit/form/SimpleTextarea',
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,AbstractEditPane,
    Button)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate, AbstractEditPane],{
        constructor:function(args){
            /**
             * Toolbar reference
             */
            this.toolbar;
            
            this.basePath = args.basePath;
            
            this.title = "Nova dadoteka"; // nls.tabTitle
            
            // Form layout
            if(!this.layout){
                // defaults to edit
                this.layout = this.LAYOUT_EDIT;
            }
            
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
            if(this.onSave){
                this.nameInput.set('disabled',true);
            }
        },
        
        
        resize:function(){
            this.inherited(arguments);
            this.borderContainer.resize();
        },
        
        _getValueAttr:function(){
            var values = this.get("inputValues");
            values.path = this.path;
            
            return values;
        },
        
        _setValueAttr:function(values){
            this.set("path",values.path);
            this.set("inputValues",values);
        },
        
        _onSave:function(){
            if(this.onCreate){
                this.onCreate(this, this.get('value'));
            } else if(this.onSave) {
                this.onSave(this, this.get('value'));
            }
        }
        
        
//        onCreate:function(pane, value){
//            
//        },
        
//        onSave:function(pane, value){
//            
//        }
            
    });
    
    return _class;
})