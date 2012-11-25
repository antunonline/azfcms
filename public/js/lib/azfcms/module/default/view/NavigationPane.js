define(
['dojo/_base/declare','dijit/layout/ContentPane','dijit/Tree',
'dijit/layout/BorderContainer','dijit/Menu','dijit/MenuItem',
'dijit/tree/dndSource','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common',
'dojo/aspect'],
function
(declare,ContentPane,Tree,
  BorderContainer,  Menu, MenuItem,
  dndSource,nls, aspect)
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
            this.actionPane = new ContentPane({region:"left",style:"width:200px;padding:5px 0px;margin:0px;border:none"});
            
            
            /**
             * Action menu reference
             */
            this.menu = new Menu({
                style:"width:100%"
            });
            
            
        },
        postCreate: function(){
            // Call superclass postCreate
            this.inherited(arguments);
            
            // Create tree
            this.tree = new Tree({
                model:this.model,
                region:"center",
                dndController: dndSource,
                persist: false,
                autoExpand: false,
                betweenThreshold: 5,
                getIconClass:function(item){
                    return " dijitLeaf";
                }
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
            var tree = this.tree;
            menuItem.on("click",function(){
                onClick(tree.get("selectedItem"));
            });
            this.menu.addChild(menuItem)
        },
        
        /**
         * This method will be called when the item is selected on tree widget
         */
        onItemSelect: function(item){
            
        },
        
        /**
         * Prevent resizes if tree is not yet loaded
         */
        resize:function(){
//            if(this.tree.rootNode){
                this.inherited(arguments);
//            }
        }
    });
})
