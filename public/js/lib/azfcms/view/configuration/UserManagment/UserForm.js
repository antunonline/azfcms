define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UserForm.html','dojo/i18n!azfcms/resources/i18n/cms/configuration/nls/UserManagment',
    'dojo/_base/lang',
    
    'dijit/form/TextBox','dijit/form/Button','dijit/layout/ContentPane','dijit/layout/BorderContainer',
    'dijit/form/CheckBox','dijit/Toolbar'

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
                id:this.id|0,
                loginName:this.loginNameInput.get('value'),
                firstName:this.firstNameInput.get('value'),
                lastName:this.lastNameInput.get('value'),
                email:this.emailInput.get('value'),
                password:this.passwordInput.get('value'),
                verified:this.verifiedInput.get('checked'),
                verificationKey:this.verificationKeyInput.get('value')
            };
        },
        _setValueAttr:function(user){
            this.id = user.id||"";
            this.loginNameInput.set('value',user.loginName);
            this.firstNameInput.set('value',user.firstName);
            this.lastNameInput.set('value',user.lastName);
            this.emailInput.set('value',user.email);
            this.passwordInput.set('value',user.password|"");
            this.verifiedInput.set('checked',user.verified);
            this.verificationKeyInput.set('value',user.verificationKey);
        },
        
        _onSave:function(){
            this.onSave(this.get('value'));
        },
        
        onSave:function(user){
            
        }
            
    });
    
    return _class;
})