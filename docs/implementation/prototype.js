azfcms.model.cms.addExtensionPlugin(function(name,description,type,region,weight,enable){
    Application_Resolver_ExtensionPlugin.addExtensionPlugin(function(){
        var pluginId = Application_Model_DbTable_Plugin.insertPlugin(function(name,description,type,region,params){});
        
        var Azf_Plugin_Extension_Abstract = 
        Azf_Plugin_Extension_Manager.setUp(function(type, pluginId){
            var Azf_Plugin_Extension_Abstract= Azf_Plugin_Extension_Manager._getPluginInstance(function(type,pluginId, pluginParams){
                if(is_array(pluginParams)==false){
                    // Construct plugin params if not present
                    pluginParams = Azf_Plugin_Extension_Manager.getPluginParams(function(pluginId){
                        Application_Model_DbTable_Plugin = Azf_Plugin_Extension_Manager.getModel();
                        return Application_Model_DbTable_Plugin.getPluginParams(function(pluginId){
                            var params = Application_Model_DbTable_Plugin.find(pluginId)['params'];
                            return Application_Model_DbTable_Plugin._decode(params);
                        })
                    })
                } 
                
                // Construct instance
                return Azf_Plugin_Extension_Manager._constructPlugin(function(type, pluginParams){
                    var className = Azf_Plugin_Extension_Manager.getClassName(type);
                    return new className(function(pluginParams){
                        return Azf_Plugin_Extension_Abstract.__construct(function(pluginParams){
                            Azf_Plugin_Extension_Abstract.setParams(pluginParams);
                        })
                    })
                });
            });
            
            try{
                Azf_Plugin_Extension_Abstract.setUp();
            }catch(e){
                
            }
        })
    })
    
});



azfcms.model.cms.removeExtensionPlugin(function(pluginId){
    Application_Resolver_ExtensionPlugin.removeExtensionPlugin(function(pluginId){
        // Load plugin type
        var type = Application_Model_DbTable_Plugin.find(pluginId)['type'];
        
        // Remove plugin related resources
        Azf_Plugin_Extension_Manager.tearDown(function(type, pluginId){
            Azf_Plugin_Extension_Abstract = Azf_Plugin_Extension_Manager._getPluginInstance(type, pluginId);
            try{
                Azf_Plugin_Extension_Abstract.tearDown();
            } catch(e){
                    
            }
        });
    
        // Delete plugin
        Application_Model_DbTable_Plugin.deleteById(function(pluginId){
            Application_Model_DbTable_Plugin['delete']();
        })
    })
})


azfcms.model.cms.getTemplateRegionsStore(function(navigationId){
    return Application_Resolver_Template.getTemplateRegions(function(){
        var templateIdentifier = Azf_Model_Tree_Navigation.getDynamicParam(navigationId, 'templateIdentifier');
        return Azf_Template_Descriptor.getRegions(function(templateIdentifier){
            return this.getTemplate(templateIdentifier)['regions'];
        })
    })
})


azfcms.model.cms.getRegionPluginsStore(function(nodeId, region){
    return getRegionExtendedPluginsStore.getRegionExtendedPluginsStore(function(nodeId, region){
        return Application_Model_DbTable_NavigationPlugin.findAllByNavigationAndRegion(nodeId,region);
    });
})


azfcms.model.cms.enableExtensionPlugin(function(nodeId, pluginId,weight){
    return Application_Resolver_ExtensionPlugin.enableExtensionPlugin(function(nodeId, pluginId, weight){
        return Application_Model_DbTable_NavigationPlugin.bind(nodeId, pluginId, weight);
    })

})


azfcms.model.cms.disableExtensionPlugin(function(nodeId,pluginId){
    return Application_Resolver_ExtensionPlugin.disableExtensinPlugin(function(nodeId,pluginId){
        Application_Model_DbTable_NavigationPlugin.unbind(nodeId,pluginId);
        return true;
        
    });
})

azfcms.model.cms.setExtensionPluginValues(function(navigationId, pluginId, name,description,region,weight,enable){
    return Application_Resolver_ExtensionPlugin.setExtensionPluginValues(function(navigationId, pluginId, name,description,region,weight,enable){
        if(enabled==false){
            Application_Model_DbTable_NavigationPlugin.unbind(navigationId, pluginId);
        } else {
            Application_Model_DbTable_NavigationPlugin.updateWeightByNavigationIdAndPluginId(navigationId, pluginId, weight);
        }
        Application_Model_DbTable_Plugin.updatePluginValues(pluginId, name,description, region);
        
    })
})

azfcms.model.getExtensionPluginStore(function(){
    return Application_Resolver_PluginDescriptor.getExtensionPlugins();
})


azfcms.view.ExtendedEditorPane.constructor(function(args){
    args = {
        regionStore: azfcms.model.cms.getTemplateRegionsStore(navigationId),
        gridStore: azfcms.model.cms.getRegionPluginsStore(nodeId, region),
        typeStore: azfcms.model.cms.getExtensionPluginStore()
    }
    azfcms.view.ExtendedEditorPane.regionStore = args.regionStore;
    azfcms.view.ExtendedEditorPane.typeStore = args.typeStore;
    azfcms.view.ExtendedEditorPane.gridStore = args.pluginsStore;
});

