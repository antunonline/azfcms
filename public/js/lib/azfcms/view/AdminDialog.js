
define(
    ['dojo/_base/declare','dijit/Dialog','dijit/layout/ContentPane','dijit/layout/TabContainer',
    'dojo/window','dojo/i18n!azfcms/resources/nls/view'],function
    (declare,Dialog,ContentPane,TabContainer,
        dojoWindow,nls){
        var _class = declare([Dialog],{
            constructor: function(){
                // Define tab container reference
                this.tabContainer = null;
                
                // Set 
                this.title = nls.adTitle;
                
                this.closable = false;
                
                /**
                 * Border container reference
                 */
                this.borderContainer = null;
                
                // Set style
                this.style = "width:95%;height:95%"
            },
            
            postCreate: function(){
                this.inherited(arguments);
                
                
                // Create tab container
                this.tabContainer = new TabContainer({});
                
                // Insert tab container into Dialog
                this.set("content",this.tabContainer);
                this.resize();
                
                    
            },
            /**
             * Add content pane child
             * @property {ContentPane} cp 
             */
            addChild: function(cp){
                this.tabContainer.addChild(cp);
            },
            
            /**
             * Remove child
             */
            removeChild: function(cp){
                this.tabContainer.removeChild(cp);
            },
            
            // Override resize function
            resize: function(){
                
                // Calculate height
                var height = dojoWindow.getBox().h*0.95 - 40;

                var style = "width:100%;height:"+String(height)+"px";

                // Set tabContainer style
                this.tabContainer.set("style",style);
                // Resizetab container
                
                this.inherited(arguments);
            },
            
            /**
             * Override show function so that it initiates resize event
             */
            show: function(){
                this.inherited(arguments);
                this.resize();
            }
        });
    
        // return class
        return _class;
    })
