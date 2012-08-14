define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UserForm.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'dojo/_base/lang','dojo/keys',
    
    'dijit/layout/BorderContainer','dijit/Toolbar','dijit/form/Button',
    'dojox/grid/DataGrid','dijit/form/TextBox','dijit/form/Button',
    'dijit/layout/ContentPane'

],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,keys
    
    )
{
    var _class = declare('azfcms.view.configuration.AclManagment.UserForm',[_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        constructor:function(){
            
            /** REQURED **/
            /**
             * Store that will feed the data grid
             */
            this.store;
            
            
            
            /** Attach points **/
            
            /**
             * Toolbar 
             */
            this.toolbar;
            
          
          /**
           * Acl group name textbox used for group filtering
           */
          this.aclGroupNameInput;
          
          /**
           * User loginName textbox used for user filtering
           */
          this.userLoginNameInput;
          
          /**
           * Button that is used to initiate search
           */
          this.searchButton;
          
          /**
           * Grid that contains listing of users and groups
           */
          this.grid;
          
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
                userLoginName:this.userLoginNameInput.get('value'),
                aclGroupName:this.aclGroupNameInput.get('value')
            }
        },
        
        reload:function(query){
            this.grid.setQuery(query);
        },
        
        _onInputKeyPress:function(e){
            if(e.keyCode==keys.ENTER){
                this._onSearchClick(e);
            }
        },
        
        _onSearchClick:function(e){
            e.preventDefault();
            e.stopPropagation();
            this.onSearch(this.get('query'));
        },
        
        onSearch:function(query){
            /**
             * Will be called when the user activates search button or
             * search text box
             */
            this.doSearch(query);
        },
        
        onSave:function(e){
            e.stopPropagation();
            e.preventDefault();
            /**
             * Will Be activated when the user activates save button
             */
            this.doSave();
        },
        
        doSearch:function(query){
            this.grid.setQuery(query);
        },
        
        doSave:function(){
            this.grid.store.save();
        }
            
    });
    
    return _class;
})