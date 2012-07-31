/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','azfcms/model/cms','dojo/_base/lang'],
    function (declare,cmsModel,lang){
        return declare([],{
            constructor:function(args){
                if(!args.cmsModel){
                    this.cmsModel = cmsModel;
                } else {
                    this.cmsModel  = args.cmsModel;
                }
                
                if(!args.pluginId){
                    throw "azfcms/controller/extensionPlugin/Abstract: pluginId is not specified"
                }
                
                if(!args.navigationId){
                    throw "azfcms/controller/extensionPlugin/Abstract: navigationId is not specified"
                }
                
                if(!args.view){
                    throw "azfcms/controller/extensionPlugin/Abstract: view is not specified"
                }
                
                lang.mixin(this,args);
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