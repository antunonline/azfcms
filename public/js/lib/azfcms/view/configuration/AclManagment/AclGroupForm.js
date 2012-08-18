define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/AclGroupForm.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/AclManagment',
    'dojo/_base/lang',
    
    'dijit/layout/ContentPane','dijit/Toolbar','dijit/form/Button','azfcms/view/widget/form/InputContainer',
    'dijit/layout/BorderContainer','dijit/form/TextBox','dijit/form/Textarea'

],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang
    
    )
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        constructor:function(){
          
          this.init();
        },
        templateString: templateString,
        closable:true,
        nls:nls,
        init:function(){
            // In this method you can initialize the view
        },
        /**
         * Implement resize method. Call all direct descendants resize methods.
         */
        resize:function(){
            this.borderContainer.resize();
        },
        
        _getValueAttr:function(){
            return {
                name:this.nameInput.get('value'),
                description:this.descriptionInput.get('value')
            }
        },
        
        
        _setValueAttr:function(value){
            this.nameInput.set('value',value.name);
            this.descriptionInput.set('value',value.description);
        },
        
        _setMessagesAttr:function(messages){
            
            for(var i = 0,prop='',len=this._attachPoints.length;i<len;i++){
                prop = this._attachPoints[i];
                if(this[prop].set && typeof this[prop].set == 'function'){
                    this[prop].set("messages",{});
                }
            }
            
            for(var name in messages){
                prop = name+"Input";
                if(typeof this[prop].set == 'function'){
                    this[prop].set('messages',messages[name]);
                }
            }
        },
        
        _fireSave:function(){
            this.onSave(this.get('value'));
        },
        
        onSave:function(value){
            
        }
            
    });
    
    return _class;
})