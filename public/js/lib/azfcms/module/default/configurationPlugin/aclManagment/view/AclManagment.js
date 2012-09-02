define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/AclManagment.html','dojo/i18n!../resource/i18n/nls/AclManagment',
    'dojo/_base/lang','./AclGroupForm',
    'dijit/form/Button',
    
    'dijit/layout/BorderContainer','dijit/layout/ContentPane','dijit/Toolbar',
    './UserForm','dijit/layout/TabContainer',
    './AclForm','./AclGroupGrid'

],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
nls,lang,AclGroupForm,Button
    
)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        constructor:function(){
          
            /**
             * AclGroupGrid widget reference
             */
            this.aclGroupGrid;
            this.init();
        },
        templateString: templateString,
        title:nls.tabTitle,
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
        },
        
        addChild:function(child){
            this.tabContainer.addChild(child);
        },
        
        removeChild:function(child){
            this.tabContainer.removeChild(child);
        },
        
        addToolbarButton:function(params){
            this.toolbar.addChild(new Button(params));
        },
        
        createNewGroupForm:function(params){
            var aclGroupForm = new AclGroupForm(lang.mixin({
                title:this.nls.newGroupTabTitle
            },params||{}));
            this.addChild(aclGroupForm);
            return aclGroupForm;
        },
        
        reloadAclGroupGrid:function(){
            this.aclGroupGrid.reload();
        },
        
        
        _getSelectedRowAttr:function(){
            var selection = this.aclGroupGrid.grid.selection.getSelected();
            if(selection.length>0){
                return selection[0];
            } else {
                return null;
            }
        }
            
        });
    
        return _class;
    })