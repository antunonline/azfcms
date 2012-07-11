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
                
                this.view.on("save",lang.hitch(this,this.onSave));
                
                // This is where you will initialize the controller.
                // When the controller is initialized call the provided callback 
                // function to procede with construction of visual editing tool
                this.getValue('url').then(function(url){
                    self.view.set("url",url);
                });
            },
            
            onSave:function(url){
                this.setValue('url',url);
            }
        });
    
        return _class;
    })