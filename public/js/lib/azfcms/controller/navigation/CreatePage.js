/* 
 * @author Antun Horvat
 */

define(
['dojo/_base/declare','azfcms/model/navigation'],function
(declare,navigation)
{
    var _class = declare(null,{
        constructor: function(cpd){
            // Store dialog reference
            this.cpd = cpd;
            this.form= cpd.form;
            // Reference of the selected node
            this.node = null;
            
            var cpc = this;
            var self = this;
            this.form.on("action",function(title,type){
                self.form.disable();
                cpc.insertNode(title,type)
            });
        },
        
        insertNode: function(title,type){
            this.cpd.set("disabled",true);
            var form = this.form;
            form.disable();
            navigation.insertInto(this.node.id,title,type).
                then(function(){
                form.reset();
                form.enable();
                });
        },
        
        show: function(node){
            this.node = node;
            this.form.reset();
            this.cpd.show();
        }
    });
    
    return _class;
})
