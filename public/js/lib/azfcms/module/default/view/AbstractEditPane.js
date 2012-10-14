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
             * Will return all attached points that start end with Input name.
             * 
             * Example:
             * [['firstNameInput','firstName'],['lastNameInput','lastName']]
             * [[propertyName,fieldName]]
             * @return {Array}
             */
            _getAttachedInputPropertyNames:function(){
                var attachPoints = this._attachPoints;
                
                var propertyNames = new Array();
                var propName,groups, fieldName;
                var validRegex = /(.+)Input$/i;
                for(var i=0,len=attachPoints.length;i<len;i++){
                    propName = attachPoints[i];
                    
                    groups = validRegex.exec(propName);
                    if(groups){
                        fieldName = groups[1];
                        propertyNames.push([propName,fieldName]);
                    }
                }
                
                return propertyNames;
            },
            
            
            /**
             * 
             */
            _setInputPropertyValues:function(attributeName, values){
                var inputPropertyNames = this._getAttachedInputPropertyNames();
                var propName, fieldName, propValue;
                
                for(var i = 0, len = inputPropertyNames.length;i<len;i++){
                    propName = inputPropertyNames[i][0];
                    fieldName = inputPropertyNames[i][1];
                    
                    if(typeof values[fieldName]!='undefined'){
                        this[propName].set(attributeName,values[fieldName]);
                    }
                }
            },
            
            /**
             * 
             */
            _getInputPropertyValues:function(attributeName){
                var inputPropertyNames = this._getAttachedInputPropertyNames();
                var propName, fieldName, propValue;
                var values = {};
                
                for(var i = 0, len = inputPropertyNames.length;i<len;i++){
                    propName = inputPropertyNames[i][0];
                    fieldName = inputPropertyNames[i][1];
                    
                    values[fieldName] = this[propName].get(attributeName);
                }
                
                return values;
            },
            
            
            /**
             * Set value to input elements specified by dict dictionary
             */
            _setInputValuesAttr:function(entity){
                this._setInputPropertyValues('value',entity);
            },
            
            /**
             * Get dict from all input elements
             */
            _getInputValuesAttr:function(){
                return this._getInputPropertyValues('value');
            },
            
            
            
            _setMessagesAttr:function(entity){
                this._setInputPropertyValues('messages',entity);
            },
            
            
            /**
             * Use this setter for disabling all attached widgets. 
             * 
             */
            _setDisabledAttr:function(state){
                var inputProps = this._getAttachedInputPropertyNames();
                var fieldName;
                
                for(var i = 0, len=inputProps.length;i<len;i++){
                    fieldName = inputProps[i][0];
                    this[fieldName].set("disabled",state);
                }
            }
        });
    
        return _class;
    })