define(
    ['dojo/_base/declare','azfcms/controller/AbstractEditorController','azfcms/model/navigation'],function
    (declare, AbstractEditorController,navigationModel)
    {
        var _class = declare([AbstractEditorController],{
            
            /**
             * Initialize controller
             */
            initialize: function(callback){
                if("model" in this == false){
                    this.model = navigationModel;
                }
                
                var self = this;
                
                this.editorPane.disable();
                
                // Load content
                this.model.getContent(this.nodeId).then(function(content){
                    self.editorPane.set("content",content['default']);
                    self.editorPane.enable();
                })
                
                // Register save listener
                this.editorPane.on("save",function(content){
                    self.setContent(content);
                });
                
                callback();
            },
            
            setContent: function(content){
                var self = this;
                this.editorPane.disable();
                self.model.setContent(self.nodeId,content).then(function(){
                    self.editorPane.enable();
                })
            }
        });
    
        return _class;
    })