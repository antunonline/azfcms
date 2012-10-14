define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/<?=$ucResource?>Form.html','dojo/i18n!../resource/i18n/nls/<?=$ucName?>',
    'dojo/_base/lang','azfcms/module/default/view/AbstractEditPane',
    'dijit/form/Button',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane','dijit/form/TextBox','dijit/form/Textarea',
    'azfcms/module/default/view/form/InputContainer','azfcms/module/default/view/form/WideInputContainer'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang,AbstractEditPane,
    Button)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate, AbstractEditPane],{
        LAYOUT_EDIT:"edit",
        LAYOUT_NEW:"new",
        constructor:function(){
            /**
             * Toolbar reference
             */
            this.toolbar;
            
            this.title = "Change Title"; // nls.tabTitle
            
            // Form layout
            if(!this.layout){
                // defaults to edit
                this.layout = this.LAYOUT_EDIT;
            }
            
            this.init();
        },
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        templateString: templateString,
        closable:true,
        title:nls.tabName,
        nls:nls,
        init:function(){
            
        },
        
        postCreate:function(){
            this._postCreateLayout();
        },
        
        _postCreateLayout:function(){
            var label;
            switch(this.layout){
                case this.LAYOUT_EDIT:
                    label = "Izmjeni";
                    break;
                case this.LAYOUT_NEW:
                    label = "Dodaj";
                    break;
            }
            this.toolbar.addChild(new Button({
                label:label,
                onClick:lang.hitch(this,'_onSave')
            }));
        },
        
        resize:function(){
            this.inherited(arguments);
            this.borderContainer.resize();
        },
        
        _getValueAttr:function(){
            var values = this.get("inputValues");
            values.id = this.idValue;
            
            return values;
        },
        
        _setValueAttr:function(values){
            this.idValue = values.id;
            this.set("title",values.title);
            this.set("inputValues",values);
        },
        
        _onSave:function(){
            this.onSave(this.get('value'));
        },
        
        onSave:function(value){
            
        }
            
    });
    
    return _class;
})