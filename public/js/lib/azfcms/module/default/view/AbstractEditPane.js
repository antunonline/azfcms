define(
    ['dojo/_base/declare','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common'],function
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