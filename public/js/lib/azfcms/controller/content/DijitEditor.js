define(
    ['dojo/_base/declare','dojo/_base/Deferred'],function
    (declare, Deferred)
        {
        var _class = declare([],{
        
        
            constructor: function(){
                /**
                 * Reference of editor pane
                 */
                this.editorPane = null;
                
                /**
                 * Reference of navigation node identifier
                 */
                this.nodeId = null;
            },
            
            initDependencies: function(nodeId, ep){
                this.editorPane = ep;
                this.nodeId = nodeId;
                
                var d = new Deferred();
                this.initialize(d); 
                return d;
            },
            
            initialize: function(callback){
                callback.callback(this);
            }
            
        });
    
        return _class;
    })