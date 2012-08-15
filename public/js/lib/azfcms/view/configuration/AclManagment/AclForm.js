define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/AclForm.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'dojo/_base/lang','dojo/keys',
    
    'dijit/layout/ContentPane','dijit/layout/BorderContainer','dijit/form/Button',
    'dijit/form/TextBox','dijit/Toolbar','dojox/grid/DataGrid'
    
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,keys
    
    )
{
    var _class = declare('azfcms.view.configuration.AclManagment.AclForm',[_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        closable:false,
        constructor:function(){
          
          /**
           * Store that will feed the data grid
           */
          this.store;
          this.init();
        },
        templateString: templateString,
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
                aclResource:this.aclResourceInput.get('value'),
                aclGroupName:this.aclGroupNameInput.get('value')
            }
        },
        
        _fireKeyPressSearchEvent:function(e){
           
            if(e.keyCode == keys.ENTER){
                this._fireSearchEvent();
                 e.stopPropagation();
            }
        },
        
        _fireSearchEvent:function(){
            this.onSearch(this.get('query'));
        },
        
        
        onSearch:function(query){
            this.grid.setQuery(query);
        },
        
        onSave:function(){
            this.store.save()
        }
            
    });
    
    return _class;
})