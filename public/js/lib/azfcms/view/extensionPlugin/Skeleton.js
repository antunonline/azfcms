/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','dojo/text!./templates/{ucName}.html','dijit/_Widget','dijit/_WidgetsInTemplateMixin','dijit/_TemplatedMixin'],
function (declare,templateString,_Widget,_WidgetsInTemplate,_Templated){
    return declare([_Widget,_Templated,_WidgetsInTemplate],{
        templateString:templateString,
        constructor:function(args){
            
        },
        
        postCreate:function(){
            this.inherited(arguments);
        }
        })
})