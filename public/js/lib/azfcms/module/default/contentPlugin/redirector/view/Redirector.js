define(
    ['dojo/_base/declare','azfcms/module/default/view/AbstractEditPane',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/Redirector.html','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common',

    'dijit/form/TextBox','dijit/form/Button'],function
    (declare, AbstractEditPane,
        _Widget, _TemplatedMixin, _WidgetsInTemplate,
        templateString, nls)
        {
        var _class = declare([AbstractEditPane,_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            /**
             * Url input element
             */
            url:null,
            templateString: templateString,
            constructor:function(){
                for(var name in nls){
                    if(name.indexOf("redir")==0){
                        this[name] = nls[name];
                    }
                }
            }
            ,
            
            _setUrlAttr:function(url){
                this.inputText.set("value",url);
            },
            _getUrlAttr:function(){
                return this.inputText.get("value");
            },
            
            _onSave:function(){
                this.onSave(this.get("url"));
            },
            
            onSave:function(url){
                
            }
            
        });
    
        return _class;
    })