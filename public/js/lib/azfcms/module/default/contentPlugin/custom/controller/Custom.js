define(
    ['dojo/_base/declare','azfcms/module/default/controller/AbstractContentController','dojo/_base/lang'],function
    (declare, AbstractEditorController,lang)
    {
        var _class = declare([AbstractEditorController],{
            
            /**
             * Initialize controller
             */
            initialize: function(callback){
                
                this.view.set("disableAttached",true);
                
                this.view.on("save",lang.hitch(this,'doSave'));
                
                this.getValues().then(lang.hitch(this,function(value){
                    this.view.set('value',value);
                    this.view.set("disableAttached",false);
                }))
                callback();
            },
            
            doSave:function(value){
                this.view.set("disableAttached",true);
                this.setValues(value).then(lang.hitch(this,function(){
                    this.view.set("disableAttached",false);
                }));
            }
        });
    
        return _class;
    })