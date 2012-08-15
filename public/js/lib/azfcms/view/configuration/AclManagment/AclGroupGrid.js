define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/AclGroupGrid.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'dojo/_base/lang','dojo/keys',
    
    'dijit/form/Button','dijit/form/TextBox','dijit/layout/BorderContainer',
    'dijit/layout/ContentPane','dijit/Toolbar','dojox/grid/DataGrid'

],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,keys
    
    )
{
    var _class = declare('azfcms.view.configuration.AclManagment.AclGroupGrid',[_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        constructor:function(){
          
          /**
           * Reference of the AclGroupStore
           */
          this.store;
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
        },
        
        _getQueryAttr:function(){
            return {
                name:this.nameInput.get('value')
            }
        },
        
        
        
        _fireSearch:function(){
            this.onSearch(this.get('query'));
        },
        
        _fireKeySearch:function(e){
            if(e.keyCode==keys.ENTER){
                this._fireSearch();
            }
            e.stopPropagation();
        },
        
        onSearch:function(query){
            this.grid.setQuery(query);
        }
            
    });
    
    return _class;
})