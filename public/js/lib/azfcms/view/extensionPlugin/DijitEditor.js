/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','dojo/text!./templates/DijitEditor.html','dijit/_Widget','dijit/_WidgetsInTemplateMixin','dijit/_TemplatedMixin',

'dijit/Editor','dijit/form/Button'],
function (declare,templateString,_Widget,_WidgetsInTemplate,_Templated){
    return declare([_Widget,_Templated,_WidgetsInTemplate],{
        templateString:templateString,
        closable:true,
        constructor:function(args){
            
        },
        
        postCreate:function(){
            this.inherited(arguments);
        }
        })
})