define(
    ['dojo/_base/declare','azfcms/module/default/view/AbstractEditPane',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/Custom.html','dojo/dom-geometry',
    'dijit/Editor','dijit/form/Button','dijit/layout/BorderContainer',
    'dijit/layout/ContentPane','dijit/Toolbar','dijit/form/TextBox',
'azfcms/module/default/view/form/InputContainer'],function
    (declare, AbstractEditPane,
        _Widget, _TemplatedMixin, _WidgetsInTemplate,
        templateString,domGeometry)
        {
        var _class = declare([AbstractEditPane,_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString: templateString,
            init:function(){
                this.saveButton;
                this.inputModule;
                this.inputController;
                this.inputAction;
            },
            
            _getValueAttr:function(){
                return this.get('inputValues');
            },
            
            _setValueAttr:function(values){
                this.set('inputValues',values);
            },
            
            disable: function(){
                this.saveButton.set("disabled",true);
            },
            
            enable: function(){
                this.saveButton.set("disabled",false);
            },
            
            _onSave: function(){
                this.onSave(this.get("value"));
            },
            
            onSave: function(content){
                
            },
            
            resize:function(){
                this.borderContainer.resize();
            }
        });
    
        return _class;
    })