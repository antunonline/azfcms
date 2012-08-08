define(
    ['dojo/_base/declare','dojo/i18n!azfcms/resources/nls/view'],function
    (declare, nls)
        {
        var _class = declare([],{
        
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