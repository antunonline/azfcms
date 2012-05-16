define(
    ['dojo/_base/declare'],function
    (declare)
        {
        var _class = declare([],{
        
        
            constructor: function(adminDialog, navigationPane){
            
                /**
             * Store adminn dialog reference
             */
                this.adminDialog = adminDialog;
                /**
                 * Store navigation pane reference
                 */
                this.navigationPane = navigationPane;
            
                /**
             * This variable will hold currently selected navigation node
             */
                this.selectedNode = null;
                
                this._initNavigationListeners();
            },
            /**
             * Initialize navigation listeners
             */
            _initNavigationListeners: function(){
                var context = this;
                this.navigationPane.on("itemSelect",function(item){
                    context.selectNode(item)
                });
            },
            /**
             * Initialize navigation buttons
             */
            _initiNavigationButtons: function(){
                
                
            },
            
            
        
            selectNode: function(node){
                this.selectedNode = node;
            }
        });
    
        return _class;
    })