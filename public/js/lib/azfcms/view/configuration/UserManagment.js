define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UserManagment.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/UserManagment',
    'dijit/form/Button',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane','azfcms/view/configuration/UserManagment/UserGrid'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,Button)
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
            
            /**
             * User store
             */
            this.userStore;
            
            this.init();
        },
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        templateString: templateString,
        closable:true,
        nls:nls,
        init:function(){
            
        },
        
        /**
         * Add widget to tab container
         * @param {dijit._Widget} widget
         */
        addChild:function(widget){
            this.tabContainer.addChild(widget);
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