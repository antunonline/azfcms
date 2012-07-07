define(
    ['dojo/_base/declare','dijit/layout/ContentPane','dijit/Editor',
    'dojo/i18n!azfcms/resources/nls/view'],function
    (declare, ContentPane, Editor,
     nls)
        {
        var _class = declare([ContentPane],{
        
            constructor: function(){
                this.region = "center";
                this.title = nls.aepEditor;
            },
            
            postCreate: function(){
                this.inherited(arguments);
                
                if(this.init)
                    this.init();
            }
        });
    
        return _class;
    })