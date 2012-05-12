define(
['dojo/_base/declare','dijit/layout/ContentPane','dijit/Tree',
'dijit/layout/BorderContainer','dijit/Menu','dijit/MenuItem',
'dijit/tree/dndSource','dojo/i18n!azfcms/resources/nls/view'],
function
(declare,ContentPane,Tree,
  BorderContainer,  Menu, MenuItem,
  dndSource,nls)
{
    return declare([BorderContainer],{
        constructor: function(args){
            this.inherited(arguments);
            /**
             * Store navigation model reference
             * @property {azfcms/model/navigation}
             */
            this.model = args.model;
            
            // Set pane title
            this.title = nls.npTitle;
            
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
            
            var pane = this;
            this.tree.on("click",function(item){
                pane.onItemSelect(item)
            })
            
            // Add tree into content pane
            this.addChild(this.tree);
            // Add right pane
            this.addChild(this.actionPane);
            
            
            this.actionPane.set("content",this.menu);
            
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
        },
        
        /**
         * This method will be called when the item is selected on tree widget
         */
        onItemSelect: function(item){
            
        }
    });
})
