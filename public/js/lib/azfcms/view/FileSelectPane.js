/* 
 * @email antunhorvat@gmail.com
 * @author Antun Horvat
 * 
 */

define(['dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dijit/_Widget',
    'dojo/_base/lang','dojo/_base/declare','dojo/text!./templates/FileSelectPane.html',
    'dijit/Tree','dijit/form/Button','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane'],function
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
                this.tree = new Tree({
                    model:this.treeStore,
                    autoExpand:false
                },this.tree);
                this.tree.startup();
            },
            resize:function(){
                this.inherited(arguments);
                this.borderContainer.resize();
            },
            getTreeSelection:function(){
                return this.tree.selectedItems;
            },
            _onSelect:function(){
                var selection = this.getTreeSelection();
                this.onSelect(selection.length>0?selection:[]);
            },
            _onCancel:function(){
                this.onCancel();
            },
            
            
            onSelect:function(files){},
            onCancel:function(){},
            onReload:function(){}
        });
    
    
    })


