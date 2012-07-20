/* 
 * @email antunhorvat@gmail.com
 * @author Antun Horvat
 * 
 */

define(['dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dijit/_Widget',
    'dojo/_base/lang','dojo/_base/declare','dojo/text!./templates/FileSelectPane.html',
    'dijit/Tree','dijit/form/Button'],function
    (_TemplatedMixin,_WidgetsInTemplateMixin,_Widget,
        lang,declare,templateString,
        Tree)
        {
        return declare([_Widget,_TemplatedMixin,_WidgetsInTemplateMixin],{
            templateString:templateString,
            
            constructor:function(args){
                if(!args){
                    args = {}
                }
              
                lang.mixin(this,args);
            },
            
            destroy:function(){
                this.inherited(arguments);
                this.treeStore.destroy();
            },
            
            postCreate:function(){
                new Tree({
                    model:this.treeStore,
                    style:"height:350px;",
                    autoExpand:false
                },this.tree)
            },
            getTreeSelection:function(){
                if("treeSelect"in this){
                    return this.treeSelect;
                } else {
                    return null;
                }
            },
            _onSelect:function(){
                var selection = this.getTreeSelection();
                this.onSelect(selection?selection:[]);
            },
            _onCancel:function(){
                this.onCancel();
            },
            
            
            onSelect:function(files){},
            onCancel:function(){},
            onReload:function(){}
        });
    
    
    })


