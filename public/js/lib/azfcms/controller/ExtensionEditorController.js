
define(
    ['dojo/_base/declare','dojo/_base/lang'],function
    (declare, lang){
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
                this.navigationId = args.navigationId;
                
                this._attachEventListeners();
            },
            
            _attachEventListeners: function(){
                this.editorPane.on("new",lang.hitch(this,"onNew"));
                this.editorPane.on("save",lang.hitch(this,"onSave"));
                this.editorPane.on("delete",lang.hitch(this,"onDelete"));
                this.editorPane.on("enable",lang.hitch(this,"onEnable"));
                this.editorPane.on("disable",lang.hitch(this,"onDisable"));
                this.editorPane.on("extendedEdit",lang.hitch(this,"onExtendedEdit"));
                this.editorPane.on("itemSelect",lang.hitch(this,"onItemSelect"));
                this.editorPane.on("regionSelect",lang.hitch(this,"onRegionSelect"));
            },
    
            _buildRequire: function(type){
                function _ucFirst(str) {
                    return str[0].toUpperCase()+str.substring(1);
                    
                }
                var name = _ucFirst(type);
                return [
                'azfcms/controller/extension/'+name,
                'azfcms/view/extension/'+name
                ];
            },
            _buildEditorPane:function(AbstractExtensionPane){
                var aep = new AbstractExtensionPane();
                this.editorPane.addChild(aep);
                return aep;
            },
            _buildController: function(Controller, pluginId, extendedEditorPane){
                return new Controller({
                    pluginId:pluginId,
                    editorPane:extendedEditorPane
                });
                
            },
            onNew: function(name,description,type,region,weight,enable){
                var self = this;
                this.editorPane.disable();
                this.model.addExtensionPlugin(this.navigationId,name,description,type,region,weight,enable).then(function(){
                    self.editorPane.reloadGrid(self.navigationId,region).
                    then(function(){
                        self.editorPane.regionSelect.set('value',region);
                        self.editorPane.enable();
                    })
                })
            },
            onSave: function(pluginId, name,description,type, region,weight,enable){
                var self = this;
                this.editorPane.disable();
                this.model.setExtensionPluginValues(this.navigationId, pluginId,name,description, region,weight,enable).then(function(){
                    self.editorPane.reloadGrid(self.navigationId,region).
                    then(function(){
                        self.editorPane.regionSelect.set('value',region);
                        self.editorPane.enable();
                    })
                })
            },
            onDelete:function(pluginId){
                var self = this;
                this.editorPane.disable();
                this.model.removeExtensionPlugin(pluginId).then(function(){
                    self.editorPane.reloadGrid(self.navigationId,region).
                    then(function(){
                        self.editorPane.enable();
                    })
                })
            },
            onDisable: function(pluginId){
                var self = this;
                this.editorPane.disable();
                this.model.disableExtensionPlugin(this.navigationId,pluginId).then(function(){
                    self.editorPane.reloadGrid(self.navigationId,self.editorPane.get('form').region).
                    then(function(){
                        self.editorPane.enable();
                    })
                })
            },
            onEnable: function(pluginId, weight){
                var self = this;
                this.editorPane.disable();
                this.model.enableExtensionPlugin(this.navigationId, pluginId).then(function(){
                    self.editorPane.reloadGrid(self.navigationId,self.editorPane.get('form').region).
                    then(function(){
                        self.editorPane.enable();
                    })
                })
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
            },
            onRegionSelect:function(region){
                var self = this;
                this.editorPane.disable();
                this.editorPane.reloadGrid(this.navigationId,region).
                then(function(){
                    self.editorPane.enable();
                })
                
            }
        })
    
    })

