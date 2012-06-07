/* 
 *
 */


define(
    ['dojo/_base/declare','dojo/text!./templates/ExtendedEditorPane.html','dijit/_Widget',
    'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dojo/dom-style',
    'dojo/data/ObjectStore','dojo/_base/lang',

    'dijit/layout/TabContainer','dijit/layout/ContentPane','dojox/grid/DataGrid',
    'dijit/form/FilteringSelect','dijit/form/CheckBox'],function
    (declare,templateString,_Widget,
        _TemplatedMixin, _WidgetsInTemplateMixin, domStyle,
        ObjectStore,lang)
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
                this.pluginId=0;
            },
            postCreate: function(){
                this.inherited(arguments);
                this.pluginGrid.set("store",new ObjectStore({
                    objectStore:this.gridStore
                    }));
                this.typeSelect.set("store",this.typeStore);
                this.regionSelect.set("store",this.regionStore);
                this.formRegionSelect.set("store",this.regionStore);
                this.pluginGrid.startup();
                this.tabContainer.containerNode.style.overflow="auto";
                
                var self = this;
                this.pluginGrid.on("rowClick",function(e){
                    var items = e.grid.selection.getSelected();
                    if(items.length){
                        self.onItemSelect(items[0]);
                    }
                });
            },
            resize: function(){
                var domBox = this.getDomNodeBox();
                this.tabContainer.set("style","height:"+domBox.h+"px");
                this.tabContainer.resize();
                
                var tabBox = this.getTabContainerBox();
                var tabSize = 30;
                var regionSelectionSize = 30;
                var formSize = 200
                ;
                var minGridSize = 200;
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
            disable:function(){
                this.tabContainer.set("disabled",true);
                this.regionSelect.set("disabled",true);
                this.pluginGrid.set("disabled",true);
                this.nameText.set("disabled",true);
                this.descriptionText.set("disabled",true);
                this.typeSelect.set("disabled",true);
                this.formRegionSelect.set("disabled",true);
                this.weightText.set("disabled",true);
                this.disabledCheckBox.set("disabled",true);
                this.saveButton.set("disabled",true);
                this.customEditButton.set("disabled",true);
                this.removeButton.set("disabled",true);
                this.addButton.set("disabled",true);
            },
            enable:function(){
                this.tabContainer.set("disabled",false);
                this.regionSelect.set("disabled",false);
                this.pluginGrid.set("disabled",false);
                this.nameText.set("disabled",false);
                this.descriptionText.set("disabled",false);
                this.typeSelect.set("disabled",false);
                this.formRegionSelect.set("disabled",false);
                this.weightText.set("disabled",false);
                this.disabledCheckBox.set("disabled",false);
                this.saveButton.set("disabled",false);
                this.customEditButton.set("disabled",false);
                this.removeButton.set("disabled",false);
                this.addButton.set("disabled",false);
            },
            addChild: function(pane){
                this.tabContainer.addChild(pane);
            },
            reloadGrid: function(){
                
            },
            _setFormAttr: function(plugin){
                this.nameText.set("value",plugin.name);
                this.descriptionText.set("value",plugin.description);
                
                this.typeSelect.set("value",plugin.type);
                this.formRegionSelect.set("value",plugin.region);
                this.weightText.set("value",plugin.weight);
                this.disabledCheckBox.set("value",plugin.enabled?"on":"off");
                this.pluginId = plugin.id;
            },
            _getFormAttr:function(){
                return {
                    'id':this.pluginId,
                    'name':this.nameText.get("value"),
                    'description':this.descriptionText.get("value"),
                    "type":this.typeSelect.get("value"),
                    'region':this.formRegionSelect.get("value"),
                    'weight':this.weightText.get("value"),
                    "enabled":this.disabledCheckBox.get("value")=="on"
                };
            },
            _onSave:function(){
                var f = this.get('form');
                this.onSave(f.id,f.name,f.description,f.type,f.region,f.weight,f.enabled);
            },
            _onExtendedEdit:function(){
                var f= this.get('form');
                this.onExtendedEdit(f.id,f.type);
            },
            _onDelete: function(){
                this.onDelete(this.pluginId);
            },
            _onCreate : function(){
                var f = this.get('form');
                this.onNew(f.name,f.description,f.type,f.region,f.weight,f.enabled);
            },
            _onStateChange: function(){
                var f = this.get('form');
                var enabled = f.enabled;
                
                if(enabled){
                    this.onEnable(f.id,f.weight);
                } else {
                    this.onDisable(f.id);
                }
            },
            onNew: function(name,description,type,region,weight,enable){},
            onSave: function(pluginId, name,description,region,weight,enable){},
            onDelete: function(pluginId){},
            onDisable: function(pluginId){},
            onEnable: function(pluginId, weight){},
            onExtendedEdit: function(pluginId,type){},
            onItemSelect: function(item){}
        })
    })