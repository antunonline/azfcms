/* 
 * @email antunhorvat@gmail.com
 * @author Antun Horvat
 * 
 */

define(['dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dijit/_Widget',
    'dojo/_base/lang','dojo/_base/declare','dojo/text!./templates/FilesystemPane.html',
    'dojo/data/ObjectStore','dojo/query','dojox/grid/DataGrid','dojo/dom-style',
    'dijit/form/Button','dojo/aspect','dojo/i18n!azfcms/resources/nls/view',

    'dojo/NodeList-manipulate',
    'dijit/layout/ContentPane','dijit/layout/BorderContainer','dijit/Tree',
    'dijit/Toolbar'],function
    (_TemplatedMixin,_WidgetsInTemplateMixin,_Widget,
        lang,declare,templateString,
        ObjectStore,query,Grid,domStyle,
        Button,aspect,nls,

        CP,BC,Tree)
        {
        return declare([_Widget,_TemplatedMixin,_WidgetsInTemplateMixin],{
            isFirstConstruct:false,
            templateString:templateString,
            /**
         * 
         *  Reference of dijit.tree.Model 
         * treeStore:null,
         * 
         * // Reference of dojo.data.api.Read or
         * // dojo.Store object
         * gridStore
         */
            constructor:function(args){
                if(!this.isFirstConstruct){
                    var url = require.toUrl("")+"/dojox/grid/resources/claroGrid.css";
                    query("head").append("<link rel='stylesheet' type='text/css' href='"+url+"' />");
                    this.isFirstConstruct = true;
                }
                
                for(var name in nls){
                    if(name.indexOf('fsp')==0){
                        this[name] = nls[name];
                    }
                }
            },
            destroy:function(){
                this.grid.destroy();
                this.gridContentPane.destroy();
                this.tree.destroy();
                this.toolbar.destroy();
                this.borderContainer.destroy();
                this.domNode.parentNode.removeChild(this.domNode);
                if("destroy" in this.gridStore){
                    this.gridStore.destroy();
                }
                if("destroy" in this.treeStore){
                    this.treeStore.destroy();
                }
                this.inherited(arguments);
            },
            postCreate:function(){
                var self = this;
                this.inherited(arguments);
                // Add tree
                var tree = this.tree = new dijit.Tree({
                    model:this.treeStore,
                    region:"left",
                    style:"width:300px;",
                    persist:false
                });
                tree.on("click",function(item){
                    self.treeSelect=item;
                    self.onTreeSelect(item);
                });
                this.borderContainer.addChild(tree);
            
                // Set grid store
                // If gridStore does not inherit dojo.data.api.Read interface
                var gridStore;
                if("getFeatures" in this.gridStore ==false){
                    // Alter grid store so that it will work with ObjectStore 
                    // strange transformation of query object
                    aspect.before(this.gridStore,"query",function(query){
                        var modifiedQuery={};
                        for(var name in query){
                            modifiedQuery[name] = query[name].toString();
                        }
                        return [modifiedQuery];
                    });
                    gridStore = new ObjectStore({
                        objectStore:this.gridStore
                    });
                } else {
                    gridStore= this.gridStore;
                }
                
                // Convert timestamps to dates
                aspect.after(this.gridStore,"query",function(promise){
                    promise.then(function(items){
                         var d = new Date();
                        for(var i = 0; i < items.length;i++){
                           d = new Date(items[i].date*1000);
                            items[i].date = d.getUTCDay()+"."+d.getUTCMonth()+"."+d.getUTCFullYear()
                        }
                    })
                    return promise;
                })
                
                this.grid.setStore(gridStore,{});
                
            },
            enable:function(){
                this.toolbar.set("disabled",false);
                if(this.disableLayer){
                    this.disableLayer.parentNode.removeChild(this.disableLayer);
                    this.disableLayer = null;
                }
            },
            disable:function(){
                this.toolbar.set("disabled",false);
                if(this.disableLayer){
                    return;
                } else {
                    this.disableLayer = document.createElement("div");
                    this.disableLayer.style.left="0px";
                    this.disableLayer.style.right="0px";
                    this.disableLayer.style.bottom="0px";
                    this.disableLayer.style.top="0px";
                    this.disableLayer.style.position="absolute";
                    this.disableLayer.style.backgroundColor="#333";
                    domStyle.set(this.disableLayer,"opacity",0.5);
                    this.domNode.appendChild(this.disableLayer);
                    
                }
            },
            resize:function(){
                this.borderContainer.resize();
                
                this.grid.set("style",this._getComputedGridStyle());
                this.reload();
            },
            _getComputedGridStyle:function(){
                var compS = domStyle.getComputedStyle(this.gridContentPane);
                var h = parseInt(compS.height);
                return "width:100%;height:"+h+"px";
            },
            
            addAction:function(name,callback, dijitIconClass){
                var button = new Button({
                    label:name,
                    onClick:callback,
                    icon:dijitIconClass
                });
                
                this.toolbar.addChild(button);
            },
            reloadGrid:function(query){
                this.grid.setQuery(query);
            },
            reloadTree:function(){
                this.tree.set("model",this.treeStore);
            },
            reload:function(){
                var query ;
                if(arguments.length == 1){
                    query = arguments[0];
                } else {
                    query = this.grid.query;
                }
                
                this.reloadGrid(query);
                this.reloadTree();
            },
            getGridSelection:function(){
                return this.grid.selection.getSelected();
            },
            getTreeSelection:function(){                
                if("treeSelect"in this){
                    return this.treeSelect;
                } else {
                    return null;
                }
            },
            onTreeSelect:function(item){}
        });
    
    
    })


