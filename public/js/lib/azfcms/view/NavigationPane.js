define(
['dojo/_base/declare','dijit/layout/ContentPane','dijit/Tree',
'dijit/layout/BorderContainer','dijit/Menu','dijit/MenuItem',
'dijit/tree/dndSource'],
function
(declare,ContentPane,Tree,
  BorderContainer,  Menu, MenuItem,
  dndSource)
{
    return declare([BorderContainer],{
        constructor: function(args){
            this.inherited(arguments);
            /**
             * Store navigation model reference
             * @property {azfcms/model/navigation}
             */
            this.model = args.model;
            
            /**
             * Store context controller reference
             */
            this.contextController = args.controller;
            
            /**
             * Tree reference
             * #@property {dijit/Tree}
             */
            this.tree = null;
            
            /**
             * Right pane reference
             */
            this.actionPane = new ContentPane({region:"right",style:"width:200px;"});
            
            
            /**
             * Action menu reference
             */
            this.menu = new Menu({
                style:"width:100%;"
            });
            
            
        },
        postCreate: function(){
            // Call superclass postCreate
            this.inherited(arguments);
            
            // Create tree
            this.tree = new Tree({
                model:this.model,
                region:"center",
                dndController: dndSource
            });
            
            var controller = this.contextController;
            // Register click listener
            this.tree.on("click",function(item){
                controller.selectNode(item);
            })
            
            // Add tree into content pane
            this.addChild(this.tree);
            // Add right pane
            this.addChild(this.actionPane);
            
            
            this.actionPane.set("content",this.menu);
            this.addButton("Editiraj",new Function(), "dijitIconEdit");
            this.addButton("Izbrisi",new Function(), "dijitIconDelete");
            this.addButton("SEO Optimizacija",new Function(), "dijitIconSearch");
        },
        
        /**
         * Add button 
         */
        addButton: function(label, onClick, iconClass){
            var menuItem = new MenuItem({
                label:label,
                iconClass:iconClass
            });
            menuItem.on("click",onClick);
            this.menu.addChild(menuItem)
        }
    });
})
