/* 
 * @author Antun Horvat
 */


define(  ['dojo/_base/declare','dojo/text!./templates/DijitEditor.html','dijit/_Widget','dijit/_WidgetsInTemplateMixin','dijit/_TemplatedMixin',
    'dojo/i18n!azfcms/resources/i18n/cms/common/nls/common',

'dijit/Editor','dijit/form/Button','dijit/layout/BorderContainer',
'dijit/layout/ContentPane','dijit/Toolbar'],
function (declare,templateString,_Widget,_WidgetsInTemplate,_Templated,nls){
    return declare([_Widget,_Templated,_WidgetsInTemplate],{
        templateString:templateString,
        closable:true,
        constructor:function(){
            for(var name in nls){
                if(name.indexOf("extDijit")==0){
                    this[name] = nls[name];
                }
            }
        }
        })
})