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
            // Reference of the selected node
            this.node = null;
            
            var cpc = this;
            this.cpd.on("action",function(title,type){
                cpc.insertNode(title,type);
            });
        },
        
        insertNode: function(title,type){
            this.cpd.set("disabled",true);
            var cpd = this.cpd;
            navigation.insertInto(this.node.id,title,type).
                then(function(){
                cpd.enable();
                });
        },
        
        show: function(node){
            this.node = node;
            this.cpd.show();
        }
    });
    
    return _class;
})
