define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UserManagment.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/UserManagment',
    'dojo/_base/lang',
    'dijit/form/Button','azfcms/view/configuration/UserManagment/UserGrid',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,Button,UserGrid, UserForm)
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
        
        /**
         * Add widget to tab container
         * @param {dijit._Widget} widget
         */
        addChild:function(widget){
            this.tabContainer.addChild(widget);
            this.resize();
        },
        
        
        /**
         * Add new action button into toolbar
         * @param {String} label
         * @param {Function} onClick
         */
        addActionButton:function(label,onClick){
            this.toolbar.addChild(new Button({
                label:label,
                onClick:onClick
            }));
        }
            
    });
    
    return _class;
})