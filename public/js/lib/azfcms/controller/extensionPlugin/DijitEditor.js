/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','azfcms/controller/extensionPlugin/Abstract'],
    function (declare,Abstract){
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
                
                this.getValue('body').then(function(body){
                    self.view.editor.set("value",body);
                });
                
                this.view.saveButton.on("click",function(){
                    self.setValue("body",self.view.editor.get("value"));
                })
            }
        
            
        })
    })