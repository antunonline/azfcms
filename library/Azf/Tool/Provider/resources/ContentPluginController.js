define(
    ['dojo/_base/declare','azfcms/module/default/controller/AbstractContentController','dojo/_base/lang'],function
    (declare, AbstractEditorController,lang)
    {
        var _class = declare([AbstractEditorController],{
            
            /**
             * Initialize controller
             */
            initialize: function(callback){
                
                
                callback();
            }
            
        });
    
        return _class;
    })