/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','azfcms/model/cms'],
    function (declare,cmsModel){
        return declare([],{
            constructor:function(args){
                if(!this.cmsModel){
                    this.cmsModel = cmsModel;
                } else {
                    this.cmsModel  = args.cmsModel;
                }
                
                if(!args.pluginId){
                    throw "azfcms/controller/extensionPlugin/Abstract: pluginId is not specified"
                }
                this.pluginId = args.pluginId;
                
                if(!args.navigationId){
                    throw "azfcms/controller/extensionPlugin/Abstract: navigationId is not specified"
                }
                this.navigationId = args.navigationId;
                
                if(!args.view){
                    throw "azfcms/controller/extensionPlugin/Abstract: view is not specified"
                }
                this.view = args.view;
            },
            
            init:function(){},
            
        
            /**
            * @param {String|Number} key
            * @param {String|Number} value
            * @return {dojo.Deferred} (boolean)
            * 
            */
            setValue:function(key,value){
                return this.cmsModel.setExtensionValue(this.pluginId,key,value);
            },
            
            /**
            * @param {Object} values
            * @return {dojo.Deferred} (boolean)
            * 
            */
            setValues:function(values){
                return this.cmsModel.setExtensionValues(this.pluginId,values);
            },
            
            
            /**
            * @param {String|Number} key
            * @return {dojo.Deferred} (mixed)
            * 
            */
            getValue:function(key){
                return this.cmsModel.getExtensionValue(this.pluginId,key);
            },
            
            /**
            * @return {dojo.Deferred} (Object)
            * 
            */
            getValues:function(){
                return this.cmsModel.getExtensionValues(this.pluginId);
            }
        })
    })