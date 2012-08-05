/* 
 *
 */


define(
    ['dojo/_base/declare','dijit/layout/ContentPane','dojox/grid/DataGrid',
    'dojo/data/ObjectStore','dijit/form/Button','dojo/dom-geometry',
    'dijit/Toolbar', 'dojo/_base/lang','dijit/layout/BorderContainer',
    'dojo/i18n!azfcms/resources/nls/view'],function
    (declare,ContentPane,DataGrid,
        ObjectStore, Button,domGeometry,
        Toolbar, lang, BorderContainer,
        nls)
        {
        return declare([ContentPane],{
            closable:true,
            title:nls.epspTitle,
            postCreate:function(){
                this.inherited(arguments);
                this._createBorderContainer();
                this._createToolbar();
                this._createGrid();
                this._createButtons();
            },
            _createBorderContainer:function(){
                this.borderContainer = new BorderContainer();
                this.addChild(this.borderContainer);
            },
            _createToolbar:function(){
                this.toolbar = new Toolbar({region:"top"})
                this.borderContainer.addChild(this.toolbar);
                this.toolbar.startup();
            },
            _createGrid:function(){
                var store;
                if(this.gridStore.getFeatures){
                    store = this.gridStore;
                } else {
                    store = new ObjectStore({
                        objectStore:this.gridStore
                    });
                }
                this.grid = new DataGrid({
                    singleClickEdit:true,
                    keepSelection:false,
                    selectionModel:"single",
                    autoHeight:false,
                    region:"center",
                    defaultHeight:"200px;",
                    structure:[
                    {
                        name:nls.epspGridLabelName,
                        field:'pageTitle',
                        width:'100px'
                    },
                    
                    {
                        name:nls.epspGridLabelRegion,
                        field:'pluginRegion',
                        width:'100px'
                    },

                    {
                        name:nls.epspGridLabelPluginName,
                        field:'pluginName',
                        width:'100px'
                    },

                    {
                        name:nls.epspGridLabelEnabled,
                        field:'enabled',
                        width:'100px',
                        editable:true,
                        cellType:"dojox.grid.cells.Bool"
                    },

                    {
                        name:nls.epspGridLabelWeight,
                        field:'pluginWeight',
                        width:'100px',
                        editable:true
                    },

                    {
                        name:nls.epspGridLabelCustomWeight,
                        field:'weight',
                        width:'100px;',
                        editable:true
                    }
                    ],
                    style:"width:100%;height:200px",
                    store:store
                });
                this.borderContainer.addChild(this.grid);
                this.grid.startup();
            },
            _createButtons:function(){
                this.toolbar.addChild(new Button({
                    label:nls.epspSaveAction,
                    onClick:lang.hitch(this,"doSave")
                }));
                this.toolbar.addChild(new Button({
                    label:nls.epspEnablePluginGlobally,
                    onClick:lang.hitch(this,"doEnablePluginGlobaly")
                }));
                this.toolbar.addChild(new Button({
                    label:nls.epspDisablePluginGlobally,
                    onClick:lang.hitch(this,"doDisablePluginGlobaly")
                }));
            },
            
            getSelectedItem:function(){
                var items = this.grid.selection.getSelected();
                if(items.length>0){
                    return items[0];
                } else {
                    return null;
                }
            },
            
            reloadGrid:function(){
                this.grid.setQuery({})
            },
            
            
            doSave:function(){
                var self = this;
                self.toolbar.set("disabled",true);
                this.grid.store.save({
                    onComplete:function(){
                        self.toolbar.set("disabled",false);
                        self.reloadGrid();
                    }
                });
            },
            
            doEnablePluginGlobaly:function(){
                var self = this;
                var item = this.getSelectedItem();
                if(!item){
                    return;
                }
                
                
                this.gridStore.enablePluginGlobally(item).then(function(){
                    self.reloadGrid();
                })
            },
            
            doDisablePluginGlobaly:function(){
                var self = this;
                var item = this.getSelectedItem();
                if(!item){
                    return;
                }
                
                
                this.gridStore.disablePluginGlobally(item).then(function(){
                    self.reloadGrid();
                })
            }
        })
    })