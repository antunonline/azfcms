require(['dojo/_base/declare','dijit/layout/_Widget','dojo/text!./templates/NewsCategoryGrid',
    'dijit/_WidgetsInTemplateMixin','dijit/_Widget',

    'dijit/layout/BorderContainer','dijit/layout/ContentPane','dijit/Toolbar',
    'dijit/form/Button','dojox/grid/DataGrid'],
function(declare,BorderContainer,templateString,
    _WidgetsInTemplateMixin,_Widget){
    return declare([_Widget,_WidgetsInTemplateMixin],{
        templateString:templateString,
        constructor:function(){
            
        }
    })
});

