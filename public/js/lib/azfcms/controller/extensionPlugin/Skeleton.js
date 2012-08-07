/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','azfcms/controller/extensionPlugin/Abstract','dojo/_base/lang'],
    function (declare,Abstract,lang){
        return declare([Abstract],{
            cmsModel:null,
            pluginId:null,
            navigationId:null,
            view:null,
            
            
            /**
             * @function
             */
/**
            * @param {String|Number} key
            * @param {String|Number} value
            * @return {dojo.Deferred} (boolean)
            * 
            */
//            setValue:function(key,value){
//            },
            
            /**
            * @param {Object} values
            * @return {dojo.Deferred} (boolean)
            * 
            */
//            setValues:function(values){
//            },
            
            
            /**
            * @param {String|Number} key
            * @return {dojo.Deferred} (mixed)
            * 
            */
//            getValue:function(key){
//            },
            
            /**
            * @return {dojo.Deferred} (Object)
            * 
            */
//            getValues:function(){
//            }
            constructor:function(){
                this.init();
            },
            init:function(){
                var self = this;
                var view = this.view;
                
                
            }
        
            
        })
    })