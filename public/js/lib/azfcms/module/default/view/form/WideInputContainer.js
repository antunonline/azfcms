/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/WideInputContainer.html','dojo/_base/lang','dojo/dom-construct',
    'dojo/dom-geometry','dojo/dom-style','dojo/dom-class'],
    function(declare,_Widget,_TemplatedMixin,_WidgetsInTemplate,
        templateString,lang,domConstruct, domGeometry,domStyle, domClass){
        return declare('azfcms.view.widget.form.WideInputContainer',[_Widget,_TemplatedMixin,_WidgetsInTemplate],{
            templateString:templateString,
            label:"",
            nativeType:"text",
            constructor:function(){
            
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
                this.inputStyle = "";
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
                domClass.add(this.inputWidget.domNode,'inputWidget');
            },
            
            _setDisabledAttr:function(value){
                this.inputWidget.set("disabled",value);
            }
        });
    }
    )

