
define(
    ['dojo/_base/declare','dojo/_base/lang','azfcms/view/util','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common'],function
    (declare, lang,util,nls){
        return declare([],{
            constructor: function(args){
                /**
             * @property {azfcms.view.ExtendedEditorPane}
             */
                this.editorPane = args.editorPane;
                /**
             * @property {azfcms.model.cms}
             */
                this.model = args.model;
            
                /**
             * @property {Object}
             */
                this.pluginItem;
    
                /**
             * @property {Number}
             */
                this._attachEventListeners();
                
                if(!args.util) {
                    this.util = util;
                }else{
                    this.util = args.util;
                }
            },
            
            _attachEventListeners: function(){
                this.editorPane.on("new",lang.hitch(this,"onNew"));
                this.editorPane.on("save",lang.hitch(this,"onSave"));
                this.editorPane.on("delete",lang.hitch(this,"onDelete"));
                this.editorPane.on("extendedEdit",lang.hitch(this,"onExtendedEdit"));
                this.editorPane.on("itemSelect",lang.hitch(this,"onItemSelect"));
            },
    
            _buildRequire: function(type){
                function _ucFirst(str) {
                    return str.substring(0,1).toUpperCase()+str.substring(1);
                    
                }
                var name = _ucFirst(type);
                return [
                'azfcms/controller/extensionPlugin/'+name,
                'azfcms/view/extensionPlugin/'+name
                ];
            },
            _buildEditorPane:function(AbstractExtensionPane){
                if(AbstractExtensionPane==false)
                    return false;
                var aep = new AbstractExtensionPane();
                aep.closable=true;
                this.editorPane.addChild(aep);
                var title = this.editorPane.get("form").name;
                aep.set("title",title);
                
                return aep;
            },
            _buildController: function(Controller, pluginId, extendedEditorPane){
                return new Controller({
                    pluginId:pluginId,
                    view:extendedEditorPane
                });
                
            },
            onNew: function(item){
                var self = this;
                this.editorPane.disable();
                this.model.add(item).then(function(){
                    self.editorPane.reloadGrid();
                    self.editorPane.enable();
                })
            },
            onSave: function(item){
                var self = this;
                this.editorPane.disable();
                this.model.put(item).then(function(){
                    self.editorPane.reloadGrid();
                    self.editorPane.enable();
                })
            },
            onDelete:function(pluginId){
                var self = this;
                this.util.confirm(function(confirmed){
                    if(!confirmed)
                        return; 
                    
                    self.editorPane.disable();
                    self.model.remove(pluginId).then(function(){
                        self.editorPane.enable();
                        self.editorPane.reloadGrid();
                    })
                },nls.eecDeleteConfirmation);
            },
            onExtendedEdit: function(pluginId,type){
                var self = this;
                var requires = this._buildRequire(type);
                require(requires,function(Controller,View){
                    var view = self._buildEditorPane(View);
                    var controller = self._buildController(Controller,pluginId,view);
                })
            },
            onItemSelect: function(item){
                this.editorPane.disable();
                this.pluginItem = item;
                this.editorPane.set("form",item);
                this.editorPane.enable();
            }
        })
    
    })

