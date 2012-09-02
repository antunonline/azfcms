define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UserGrid.html','dojo/i18n!../resource/i18n/nls/UserManagment',
    'dojo/_base/lang','dojo/data/ObjectStore','dojo/keys',
    
    'dijit/layout/BorderContainer','dojox/grid/DataGrid',
    'dijit/Toolbar','dijit/form/Button','dijit/layout/ContentPane','dijit/form/TextBox'

],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,ObjectStore,keys
    
    )
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        constructor:function(props){
            /**
             * dojox.grid.DataGrid reference
             */
          this.grid;
          
          /**
           * dojo.data.* or dojo.store* compatible store implementation that
           * will be used as data source for data grid
           */
          this.init();
        },
        templateString: templateString,
        closable:false,
        title:nls.userGridTabTitle,
        nls:nls,
        init:function(){
            // In this method you can initialize the view
        },
        
        postCreate:function(){
            this.inherited(arguments);
            var store = null;
            if(this.store.getFeatures){
                store = this.store;
            } else {
                store = new ObjectStore({
                    objectStore:this.store
                });
            }
            
            this._attachWidgetEvents();
            this.grid.setStore(store,{});
        },
        
        resize:function(){
            this.borderContainer.resize();
        },
        
        _attachWidgetEvents:function(){
            
            var searchFnc = function(e){
                if(e.keyCode==keys.ENTER){
                    this._onSearch();
                }
            };
            
            this.loginNameTextBox.on("keyPress",lang.hitch(this,searchFnc))
            this.emailTextBox.on("keyPress",lang.hitch(this,searchFnc))
        },
        
        _getLoginNameAttr:function(){
            return this.loginNameTextBox.get('value');
        },
        
        _getEmailAttr:function(){
            return this.emailTextBox.get('value');
        },
        
        _getValueAttr:function(){
            return {
                loginName:this.get('loginName'),
                email:this.get('email')
            };
        },
        
        reloadGrid:function(){
            this._onSearch();
        },
        
        _onSearch:function(){
            this.onSearch(this.get('value'));
        },
        
        _onSelect:function(){
            var selectedRows = this.grid.selection.getSelected();
            if(selectedRows.length>0){
                this.onSelect(selectedRows[0]);
            }
        },
        
        onSearch:function(user){
            /**
             * user.loginName  Searched login name of the user
             * 
             * user.email   Searched email address of the user
             */
            this.grid.setQuery(user);
        },
        onSelect:function(user){
            
        },
        
        onEdit:function(){
            /**
             * Will be activated when the user double clicks the row
             */
        }
            
    });
    
    return _class;
})