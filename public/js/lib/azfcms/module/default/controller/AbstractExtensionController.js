/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(['dojo/_base/declare'],function(declare){
    return declare(null,{
        constructor:function(args){
            this.editorPane = args.editorPane;
            this.pluginId = args.pluginId;
            this.init();
        },
        init: function(){}
    });
});

