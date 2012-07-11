define(
    ['dojo/_base/declare','azfcms/controller/AbstractEditorController','azfcms/model/navigation'],function
    (declare, AbstractEditorController,navigationModel)
    {
        var _class = declare([AbstractEditorController],{
            /**
                 * Reference of editor pane
                 */
            editorPane:null,
                
            /**
                 * Reference of navigation node identifier
                 */
            nodeId:null,
            
            /**
             * @property {azfcms.model.navigation} navigationModel
             */
            navigationModel:null,
            
            
            /**
             * Initialize controller
             */
            initialize: function(callback){
                var self = this;
                // Define model content type
                this.jsonModelContentType=false // false=string , true=json// Read description below
                
                // This is where you will initialize the controller.
                // When the controller is initialized call the provided callback 
                // function to procede with construction of visual editing tool
                callback();
            }
            
        /**
         * Submit key/value set to the content controller (put method)
         * 
         * @param {string} key
         * @param {mixed} value
         * @return {dojo.Deferred}
         */
//            setValue:function(key,value){
//                return this.navigationModel.setContent(this.nodeId,key,value);
//            },
            
            
            /**
         * Submit hash map to the content controller (put method)
         * 
         * @param {Object} values
         * @return {dojo.Deferred}
         */
//            setValues:function(values){
//                return this.navigationModel.setContent(this.nodeId,null,values);
//            },
            
            
            /**
         * This method will load the values from the content controller
         * and will pass the requested key value to the deferred listener
         * 
         * @param {string} key
         * @return {dojo.Deferred} value
         */
//            getValue:function(key){
//                this.navigationModel.getContent(this.nodeId,key);
//            },
        
            
            /**
             * This method will load values from the server and will pass the
             * result as a object to the deferred listener
             * 
             * @return {dojo.Deferred}
             */
//            getValues:function(){
//                this.navigationModel.getContent(this.nodeId,null);
//            }
            
            
            
        // Place methods in here
        });
    
        return _class;
    })