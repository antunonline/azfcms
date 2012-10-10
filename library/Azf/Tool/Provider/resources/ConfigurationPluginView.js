define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/<?=$ucName?>.html','dojo/i18n!../resource/i18n/nls/<?=$ucName?>',
    'dojo/_base/lang',
    'dijit/form/Button',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        constructor:function(){
            /**
             * Border container reference
             */
            this.borderContainer;
            /**
             * Toolbar reference
             */
            this.toolbar;
            /**
             * Tab container ref.
             */
            this.tabContainer;
            
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
            
        }
            
    });
    
    return _class;
})