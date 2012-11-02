/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(['dijit/_WidgetBase','dijit/_WidgetsInTemplateMixin','dojo/_base/declare',
'dojo/text!./templates/GeneratorInvokerPane.html','dijit/_TemplatedMixin','dojo/dom-geometry',
'dijit/Dialog','dojo/topic',

'dijit/layout/BorderContainer','dijit/layout/TabContainer','dijit/layout/ContentPane',
'dijit/Toolbar','dijit/form/Button'],
function(_WidgetsBase, _WidgetsInTemplateMixin, declare,
    templateString, _TemplatedMixin, domGeometry, 
    Dialog,topic){
    return declare([_WidgetsBase, _TemplatedMixin, _WidgetsInTemplateMixin],
    {
        templateString:templateString,
        constructor:function(args){
            
        },
        postCreate:function(){
            this.inherited(arguments);
            this._initWidgets();
            this.getDialog().set("content",this);
            var self = this;
            topic.subscribe(this.tabContainer.id+"-selectChild",function(child){
                self.onChildSelect(child);
            })
            this.resize();
        },
        _initWidgets:function(){
            this.tabContainer.startup();
        },
        resize:function(){
            this.borderContainer.resize({
                l:0,
                t:0,
                h:400,
                w:640
            });
        },
        getDialog:function(){
            if(!this._dialog){
                this._dialog = new Dialog();
            }
            return this._dialog;
        },
        hide:function(){
            this.getDialog().hide();
        },
        show:function(){
            this.getDialog().show();
        },
        addChild:function(child){
            this.tabContainer.addChild(child);
        },
        selectChild: function(child){
            this.tabContainer.selectChild(child);
        },
        onChildSelect:function(child){
            
        },
        onAccept:function(){
            
        }
    })
})

