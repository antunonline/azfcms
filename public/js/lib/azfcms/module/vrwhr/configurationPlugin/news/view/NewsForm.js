define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/NewsForm.html','dojo/i18n!../resource/i18n/nls/News',
    'dojo/_base/lang',
    'dijit/form/Button',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane','dijit/form/TextBox','dijit/form/Textarea',
    'azfcms/module/default/view/form/InputContainer','azfcms/module/default/view/form/WideInputContainer'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        constructor:function(){
            /**
             * Toolbar reference
             */
            this.toolbar;
            
            this.title = nls.tabTitle;
            
            
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
        
        resize:function(){
            this.inherited(arguments);
            this.borderContainer.resize();
        },
        
        _onSave:function(){
            
        }
            
    });
    
    return _class;
})