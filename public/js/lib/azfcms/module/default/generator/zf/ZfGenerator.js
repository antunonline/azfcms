define(['dojo/_base/declare','azfcms/module/default/AbstractGenerator','dijit/_WidgetBase',
'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dojo/text!./templates/ZfGenerator',
'azfcms/module/default/generator/zf/FSSearchStore',
'dojo/data/ObjectStore',
'dojo/topic',



'dijit/layout/TabContainer',
'dijit/Tree',
'dojox/grid/DataGrid',
'dijit/layout/BorderContainer',
'dijit/layout/ContentPane',
'dijit/form/TextBox',
'dijit/form/Button'


],
function(declare,AbstractGenerator, _WidgetBase,
_TemplatedMixin, _WidgetsInTemplate,templateString,
FSStore,ObjectStore,topic){
    return declare([AbstractGenerator,_WidgetBase, _TemplatedMixin, _WidgetsInTemplate],{
        TAB_SEARCH_GRID:'searchGrid',
        TAB_TREE:"tree",
        templateString:templateString,
        title:"Dadoteke",
        constructor:function(){
            this.model = new FSStore();
            this.objectStore = new ObjectStore({objectStore:this.model});
        },
        postCreate:function(){
            this.inherited(arguments);
            this.tabContainer.startup();
            this._initTabListener();
            this.selectedTab = this.tree;
        },
        _initTabListener:function(){
            var FSGenerator = this;
            topic.subscribe(this.tabContainer.id+"-selectChild",function(selectedTab){
                FSGenerator.selectedTab = selectedTab;
            })
        },
        resize:function(){
            this.tabContainer.resize(arguments.length>0?arguments[0]:null);
        },
        _initView:function(){
            this.setView(this)
        },
        
        _initMetadata:function(){
            this.name = "DefaultZf"
            this.services = [
                'htmlLink','htmlImage','FSObject'
            ];
        },
        _getSelectionAttr:function(){
            if(!this.selectedTab){
                return;
            } else if(this.selectedTab.tid == this.TAB_TREE) {
                return this.tree.selectedItems.length>0?this.tree.selectedItems[0]:false;
            } else if(this.selectedTab.tid == this.TAB_SEARCH_GRID) {
                var selected = this.searchGrid.selection.getSelected();
                if(selected.length>0){
                    return selected[0];
                } else {
                    return null;
                }
            }
        },
        doSearch:function(){
            var searchValue = this.searchInput.get('value');
            this.searchGrid.setStore(this.objectStore,searchValue);
        },
        buildHtmlLink:function(promise){
            var selection = this.get('selection');
            if(!selection)
                return;
            
            var link = [];
            link.push("&nbsp;<a href='");
            link.push(selection.path);
            link.push("'>");
            link.push(selection.name);
            link.push("</a>&nbsp;");
            promise.resolve(link.join(''));
        },
        
        buildHtmlImage:function(promise){
            var selection = this.get('selection');
            if(!selection || selection.isDir)
                return;
            
            var link = [];
            link.push("&nbsp;<img src='");
            link.push(selection.path);
            link.push("' alt='");
            link.push(selection.name);
            link.push("'></img>&nbsp;");
            promise.resolve(link.join(''));
        },
        
        buildFSObject:function(promise){
            var selection = this.get('selection');
            if(!selection)
                return;
            promise.resolve(selection);
        }
    })
})

