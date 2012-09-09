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
            },
            
            /**
             * Set value to input elements specified by dict dictionary
             */
            _setInputValuesAttr:function(dict){
                
                var propName = "";
                for(var name in dict){
                    propName = name+"Input";
                    if(typeof this[propName] != 'undefined'){
                        this[propName].set('value',dict[name]);
                    }
                }
            },
            
            
            /**
             * Get dict from all input elements
             */
            _getInputValuesAttr:function(){
                var attachPoints = this._attachPoints;
                
                var propName,values = {};
                var validRegex = /(.+)Input$/i;
                for(var i=0,len=attachPoints.length;i<len;i++){
                    propName = attachPoints[i];
                    
                    if(validRegex.test(propName)&& typeof this[propName].get != 'undefined'){
                        values[validRegex.exec(propName)[1]] = this[propName].get('value');
                    }
                }
                
                return values;
            },
            
            
            /**
             * Use this setter for disabling all attached widgets. 
             * 
             */
            _setDisableAttachedAttr:function(value){
                var attachPoints = this._attachPoints;
                
                var prop="";
                for(var i = 0, len = attachPoints.length;i<len;i++){
                    prop = attachPoints[i];
                    
                    if(typeof this[prop].set!='undefined'){
                        this[prop].set('disabled',value);
                    }
                }
            }
        });
    
        return _class;
    })