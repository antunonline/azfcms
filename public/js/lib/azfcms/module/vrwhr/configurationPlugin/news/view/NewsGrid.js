define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/NewsGrid.html','dojo/i18n!../resource/i18n/nls/News',
    'dojo/_base/lang','dojo/keys',
    'dijit/form/Button',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane','dijit/layout/TabContainer','dojox/grid/DataGrid',
    'dijit/form/TextBox'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,keys)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        constructor:function(args){
            
            this.title = nls.tabTitle;
            
            this.init();
        },
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        templateString: templateString,
        closable:false,
        title:nls.tabName,
        nls:nls,
        postCreate:function(){
            this.inherited(arguments);
            this.grid.setStore(this.store,{});
        },
        init:function(){
            
        },
        
        resize:function(){
            this.inherited(arguments);
            this.borderContainer.resize();
            
        },
        
        _getSearchValueAttr:function(){
            
        },
        
        _onSearch:function(){
            this.onSearch(this.get('searchValue'));
        },
        
        _onKeyPressSearch: function(e){
            if(e.keyCode==keys.ENTER || e.keyCode==keys.NUMPAD_ENTER){
                this._onSearch();
            }
        },
        
        onSearch:function(searchValue){
           
        }
            
    });
    
    return _class;
})