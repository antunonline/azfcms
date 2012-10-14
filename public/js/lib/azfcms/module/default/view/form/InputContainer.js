/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/InputContainer.html','dojo/_base/lang','dojo/dom-construct',
    'dojo/dom-geometry','dojo/dom-style','dojo/dom-class'],
    function(declare,_Widget,_TemplatedMixin,_WidgetsInTemplate,
        templateString,lang,domConstruct, domGeometry,domStyle, domClass){
        return declare('azfcms.view.widget.form.InputContainer',[_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString:templateString,
            label:"",
            nativeType:"text",
            constructor:function(props){
            
            if(typeof props !='undefined'){
                lang.mixin(this,props);
            }
                /**
             * Container label
             */
                this.label;
                
                /**
                 * Type of input component, will be injected into data-dojo-type attribubte
                 */
                this.inputType;
                
                /**
                 * Native type of input element, defaults to text
                 */
                this.nativeType;
                
                /**
                 * WIdget of this.inputType type
                 */
                this.inputWidget;
                
                /**
                 * Style that will be appliced to input element
                 */
                this.inputStyle;
                
                /**
                 * Current widget layout
                 */
                this.layout = "regular";
                
                
                /**
                 * If set to true, css class inputWidget will not be
                 * assigned to input widget.
                 */
                this.disableInputWidgetCssTweaks;
            },
            
            _getValueAttr:function(){
                return this.inputWidget.get('value');
            },
            
            _setValueAttr:function(value){
                this.inputWidget.set('value',value);
            },
            
            _setMessagesAttr:function(messages){
                // If error already existed
                if(this.errorDom){
                    domConstruct.destroy(this.errorDom);
                }
                
                var innerHTMLChunks=[];
                innerHTMLChunks.push("<ul>");
                for(var name in messages){
                    innerHTMLChunks.push("<li>");
                    innerHTMLChunks.push(messages[name]);
                    innerHTMLChunks.push("</li>");
                }
                innerHTMLChunks.push("</ul>");
                
                // If no message is present
                if(!name){
                    return;
                }
                
                this.errorDom = domConstruct.place(innerHTMLChunks.join(""),this.errorNode);
            },
            
            postCreate:function(){
                this.inherited(arguments);
                if(this.inputStyle){
                    this.inputWidget.set("style",this.inputStyle);
                }
                
                if(!this.disableInputWidgetCssTweaks){
                    domClass.add(this.inputWidget.domNode,'inputWidget');
                }
                
                this.resize();
            },
            
            _calculateInputElementWidth:function(){
                var box = domGeometry.getContentBox(this.domNode);
                return box.w-170-10+"px";
            },
            
            _getLayoutAttr:function(){
                return this.layout;
            },
            
            _setLayoutAttr:function(layout){
                if(layout=="regular"){
                    this.set("regularLayout",null);
                } else {
                    this.set("rowLayout",null);
                }
            },
            
            _setRegularLayoutAttr:function(){
                domClass.replace(this.labelNode,"label","wideLabel");
                domClass.replace(this.containerNode,"input","wideInput");
                this.layout = "regular";
            },
            
            _setRowLayoutAttr: function(){
                domClass.replace(this.labelNode,"wideLabel","label");
                domClass.replace(this.containerNode,"wideInput","input");
                this.layout = "row";
            },
            
            resize:function(){
                var width = this._calculateInputElementWidth();
                var style = this.containerNode.style;
                if(parseInt(width)>99){
                    if(this.get("layout")!="regular"){
                        this.set("layout","regular");
                    }
                    style.width = width;
                } else {
                    style.width = "auto";
                    if(this.get("layout")!="row"){
                        this.set("layout","row");
                    }
                }
            },
            
            _setDisabledAttr:function(value){
                this.inputWidget.set("disabled",value);
            }
        });
    }
    )

