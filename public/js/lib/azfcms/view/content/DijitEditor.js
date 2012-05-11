define(
    ['dojo/_base/declare','dijit/layout/ContentPane','dijit/Editor'],function
    (declare, ContentPane, Editor)
        {
        var _class = declare([ContentPane],{
        
        
            constructor: function(adminDialog, navigationPane){
                /**
                 * Editor reference
                 */
                this.editor = null;
                this.region = "center";
             
            },
            postCreate: function(){
                this.inherited(arguments);
                
                // Create editor
                this.editor = new Editor();
                this.set("content",this.editor);
                
            }
        });
    
        return _class;
    })