/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(
['dojo/_base/declare','dijit/layout/BorderContainer','dijit/layout/ContentPane',
'dijit/form/ComboBox','dijit/form/TextBox','dijit/form/Button'],function
(declare, BorderContainer, ContentPane,
ComboBox, TextBox, Button)
{
   var _class = declare([BorderContainer],{
       constructor: function(){
           
           this.metadataPane = new ContentPane({
               region:"right",
               style:"width:200px"
           });
           
           this._constuctMetadataPane();
           
       },
       addMetadataChildNode: function(node){
           this.metadataPane.domNode.appendChild(node);
       },
       addMetadataLineBreak: function(numOfLineBreaks){
           if(arguments.length == 0){
               this.addMetadataChildNode(document.createElement("br"));
           }
           else {
               for(var i = 0; i < numOfLineBreaks; i++){
                   this.addMetadataChildNode(document.createElement("br"));  
               }
           }
         
       },
       
       _constuctMetadataPane:function(){
           // Add Header
           this.addMetadataChildNode(document.createTextNode("Vrsta sadr\u017eaja"));
           this.addMetadataLineBreak(1);
           // Add combo box
           this.typeComboBox = new ComboBox({
               store:[{name:"OK"}],
               labelAttr:"name"
           });
           this.addMetadataLineBreak(1);
           this.addMetadataChildNode(this.typeComboBox.domNode);
           
           // Add change type button
           this.addMetadataLineBreak(2);
           var button = new Button({label:"Promjeni vrstu"});
           this.addMetadataChildNode(button.domNode);
           
           // Add title text box
           this.addMetadataLineBreak(3);
           this.nameTextBox = new TextBox({});
           this.addMetadataChildNode(this.nameTextBox.domNode);
           
           
           
           
       },
       
       postCreate: function(){
           this.inherited(arguments);
           
           this.addChild(this.metadataPane);
       }
   }) ;
   
   
   return _class;
});