azfcms.view.ExtendedEditorPane.postCreate(function(args){
    azfcms.view.ExtendedEditorPane.pluginGrid.set("store",azfcms.view.ExtendedEditorPane.gridStore);
    azfcms.view.ExtendedEditorPane.typeSelect.set("store",azfcms.view.ExtendedEditorPane.typeStore);
    azfcms.view.ExtendedEditorPane.regionSelect.set("store",azfcms.view.ExtendedEditorPane.regionStore);
   
})

azfcms.view.ExtendedEditorPane.onNew(function(name,description,type,region,weight,enable){
    if(region==false || type == false)return;
    azfcms.controller.ExtendedEditController.onNew(function(){
        azfcms.view.ExtendedEditorPane.disable();
        azfcms.model.cms.addExtensionPlugin(name, description, type, region, weight, enable)(function(){
            
            azfcms.view.ExtendedEditorPane.reloadGrid(function(){
                azfcms.view.ExtendedEditorPane.pluginGrid.set("store",azfcms.view.ExtendedEditorPane.gridStore)
            });
            azfcms.view.ExtendedEditorPane.enable();
        })
    })
});


azfcms.view.ExtendedEditorPane.onSave(function(navigationId, pluginId, name,description,region,weight,enable){
    azfcms.controller.ExtendedEditController.onSave(function(pluginId, name,description,region,weight,enable){
        azfcms.view.ExtendedEditorPane.disable();
        azfcms.model.cms.setExtensionPluginValues(navigationId, pluginId, name,description,region,weight,enable)(function(){
            azfcms.view.ExtendedEditorPane.enable();
        })(function(){
            azfcms.view.ExtendedEditorPane.reloadGrid();
            azfcms.view.ExtendedEditorPane.enable();
        })
    })
});

azfcms.view.ExtendedEditorPane.onDelete(function(pluginId){
    azfcms.controller.ExtendedEditController.onDelete(function(pluginId){
        azfcms.view.ExtendedEditorPane.disable();
        azfcms.model.cms.removeExtensionPlugin(pluginId)(function(){
            azfcms.view.ExtendedEditorPane.reloadGrid();
            azfcms.view.ExtendedEditorPane.enable();
        })
    })
});


azfcms.view.ExtendedEditorPane.onDisable(function(){
    azfcms.controller.ExtendedEditController.onDisable(function(navigationId, pluginId){
        azfcms.view.ExtendedEditorPane.disable();
        azfcms.model.cms.disableExtensionPlugin(navigationId, pluginId)(function(){
            azfcms.view.ExtendedEditorPane.reloadGrid();
            azfcms.view.ExtendedEditorPane.enable();
        })
    })
})

azfcms.view.ExtendedEditorPane.onEnable(function(navigationId, pluginId, weight){
    azfcms.controller.ExtendedEditController.onEnable(function(navigationId, pluginId, weight){
        azfcms.view.ExtendedEditorPane.disable();
        azfcms.model.cms.enableExtensionPlugin(navigationId, pluginId, weight)(function(){
            azfcms.view.ExtendedEditorPane.enable();
        })
    })
})



azfcms.view.ExtendedEditorPane.onExtendedEdit(function(pluginId,type){
    azfcms.controller.ExtendedEditController.onExtendedEdit(function(){
        var modules = azfcms.controller.ExtendedEditController._buildRequire(type);
        require(modules,function(AbstractExtendedController, AbstractExtendedPane){
            var pane = azfcms.controller.ExtendedEditController._buildEditorPane(function(AbstractExtendedPane){
                var pane  = new azfcms.view.AbstractExtendedPane._constructor();
                azfcms.view.ExtendedEditorPane.addChild(function(pane){
                    azfcms.view.ExtendedEditorPane.tabContainer.addChild(pane)
                });
                return pane;
            });
            
            var controller = azfcms.controller.ExtendedEditController._buildController(function(pluginId, extendedEditorPane){
                var controller = new azfcms.controller.AbstractExtendedController();
            });
        })
    })
})


Azf_Controller_Action_Helper_ExtendedPlugin.postDispatch(function(){
    var navigationId = Zend_Controller_Request_Http.getParam("id");
    Azf_Plugin_Extension_Manager.render(function(navigationId, Zend_Controller_Response_Http){
        var plugins = Azf_Plugin_Extension_Manager.getPluginDefinitions(function(navigationId){
            return Application_Model_DbTable_Plugin.findAllByNavigationId(function(navigationId){
                var rows = Zend_Db_Adapter_Abstract.fetchAll("");
                for(var row in rows){
                    row['params'] = Application_Model_DbTable_NavigationPlugin._decode(row['params']);
                }
                return rows;
            });
        });
        
        var responses = {};
        for(var plugin in plugins){
            var Azf_Plugin_Extension_Abstract = Azf_Plugin_Extension_Manager._getPluginInstance(plugin['type'],plugin['id'],plugin['params']);
            ob_start();
            Azf_Plugin_Extension_Abstract.render();
            responses[plugin['region']].push(ob_get_end());
        }
        
        foreach(function(responses, region, response){
            var rendered = response.join("");
            Zend_Controller_Response_Http.setBody(region,rendered);
        })
    });
    
})


