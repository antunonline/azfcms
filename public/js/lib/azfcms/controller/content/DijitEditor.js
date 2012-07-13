define(
    ['dojo/_base/declare','azfcms/controller/AbstractEditorController'],function
    (declare, AbstractEditorController)
    {
        var _class = declare([AbstractEditorController],{
            
            /**
             * Initialize controller
             */
            initialize: function(callback){
                var self = this;
                
                this.editorPane.disable();
                
                // Load content
                this.getValue('content').then(function(content){
                    self.editorPane.set("content",content);
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
                this.setValue('content',content).then(function(){
                    self.editorPane.enable();
                })
            }
        });
    
        return _class;
    })