/* 
 * @author antunonline@gmail.com
 */

define(
    ['dojo/_base/declare','dijit/Dialog','azfcms/view/ConfirmPane',
    'dojo/i18n!azfcms/resources/nls/view'],function
    (declare,Dialog,ConfirmPane,
        nls)
        {
        return declare([],{
            _confirmDialog:null,
            _confirmPane:null,
            alert:function(message){},
            getConfirmPane:function(){
                if(this._confirmPane)
                    return this._confirmPane;
                this._confirmDialog = new Dialog({
                    title:nls.utlConfirmDialogTitle
                });
                var pane = this._confirmPane= new ConfirmPane();
                this._confirmDialog.on("cancel",function(){
                    pane.fireReject();
                })
                this._confirmDialog.set("content",this._confirmPane);
                return this._confirmPane;
            },
            getConfirmDialog:function(){
                if(!this._confirmDialog)
                    this.getConfirmPane();
                return this._confirmDialog;
            },
            confirm:function(callback,message,acceptLabel,rejectLabel){
                var confirmDialog = this.getConfirmDialog();
                this.getConfirmPane().confirm(function(value){
                    callback(value);
                    confirmDialog.hide();
                },message,acceptLabel,rejectLabel);
                this.getConfirmDialog().show();
            },
            alert:function(message){
                window.alert(message);
            }
        });
    })


