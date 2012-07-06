/* 
 * @author antunonline@gmail.com
 */

define(
    ['dojo/_base/declare','dojo/text!./templates/ConfirmPane.html','dojo/_base/lang',
    'dijit/_TemplatedMixin','dijit/_Widget','dijit/_WidgetsInTemplateMixin',
    'dojo/i18n!azfcms/resources/nls/view',

    'dijit/form/Button'],function
    (declare, templateString, lang,
        _TemplatedMixin,_Widget,_WidgetsInTemplateMixin,
        nls)
        {
        return declare([_Widget,_TemplatedMixin,_WidgetsInTemplateMixin],{
            templateString:templateString,
            messageNode:"",
            acceptButton:null,
            rejectButton:null,
            callback:[],
            constructor:function(args){
                this.cpAcceptLabel = nls.cpAcceptLabel;
                this.cpRejectLabel = nls.cpRejectLabel;
            },
            confirm:function(callback,message,acceptLabel/*optional*/,rejectLabel/*optional*/){
                this.set("message",message);
                this.callback.push(callback);
                
                if(rejectLabel)
                    this.rejectButton.set("label",rejectLabel);
                else
                    this.rejectButton.set("label",this.cpRejectLabel);
                if(acceptLabel)
                    this.acceptButton.set("label",acceptLabel);
                else 
                    this.acceptButton.set("label",this.cpAcceptLabel);
            },
            fireReject:function(){
                if(this.callback.length>0){
                    this._onReject();
                }
            },
            _setMessageAttr:function(value){
                this.messageNode.innerHTML = value;
            },
            _getMessageAttr:function(){
                return this.messageNode.innerHTML;
            },
            _onReject:function(){
                this.callback.shift()(false);
            },
            _onAccept:function(){
                this.callback.shift()(true);
            }
        });
    })


