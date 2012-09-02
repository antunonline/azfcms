/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','dojo/text!./templates/LinkList.html','dijit/_Widget','dijit/_WidgetsInTemplateMixin','dijit/_TemplatedMixin',
    'dojo/_base/lang',
    
    
    'dijit/layout/BorderContainer','dijit/layout/ContentPane','dijit/Toolbar','dijit/form/Button',
    'dijit/form/Textarea','dijit/form/TextBox'],
    function (declare,templateString,_Widget,_WidgetsInTemplate,_Templated,
        lang){
        return declare([_Widget,_Templated,_WidgetsInTemplate],{
            templateString:templateString,
            constructor:function(args){
                /**
                 * Textarea widget
                 */
                this.textarea;
                
                /**
                 * toolbar widget
                 */
                this.toolbar;
                
                /**
                 * Title textbox
                 */
                this.linkGroupName;
                
            },
        
            postCreate:function(){
                this.inherited(arguments);
            },
        
            _save:function(){
                this.onSave(this.get('value'));
            },
            
            _setValueAttr:function(object){
                var csvLinks = this._linksToCsv(object.linkList);
                this.textarea.set("value",csvLinks);
                this.linkListName.set("value",object.title);
            },
            
            _getValueAttr:function(){
                var csvLinks = this.textarea.get("value");
                var linkList = this._csvToLinks(csvLinks);
                var title = this.linkListName.get("value");
                return {
                    title:title,
                    linkList:linkList
                };
            },
            
            _csvToLinks:function(csvList){
                rows = csvList.split("\n");
                var linkList=[];
                var row,indexOf;
                for(var i = 0, len = rows.length;i<len;i++){
                    row = rows[i];
                    if(0>(indexOf = row.indexOf(','))){
                        continue;
                    }
                    
                    linkList.push({
                        name:lang.trim(row.substring(0,indexOf)),
                        url:lang.trim(row.substring(indexOf+1))
                    });
                }
                
                return linkList;
            },
            
            _linksToCsv:function(links){
                var csv = [];
                for(var i=0,len=links.length;i<len;i++){
                    csv.push(links[i].name+","+links[i].url);
                }
                return csv.join("\n");
            },
            
            
            /**
             * onSave event that is activated when the user selects the save button.
             * The First argument is an array of links where link is {name:"",url:""} object.
             */
            onSave:function(values){
                
            }
        })
    })