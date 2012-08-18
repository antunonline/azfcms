/* 
 *
 */


define(
    ['dojo/_base/declare','dijit/layout/ContentPane','dojox/grid/DataGrid',
    'dojo/data/ObjectStore','dijit/form/Button','dojo/dom-construct',
    'dijit/Toolbar', 'dojo/_base/lang','dijit/layout/BorderContainer',
    'dojo/i18n!azfcms/resources/i18n/cms/common/nls/common','dijit/form/TextBox','dojo/keys'],function
    (declare,ContentPane,DataGrid,
        ObjectStore, Button,domConstruct,
        Toolbar, lang, BorderContainer,
        nls,TextBox,keys)
        {
        return declare([ContentPane],{
            closable:true,
            title:nls.epspTitle,
            postCreate:function(){
                this.inherited(arguments);
                this._createBorderContainer();
                this._createTopContentPane();
                this._createToolbar();
                this._createSearchControls();
                this._createGrid();
                this._createButtons();
            },
            _createBorderContainer:function(){
                this.borderContainer = new BorderContainer();
                this.addChild(this.borderContainer);
            },
            _createTopContentPane:function(){
                this.topContentPane = new ContentPane({
                    region:"top",
                    style:"padding:0px;"
                });
                this.topContentPane.startup();
                this.borderContainer.addChild(this.topContentPane);
            },
            _createToolbar:function(){
                this.toolbar = new Toolbar({})
                this.topContentPane.addChild(this.toolbar);
                this.toolbar.startup();
            },
            _createSearchControls:function(){
                this.searchContentPane = new ContentPane({
                    style:"margin-top:5px;"
                });
                this.topContentPane.addChild(this.searchContentPane);
                
                domConstruct.place("<span>"+nls.epspSearchPageLabel+":</span>",this.searchContentPane.domNode);
                this.searchTextBox = new TextBox({});
                this.searchTextBox.reloadGrid = lang.hitch(this,"reloadGrid");
                this.searchTextBox.on("keyDown",function(e){
                    if(e.keyCode==keys.ENTER){
                        this.reloadGrid();
                    }
                });
                
                this.searchContentPane.addChild(this.searchTextBox);
                
                domConstruct.place("<span style='padding-left:20px;'>"+nls.epspSearchPluginLabel+":</span>",this.searchContentPane.domNode);
                this.searchPluginTextBox = new TextBox({});
                this.searchContentPane.addChild(this.searchPluginTextBox);
                this.searchPluginTextBox.reloadGrid = lang.hitch(this,"reloadGrid");
                this.searchPluginTextBox.on("keyDown",function(e){
                    if(e.keyCode==keys.ENTER){
                        this.reloadGrid();
                    }
                });
                
                this.searchButton = new Button({
                    label:nls.epspSearchLabel,
                    onClick:lang.hitch(this,'reloadGrid')
                });
                this.searchContentPane.addChild(this.searchButton);
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
                        width:'200px'
                    },
                    
                    {
                        name:nls.epspGridLabelRegion,
                        field:'pluginRegion',
                        width:'200px'
                    },

                    {
                        name:nls.epspGridLabelPluginName,
                        field:'pluginName',
                        width:'200px'
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
                this.grid.setQuery({
                    title:this.searchTextBox.get('value'),
                    pluginTitle:this.searchPluginTextBox.get('value')
                })
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