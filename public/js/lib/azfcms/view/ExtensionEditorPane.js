/* 
 *
 */


define(
    ['dojo/_base/declare','dojo/text!./templates/ExtendedEditorPane.html','dijit/_Widget',
    'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dojo/dom-style',
    'dojo/data/ObjectStore','dojo/_base/lang','azfcms/model/cms','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common',
    'dijit/layout/ContentPane',

    'dijit/layout/TabContainer','dojox/grid/DataGrid','dijit/layout/BorderContainer',
    'dijit/form/FilteringSelect','dijit/form/CheckBox','dijit/form/TextBox','dijit/form/NumberSpinner'],function
    (declare,templateString,_Widget,
        _TemplatedMixin, _WidgetsInTemplateMixin, domStyle,
        ObjectStore,lang,cms,nls,
        ContentPane)
        {
        return declare([_Widget,_TemplatedMixin, _WidgetsInTemplateMixin],{
            templateString: templateString,
            constructor: function(args){
                
                this.tabContainer=null,
                this.pluginGrid=null;
                this.nameText=null;
                this.descriptionText=null;
                this.typeSelect=null;
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
                this.model = cms;
                
                for(var name in nls){
                    if(name.indexOf("eep")==0){
                        this[name] = nls[name];
                    }
                }
                
            },
            postCreate: function(){
                this.inherited(arguments);
                
                if(typeof this.gridStore.getFeatures == 'undefined'){
                    var gridStore= new ObjectStore({
                        objectStore:this.gridStore
                    });
                }
                
                    

                this.pluginGrid.set("store",gridStore||this.gridStore);
                this.typeSelect.set("store",this.typeStore);
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
                this.pluginGrid.on("rowDblClick",function(){
                    self._onExtendedEdit();
                })
            },
            resize:function(){
                this.tabContainer.resize();
                this.borderContainer.resize();
                this.pluginGrid.resize();
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
            disable:function(){
                this.tabContainer.set("disabled",true);
                this.pluginGrid.set("disabled",true);
                this.nameText.set("disabled",true);
                this.descriptionText.set("disabled",true);
                this.typeSelect.set("disabled",true);
                this.weightText.set("disabled",true);
                this.saveButton.set("disabled",true);
                this.customEditButton.set("disabled",true);
                this.removeButton.set("disabled",true);
                this.addButton.set("disabled",true);
            },
            enable:function(){
                this.tabContainer.set("disabled",false);
                this.pluginGrid.set("disabled",false);
                this.nameText.set("disabled",false);
                this.descriptionText.set("disabled",false);
                this.typeSelect.set("disabled",false);
                this.formRegionSelect.set("disabled",false);
                this.weightText.set("disabled",false);
                this.saveButton.set("disabled",false);
                this.customEditButton.set("disabled",false);
                this.removeButton.set("disabled",false);
                this.addButton.set("disabled",false);
            },
            addChild: function(pane){
                this.tabContainer.addChild(pane);
            },
            reloadGrid: function(region){
                this.pluginGrid.setQuery({});
            },
            resetForm:function(){
                this.set("form",{
                    pluginId:"",
                    name:"",
                    description:"",
                    type:"",
                    region:"",
                    weight:0,
                    enabled:false
                });
            },
            _setFormAttr: function(plugin){
                this.nameText.set("value",plugin.name);
                this.descriptionText.set("value",plugin.description);
                
                this.formRegionSelect.set("value",plugin.region);
                this.typeSelect.set("value",plugin.type);
                this.weightText.set("value",plugin.weight);
                this.pluginId = plugin.id;
            },
            _getFormAttr:function(){
                return {
                    'id':this.pluginId,
                    'name':this.nameText.get("value"),
                    'description':this.descriptionText.get("value"),
                    "type":this.typeSelect.get("value"),
                    'weight':parseInt(this.weightText.get("value")),
                    'region':this.formRegionSelect.get("value")
                };
            },
            _onSave:function(){
                var f = this.get('form');
                this.onSave(f);
            },
            _onExtendedEdit:function(){
                var f= this.get('form');
                if(!f.id){
                    return;
                }
                this.onExtendedEdit(f.id,f.type);
            },
            _onDelete: function(){
                this.onDelete(this.pluginId);
            },
            _onCreate : function(){
                var f = this.get('form');
                this.onNew(f);
            },
            _onReloadGrid:function(){
            },
            onNew: function(name,description,type,region,weight,enable){},
            onSave: function(pluginId, name,description,region,weight,enable){},
            onDelete: function(pluginId){},
            onExtendedEdit: function(pluginId,type){},
            onItemSelect: function(item){},
            onConfigurationMatrix:function(){}
        })
    })