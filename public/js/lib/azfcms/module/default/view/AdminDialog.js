
define(
    ['dojo/_base/declare','dijit/Dialog','dijit/layout/ContentPane','dijit/layout/TabContainer',
    'dojo/dom-style','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common',
    'dojo/dom-geometry'],function
    (declare,Dialog,ContentPane,TabContainer,
        domStyle,nls, domGeometry){
        var _class = declare([TabContainer],{     
            postCreate:function(){
                this.inherited(arguments);
                this.startup();
                this.resize(domGeometry.getContentBox(this.domNode.parentNode));
            },
            
            addChild:function(){
                this.inherited(arguments);
                this.resize();
            },
            
            resize: function(){
                this.inherited(arguments);
            }
        });
    
        // return class
        return _class;
    })
