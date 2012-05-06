define(
['dojo/_base/declare'],function
(declare)
{
    var _class = declare([],{
        
        
        constructor: function(adminDialog){
            
            /**
             * Store adminn dialog reference
             */
            this.adminDialog = adminDialog;
            
            /**
             * This variable will hold currently selected navigation node
             */
            this.selectedNode = null;
        },
        
        selectNode: function(node){
            this.selectedNode = node;
        },
        
        editNode: function(){
            
        }
    });
    
    return _class;
})