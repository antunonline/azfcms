define(
['dojo/_base/declare','azfcms/view/AbstractEditPane',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/{ucIdentifier}.html'],function
(declare, AbstractEditPane,
_Widget, _TemplatedMixin, _WidgetsInTemplate,
templateString)
{
    var _class = declare([AbstractEditPane,_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        templateString: templateString,
        init:function(){
            // In this method you can initialize the view
        }
            
    });
    
    return _class;
})