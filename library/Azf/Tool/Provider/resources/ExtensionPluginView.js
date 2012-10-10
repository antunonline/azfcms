/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','dojo/text!./templates/<?= $ucName ?>.html','dijit/_Widget','dijit/_WidgetsInTemplateMixin','dijit/_TemplatedMixin',
    'dojo/_base/lang',
    
    
    'dijit/layout/BorderContainer','dijit/layout/ContentPane','dijit/Toolbar','dijit/form/Button',
    'dijit/form/TextBox'],
    function (declare,templateString,_Widget,_WidgetsInTemplate,_Templated,
        lang){
        return declare([_Widget,_Templated,_WidgetsInTemplate],{
            templateString:templateString,
            constructor:function(args){
                /**
                 * Textarea widget
                 */
                this.textarea;
                
                /**
                 * toolbar widget
                 */
                this.toolbar;
                
                /**
                 * Title textbox
                 */
                this.linkGroupName;
                
            },
        
            postCreate:function(){
                this.inherited(arguments);
            },
        
            _save:function(){
                this.onSave(this.get('value'));
            },
            
            _setValueAttr:function(object){
                
            },
            
            _getValueAttr:function(){
                
            }
        })
    })