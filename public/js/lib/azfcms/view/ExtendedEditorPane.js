/* 
 *
 */


define(
    ['dojo/_base/declare','dojo/text!./templates/ExtendedEditorPane.html','dijit/_Widget',
    'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dojo/dom-style',
    'dojo/data/ObjectStore',

    'dijit/layout/TabContainer','dijit/layout/ContentPane','dojox/grid/DataGrid',
    'dijit/form/FilteringSelect','dijit/form/CheckBox'],function
    (declare,templateString,_Widget,
        _TemplatedMixin, _WidgetsInTemplateMixin, domStyle,
        ObjectStore)
        {
        return declare([_Widget,_TemplatedMixin, _WidgetsInTemplateMixin],{
            templateString: templateString,
            constructor: function(args){
                
                this.tabContainer=null,
                this.regionSelect=null;
                this.pluginGrid=null;
                this.nameText=null;
                this.descriptionText=null;
                this.typeSelect=null;
                this.formRegionSelect=null;
                this.weightText=null;
                this.disabledRadio=null;
                this.saveButton=null;
                this.customEditButton=null;
                this.removeButton=null;
                this.addButton=null;
                this.regionStore=null;
                this.typeStore=null;
                this.gridStore=null;
                this.regionSelectionNode=null;
                this.formNode=null;
            },
            postCreate: function(){
                this.inherited(arguments);
                this.pluginGrid.set("store",new ObjectStore({objectStore:this.gridStore}));
                this.typeSelect.set("store",this.typeStore);
                this.regionSelect.set("store",this.regionStore);
                this.formRegionSelect.set("store",this.regionStore);
                this.pluginGrid.startup();
                this.tabContainer.containerNode.style.overflow="auto";
            },
            resize: function(){
                var domBox = this.getDomNodeBox();
                this.tabContainer.set("style","height:"+domBox.h+"px");
                this.tabContainer.resize();
                
                var tabBox = this.getTabContainerBox();
                var tabSize = 30;
                var regionSelectionSize = 30;
                var formSize = 200
;                var minGridSize = 200;
                var gridSize = 0;
                var minSize = tabSize+regionSelectionSize+formSize+minGridSize;
                
                // If there is more space available
                if(minSize<tabBox.h){
                    gridSize = tabBox.h-tabSize-regionSelectionSize-formSize;
                }
                // otherwise keep current sizes
                else {
                    gridSize = minGridSize;
                }
                
                this.setChildHeights(regionSelectionSize, gridSize, formSize);
            },
            getDomNodeBox: function(){
                var cs = domStyle.getComputedStyle(this.domNode);
                
                return {
                    w:parseInt(cs.width),
                    h:parseInt(cs.height)
                }
            },
            getTabContainerBox: function(){
                var cs = domStyle.getComputedStyle(this.tabContainer.containerNode);
                return {
                    w:parseInt(cs.width),
                    h:parseInt(cs.height)
                }
            },
            setChildHeights:function(regionSelectionSize, gridSize, formSize){
                this.regionSelectionNode.style.height = regionSelectionSize +"px";
                this.pluginGrid.set("style","height:"+gridSize+"px");
                this.pluginGrid.resize();
                this.formNode.style.height = formSize+"px";
            },
            disable:function(){},
            enable:function(){},
            addChild: function(pane){},
            reloadGrid: function(){},
            _setFormAttr: function(plugin){},
            _onSave:function(){},
            _onExtendedEdit:function(){},
            _onDelete: function(){},
            _onCreate : function(){},
            onNew: function(name,description,type,region,weight,enable){},
            onSave: function(pluginId, name,description,region,weight,enable){},
            onDelete: function(pluginId){},
            onDisable: function(navigationId, pluginId){},
            onEnable: function(navigationId, pluginId, weight){},
            onExtendedEdit: function(pluginId,type){},
            onItemSelect: function(item){}
        })
    })