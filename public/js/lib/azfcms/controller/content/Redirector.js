define(
    ['dojo/_base/declare','azfcms/controller/AbstractEditorController',
    'dojo/_base/lang'],function
    (declare, AbstractEditorController,
    lang)
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
                
                this.view.on("save",lang.hitch(this,this.onSave));
                
                // This is where you will initialize the controller.
                // When the controller is initialized call the provided callback 
                // function to procede with construction of visual editing tool
                this.getContent().then(function(url){
                    self.view.set("url",url);
                });
            },
            
            onSave:function(url){
                this.setContent(url);
            }
            
        /**
             * Access the model
             * 
             * By default all communication operations are going through navigationModel's 
             * setContent(nodeId,content) and getContent(nodeId) methods.
             * Returned values are by default plain strings which can be converted to objects
             * if server has provided a valid JSON response.
             * 
             * To speedup communication with the server side, we will provide few predefined methods that 
             * can be used to push and pull values from the server's content controller.
             * 
             * These methods are already defined in AbstractContentController class.
             */
            
            
            
        // //  //  //  //  //  //  //  WARNING FOLLOWING METHODS ARE VALID ONLY IF jsonModelContentType property is set to TRUE
        // //  //  //  //  //  //  //  This will force the controller to encode and decode values into JSON strings
        /**
             * Store a value. This call will submit the value to the server
             * 
             * @param {string} key
             * @param {mixed} value
             * @return {dojo.Deferred}
             */
        //            storeValue(key,value);
//        
//            
//            /**
//             * Submit hash map to the content controller (put method)
//             * 
//             * @param {Object} values
//             * @return {dojo.Deferred}
//             */
//            storeValues:function(values){}
        /**
             * This method will load the values from the content controller
             * and will pass the requested key value to the deferred listener
             * 
             * @param {string} key
             * @return {dojo.Deferred} value
             */
        //            loadValue(key);
        /**
             * This method will load values from the server and will pass the
             * result as a object to the deferred listener
             * 
             * @return {dojo.Deferred}
             */
        //            loadValues()


        // //  //  //  //  //  //  //  WARNING FOLLOWING METHODS ARE VALID ONLY IF jsonModelContentType property is set to FALSE
        /**
             * This method will load a value from content controller and pass it to the
             * deferred listener.
             * @return {dojo.Deferred}
             */
        //            getContent()
                
        /**
                 * This method will submit provided values to the content controller put method.
                 * @param {string} content
                 * @return {dojo.Deferred}
                 */
        //           setContent(content);
            
            
            
        // Place methods in here
        });
    
        return _class;
    })