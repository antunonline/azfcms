/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
define(
['dojo/_base/declare','dijit/layout/BorderContainer','dijit/layout/ContentPane',
'dijit/form/ComboBox','dijit/form/TextBox','dijit/form/Button',
'dijit/form/Textarea','dojo/i18n!azfcms/resources/nls/view'],function
(declare, BorderContainer, ContentPane,
ComboBox, TextBox, Button,
Textarea, nls)
{
   var _class = declare([BorderContainer],{
       constructor: function(){
           this.closable = true;
           
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
           var cep = this;
           // Add Header
           this.addMetadataChildNode(document.createTextNode(nls.cepContentType));
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
           var button = new Button({label:nls.cepChangeType});
           this.addMetadataChildNode(button.domNode);
           
                     
           
           this.createMetadataTable();
           // Add label
           this.addMetadataTableChildText(nls.cepPageTitle);
           this.pageName = new TextBox({
               style:"width:100%"
           });
           this.addMetadataTableChild(this.pageName.domNode);
           
           // add description box
           this.addMetadataTableChildText(nls.cepPageDescription);
           this.pageDescription = new Textarea({
               style:"width:100%"
           });
           this.addMetadataTableChild(this.pageDescription.domNode);
           
           // add keywords box
           this.addMetadataTableChildText(nls.cepPageKeywords);
           this.pageKeywords = new Textarea({
               style:"width:100%"
           });
           this.addMetadataTableChild(this.pageKeywords.domNode);
           
           // add save button
           this.addMetadataTableChildText("");
           this.saveMetadataButton = new Button({
               label:nls.cepSave
           });
           this.addMetadataTableChild(this.saveMetadataButton.domNode);
           
           
           this.saveMetadataButton.on("click",function(){
               var title = cep.pageName.get("value");
               var description = cep.pageDescription.get("value");
               var keywords = cep.pageKeywords.get("value");
               
               cep.onMetadataSave(title, description, keywords)
           });
           
       },
       
       createMetadataTable: function(){
           var table = document.createElement("table");
           this.metadataTable = document.createElement("tbody");
           table.appendChild(this.metadataTable);
           this.addMetadataChildNode(table);
           
           this.intMetadataCellIndex = 0;
           this._addMetadataTableRow();
       },
       
       _addMetadataTableRow: function(){
           this.metadataRow  = document.createElement("tr");
           this.metadataTable.appendChild(this.metadataRow);
       },
       
       addMetadataTableChildText: function(text){
           var t = document.createTextNode(text);
           this.addMetadataTableChild(t);
       },
       
       addMetadataTableChild: function(node){
           if(this.intMetadataCellIndex==2){
               this._addMetadataTableRow();
               this.intMetadataCellIndex=0;
           }
           
           var td = document.createElement("td");
           if(this.intMetadataCellIndex==0){
               td.style.paddingRight="10px";
           }
           
           td.appendChild(node);
           this.metadataRow.appendChild(td);
           
           this.intMetadataCellIndex++;
       },
       
       postCreate: function(){
           this.inherited(arguments);
           
           this.addChild(this.metadataPane);
       },
       
       _setTitleAttr: function(title){
           this.pageName.set("value",title);
       },
       
       _setDescriptionAttr: function(description){
           this.pageDescription.set("value",description);
       },
       
       _setKeywordsAttr: function(keywords){
           this.pageKeywords.set("value",keywords);
       },
       
       onMetadataSave: function(title, description, keywords){
           
       }
   }) ;
   
   
   return _class;
});


