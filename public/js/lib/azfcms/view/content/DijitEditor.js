define(
    ['dojo/_base/declare','azfcms/view/AbstractEditPane',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/DijitEditor.html',
    'dijit/Editor','dijit/form/Button'],function
    (declare, AbstractEditPane,
        _Widget, _TemplatedMixin, _WidgetsInTemplate,
        templateString)
        {
        var _class = declare([AbstractEditPane,_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString: templateString,
            init:function(){},
            /**
             * @param {String} value
             */
            _setContentAttr: function(value){
                this.editor.set("value",value);
            },
            
            /**
             * @return {String}
             */
            _getContentAttr: function(){
                return this.editor.get("value");
            },
            
            disable: function(){
                this.editor.set("disabled",true);
                this.saveButton.set("disabled",true);
            },
            
            enable: function(){
                this.editor.set("disabled",false);
                this.saveButton.set("disabled",false);
            },
            
            _onSave: function(){
                this.onSave(this.get("content"));
            },
            
            onSave: function(content){
                
            }
        });
    
        return _class;
    })