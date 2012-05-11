define(
    ['dojo/_base/declare','azfcms/view/AbstractEditPane','dijit/Editor'],function
    (declare, AbstractEditPane, Editor)
    {
        var _class = declare([AbstractEditPane],{
        
            init: function(){
                
                // Create editor
                this.editor = new Editor();
                this.set("content",this.editor);
            }
        });
    
        return _class;
    })