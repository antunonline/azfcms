define(
    ['dojo/_base/declare','dijit/layout/ContentPane','dijit/Editor'],function
    (declare, ContentPane, Editor)
        {
        var _class = declare([ContentPane],{
        
            constructor: function(adminDialog, navigationPane){
                this.region = "center";
            },
            
            postCreate: function(){
                this.inherited(arguments);
                
                this.init();
            }
        });
    
        return _class;
    })