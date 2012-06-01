/* 
 *
 */


define(
['dojo/_base/declare','dojo/text!./templates/ExtendedEditorPane.html','dijit/_Widget',
'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',

'dijit/layout/TabContainer','dijit/layout/ContentPane','dojox/grid/DataGrid',
'dijit/form/FilteringSelect'],function
(declare,templateString,_Widget,
_TemplatedMixin, _WidgetsInTemplateMixin)
{
    return declare([_Widget,_TemplatedMixin, _WidgetsInTemplateMixin],{
        templateString: templateString
    })
})