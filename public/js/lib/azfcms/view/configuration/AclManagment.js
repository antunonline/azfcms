define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/AclManagment.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'dojo/_base/lang',
    
    'dijit/layout/BorderContainer','dijit/layout/ContentPane','dijit/Toolbar',
    'azfcms/view/configuration/AclManagment/UserForm','dijit/layout/TabContainer'

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
        },
        /**
         * Implement resize method. Call all direct descendants resize methods.
         */
        resize:function(){
            this.borderContainer.resize();
        }
            
    });
    
    return _class;
})