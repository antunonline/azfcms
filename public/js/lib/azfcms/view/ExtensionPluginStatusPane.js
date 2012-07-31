/* 
 *
 */


define(
    ['dojo/_base/declare','dijit/layout/ContentPane','dojox/grid/DataGrid',
    'dojo/data/ObjectStore'],function
    (declare,ContentPane,DataGrid,
     ObjectStore)
        {
        return declare([ContentPane],{
            postCreate:function(){
                this.inherited(arguments);
                this._createGrid();
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
                    structure:[
                        {name:"Page title",field:'pageTitle',width:'100px'},
                        {name:"Plugin name",field:'pluginName',width:'100px'},
                        {name:"Enabled",field:'enabled',width:'100px',editable:true,cellType:"dojox.grid.cells.Bool"},
                        {name:"Weight",field:'weight',width:'100px',editable:true},
                    ],
                    style:"width:100%;height:100%",
                    store:store
                });
                this.grid.placeAt(this.domNode);
                this.grid.startup();
            }
        })
    })