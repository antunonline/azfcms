define(
    ['dojo/_base/declare','azfcms/module/default/view/AbstractEditPane',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/<?=$ucName?>.html'

],function
    (declare, AbstractEditPane,
        _Widget, _TemplatedMixin, _WidgetsInTemplate,
        templateString)
        {
        var _class = declare([AbstractEditPane,_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString: templateString,
            init:function(){
            },
            
            _getValueAttr:function(){
            },
            
            _setValueAttr:function(values){
            },
            
            disable: function(){
            },
            
            enable: function(){
            },
            
            resize:function(){
                
            }
        });
    
        return _class;
    })