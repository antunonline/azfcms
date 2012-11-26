define([
    'dojo/_base/declare',
    'dojo/text!./templates/PluginsPane.html',
    'dijit/_WidgetBase',
    'dijit/_TemplatedMixin',
    'dijit/_WidgetsInTemplateMixin',
    
    'dijit/layout/BorderContainer',
    'dijit/Toolbar',
    'dijit/form/Button',
    'dijit/layout/ContentPane',
    'dojox/grid/DataGrid'
],
function(declare, templateString, _WidgetBaseMixin,_TemplatedMixin,_WidgetsInTemplateMixin
){
    return declare([_WidgetBaseMixin,_TemplatedMixin,_WidgetsInTemplateMixin],{
        templateString:templateString,
        constructor:function(args){
            this.store = args.store;
        },
        onSave:function(){
            
        },
        onRefresh:function(){
            
        }
    })
})

