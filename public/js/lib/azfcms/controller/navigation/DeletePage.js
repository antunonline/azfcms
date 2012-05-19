/* 
 * @author Antun Horvat
 */

define(
['dojo/_base/declare','azfcms/model/navigation'],function
(declare,navigation)
{
    var _class = declare(null,{
        constructor: function(params){
            // setv iew
            this.view = params.view;
            
            // Set view
            if("model" in params){
                this.model = params.model;
            } else {
                this.model = navigation;
            }
            
            this.item = null;
            
            // Attach listeners
            this._attachListeners();
        },
        
        _attachListeners: function(){
            var self = this;
            this.view.form.on("confirm",function(){
                self.model.remove(self.item);
                self.view.hide();
            });
            this.view.form.on("cancel",function(){
                self.view.hide();
            });
        },
        
        activate: function(item){
            this.item = item;
            this.view.show(item.title);
        }
    });
    
    return _class;
})
