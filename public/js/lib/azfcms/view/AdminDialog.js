
define(
    ['dojo/_base/declare','dijit/Dialog','dijit/layout/ContentPane','dijit/layout/TabContainer',
    'dojo/dom-style','dojo/i18n!azfcms/resources/nls/view'],function
    (declare,Dialog,ContentPane,TabContainer,
        domStyle,nls){
        var _class = declare([Dialog],{     
            constructor: function(){
                // Define tab container reference
                this.tabContainer = null;
                
                // Set 
                this.title = nls.adTitle;
                
                this.closable = false;
                duration:0,
                
                /**
                 * Border container reference
                 */
                this.borderContainer = null;
                
                // Set style
                this.style = "width:95%;height:95%"
            },
            
            postCreate: function(){
                this.inherited(arguments);
                
                this.contentPane = new ContentPane();
                this.set("content",this.contentPane);
                
                // Create tab container
                this.tabContainer = new TabContainer({
                    style:"width:100%"
                });
                
                // Insert tab container into Dialog
                this.contentPane.set("content",this.tabContainer);
                
                var self = this;
                this.on("show",function(){
                    window.setTimeout(function(){
                        self.resize();
                    },this.duration+100)
                })
                
                    
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
                var cs = domStyle.getComputedStyle(this.domNode);
                var height = parseInt(cs.height);
                var width = parseInt(cs.width);

                var style = "width:100%;height:"+String(height-40)+"px";

                // Set tabContainer style
                this.tabContainer.set("style",style);
                // Resizetab container
                
                
                this.inherited(arguments);
            }
        });
    
        // return class
        return _class;
    })
