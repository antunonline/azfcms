define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/News.html','dojo/i18n!../resource/i18n/nls/<?=$ucName?>',
    'dojo/_base/lang','azfcms/module/default/view/util',
    'dijit/form/Button',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang, util)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        constructor:function(){
            /**
             * Toolbar reference
             */
            this.toolbar;
            /**
             * Tab container ref.
             */
            this.tabContainer;
            
            this.title = nls.tabTitle;
            
            
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
            this.tabContainer.resize();
        },
        
        addChild:function(child){
            this.tabContainer.addChild(child);
            child.resize();
        },
        
        removeChild: function(child){
            this.tabContainer.removeChild(child);
        },
        
        selectChild: function(child){
            this.tabContainer.selectChild(child);
        },
        
        addToolbarChild:function(child){
            this.toolbar.addChild(child);
        },
        
        removeToolbarChild:function(child){
            this.toolbar.removeChild(child);
        },
        
        
        
        onCreate:function(){
            
        },
        
        onEdit:function(){
            
        },
        
        _onDelete:function(){
            util.confirm(lang.hitch(this,function(choice){
                if(choice){
                    this.onDelete();
                }
            }),"Želite li izbrisati izabrane artikle?");
        },
        
        onDelete:function(){
            
        }
            
    });
    
    return _class;
})