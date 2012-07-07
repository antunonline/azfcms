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
                
                if(!this.navigationModel || !args.navigationModel){
                    this.navigationModel = navigationModel;
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
            storeValue:function(key,value){
                if(!this.jsonModelContentType){
                    throw new "json communication is prohibited by jsonModelContentType=false property, to enable json API set jsonModelContentType to true";
                }
                var objJson = lang.toJson({
                    key:value
                });
                return this.navigationModel.setContent(this.nodeId,objJson);
            },
            
            
            /**
         * Submit hash map to the content controller (put method)
         * 
         * @param {Object} values
         * @return {dojo.Deferred}
         */
            storeValues:function(values){
                if(!this.jsonModelContentType){
                    throw new "json communication is prohibited by jsonModelContentType=false property, to enable json API set jsonModelContentType to true";
                }
                var objJson = lang.toJson(values);
                return this.navigationModel.setContent(this.nodeId,objJson);
            },
            
            
            /**
         * This method will load the values from the content controller
         * and will pass the requested key value to the deferred listener
         * 
         * @param {string} key
         * @return {dojo.Deferred} value
         */
            loadValue:function(key){
                if(!this.jsonModelContentType){
                    throw new "json communication is prohibited by jsonModelContentType=false property, to enable json API set jsonModelContentType to true";
                }
                
                var d = new Deferred();
                this.navigationModel.getContent(this.nodeId).then(function(result){
                    var response = lang.fromJson(result);
                    d.callback(key in response?response[key]:null);
                });
                return d;
            },
        
            
            /**
             * This method will load values from the server and will pass the
             * result as a object to the deferred listener
             * 
             * @return {dojo.Deferred}
             */
            loadValues:function(){
                if(!this.jsonModelContentType){
                    throw new "json communication is prohibited by jsonModelContentType=false property, to enable json API set jsonModelContentType to true";
                }
                
                var d = new Deferred();
                this.navigationModel.getContent(this.nodeId).then(function(result){
                    var response = lang.fromJson(result);
                    d.callback(response);
                });
                return d;
            },
            
            /**
             * This method will load a value from content controller and pass it to the
             * deferred listener.
             * @return {dojo.Deferred}
             */
            getContent:function(){
                return this.navigationModel.getContent(this.nodeId);  
            },
                
            /**
                 * This method will submit provided values to the content controller put method.
                 * @param {string} content
                 * @return {dojo.Deferred}
                 */
            setContent:function(content){
                return this.navigationModel.setContent(this.nodeId,content);
            }
            
        });
    
        return _class;
    })