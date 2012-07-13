define(
    ['dojo/_base/declare','dojo/_base/Deferred','azfcms/model/navigation',
    'dojo/_base/lang'],function
    (declare, Deferred, navigationModel,
        lang)
        {
        var _class = declare([],{
            // TODO DOH TEST
        
            constructor: function(args){
             /**
             * Reference of editor pane
             */
                this.view = null;
                
                /**
             * Reference of navigation node identifier
             */
                this.nodeId = null;
                
                if(args&&args.navigationModel){
                    this.navigationModel = args.navigationModel;
                } else {
                    this.navigationModel = navigationModel;
                }
            },
            
            initDependencies: function(nodeId, ep){
                var self = this;
                this.editorPane = ep;
                this.view = ep;
                this.nodeId = nodeId;
                
                var d = new Deferred();
                this.initialize(function(){
                    d.callback(self)
                }); 
                return d;
            },
            
            initialize: function(callback){
                callback();
            },
            
            
            /**
         * Submit key/value set to the content controller (put method)
         * 
         * @param {string} key
         * @param {mixed} value
         * @return {dojo.Deferred}
         */
            setValue:function(key,value){
                return this.navigationModel.setContent(this.nodeId,key,value);
            },
            
            
            /**
         * Submit hash map to the content controller (put method)
         * 
         * @param {Object} values
         * @return {dojo.Deferred}
         */
            setValues:function(values){
                return this.navigationModel.setContent(this.nodeId,null,values);
            },
            
            
            /**
         * This method will load the values from the content controller
         * and will pass the requested key value to the deferred listener
         * 
         * @param {string} key
         * @return {dojo.Deferred} value
         */
            getValue:function(key){
                return this.navigationModel.getContent(this.nodeId,key);
            },
        
            
            /**
             * This method will load values from the server and will pass the
             * result as a object to the deferred listener
             * 
             * @return {dojo.Deferred}
             */
            getValues:function(){
                return this.navigationModel.getContent(this.nodeId,null);
            }
            
        });
    
        return _class;
    })