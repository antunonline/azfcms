define(
    ['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/UserForm.html','dojo/i18n!../resource/i18n/nls/UserManagment',
    'dojo/_base/lang','dijit/Tooltip',
    
    'dijit/form/TextBox','dijit/form/Button','dijit/layout/ContentPane','dijit/layout/BorderContainer',
    'dijit/form/CheckBox','dijit/Toolbar','dijit/form/ValidationTextBox','azfcms/view/widget/form/InputContainer'

    ],function
    (declare, 
        _Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
        nls,lang, Tooltip
    
        )
        {
        var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            // This property represents a template string which will be used to 
            // dynamicall construct user interface elements
            constructor:function(){
                /**
             * dijit.Tooltip registry used to present error messags
             */ 
                this.tooltips={};
          
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
        
            _setLoginNameMessage:{
                node:'loginNameMessage',
                type:"innerHTML"
            },
        
            _getValueAttr:function(){
                return {
                    id:this.id|0,
                    loginName:this.loginNameInput.get('value'),
                    firstName:this.firstNameInput.get('value'),
                    lastName:this.lastNameInput.get('value'),
                    email:this.emailInput.get('value'),
                    password:this.passwordInput.get('value'),
                    verified:this.verifiedInput.get('value')?1:0,
                    verificationKey:this.verificationKeyInput.get('value')
                };
            },
            _setValueAttr:function(user){
                this.id = user.id||"";
                this.loginNameInput.set('value',user.loginName);
                this.firstNameInput.set('value',user.firstName);
                this.lastNameInput.set('value',user.lastName);
                this.emailInput.set('value',user.email);
                this.passwordInput.set('value',user.password||"");
                this.verifiedInput.set('checked',parseInt(user.verified)?true:false);
                this.verificationKeyInput.set('value',user.verificationKey);
            },
        
            /**
             * Messages: 
             * {
             *  loginName:{
             *      recordFound:"A record matching root was found",
             *      stringLength: "Length of string must be between 3 and 40 characters long"
             *  },
             *  {...}
             * }
             */
            _setMessagesAttr:function(messages){
             // Remove old messages
             var emptyMsg={};
             var name;
             for(var i=0,len=this._attachPoints.length;i<len;i++){
                 name = this._attachPoints[i];
                 this[name].set('messages',emptyMsg);
             }
                var inputPropName;
                for(var name in messages){
                    inputPropName = name+'Input';
                    if(this[inputPropName]){
                        this[inputPropName].set("messages",messages[name]);
                    }
                }
            },
        
        
            _onSave:function(){
                this.onSave(this.get('value'));
            },
        
            onSave:function(user){
            
            }
            
        });
    
        return _class;
    })