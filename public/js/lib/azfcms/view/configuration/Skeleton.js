define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/{ucName}.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/{ucName}',
    'dojo/_base/lang'

],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang
    
    )
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        constructor:function(){
          
          this.init();
        },
        templateString: templateString,
        closable:true,
        nls:nls,
        init:function(){
            // In this method you can initialize the view
        }
            
    });
    
    return _class;
})