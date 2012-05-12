define(
    ['dojo/_base/declare','azfcms/controller/ContentEditController','azfcms/view/ContentEditPane',
    'azfcms/model','dojo/_base/lang','dojo/i18n!azfcms/resources/nls/view'],function
    (declare, CEC, CEP,
        model, lang, nls)
        {
        var _class = declare([],{
        
        
            constructor: function(adminDialog, navigationPane){
            
                /**
             * Store adminn dialog reference
             */
                this.adminDialog = adminDialog;
                /**
                 * Store navigation pane reference
                 */
                this.navigationPane = navigationPane;
            
                /**
             * This variable will hold currently selected navigation node
             */
                this.selectedNode = null;
                
                this._initNavigationListeners();
                this._initiNavigationButtons();
            },
            /**
             * Initialize navigation listeners
             */
            _initNavigationListeners: function(){
                var context = this;
                this.navigationPane.on("itemSelect",function(item){
                    context.selectNode(item)
                });
            },
            /**
             * Initialize navigation buttons
             */
            _initiNavigationButtons: function(){
                this.navigationPane.addButton(nls.npEditContent,lang.hitch(this,function(){
                    this.editSelectedPageContent();
                }),"dijitIconEdit")
            },
            
            
        
            selectNode: function(node){
                this.selectedNode = node;
            },
        
            editNode: function(){
            
            },
        
            editSelectedPageContent: function(){
                this.buildContentEditor();
            },
        
            buildContentEditor: function(){
                if(this.selectedNode==null){
                    return;
                }
                
                var cep = new CEP();
                this.adminDialog.addChild(cep);
            
                var cec = new CEC();
                cec.init(this.selectedNode.id,cep);
            }
        });
    
        return _class;
    })