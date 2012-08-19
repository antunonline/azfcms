/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/EmptyInputContainer.html','dojo/_base/lang','dojo/dom-construct'],
    function(declare,_Widget,_TemplatedMixin,_WidgetsInTemplate,
        templateString,lang,domConstruct){
        return declare('azfcms.view.widget.form.EmptyInputContainer',[_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString:templateString,
            constructor:function(){
            }
        });
    }
    )

