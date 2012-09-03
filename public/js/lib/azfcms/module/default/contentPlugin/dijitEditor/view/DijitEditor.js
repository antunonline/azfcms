define(
    ['dojo/_base/declare','azfcms/module/default/view/AbstractEditPane',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/DijitEditor.html','dojo/dom-geometry',
    'dijit/Editor','dijit/form/Button','dijit/layout/BorderContainer',
    'dijit/layout/ContentPane','dijit/Toolbar','dijit/_editor/plugins/LinkDialog','dijit/_editor/plugins/FontChoice',
                   'dijit/_editor/plugins/TextColor','dijit/_editor/plugins/ViewSource'],function
    (declare, AbstractEditPane,
        _Widget, _TemplatedMixin, _WidgetsInTemplate,
        templateString,domGeometry)
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
                
            },
            
            resize:function(){
                this.borderContainer.resize();
            }
        });
    
        return _class;
    })