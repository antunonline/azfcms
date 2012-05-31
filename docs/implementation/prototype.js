azfcms.model.cms.addExtensionPlugin(function(navigationId, name,description,type,region,weight,enable){
    Application_Resolver_ExtensionPlugin.addExtensionPluginMethod(function(){
        var pluginId = Azf_Model_DbTable_Plugin.insertPlugin(function(name,description,type,region,params){});
        
        var Azf_Plugin_Extension_Abstract = 
        Azf_Plugin_Extension_Manager.setUp(function(type, pluginId){
            var Azf_Plugin_Extension_Abstract= Azf_Plugin_Extension_Manager._getPluginInstance(function(type,pluginId, pluginParams){
                if(is_array(pluginParams)==false){
                    // Construct plugin params if not present
                    pluginParams = Azf_Plugin_Extension_Manager.getPluginParams(function(pluginId){
                        Azf_Model_DbTable_Plugin = Azf_Plugin_Extension_Manager.getModel();
                        return Azf_Model_DbTable_Plugin.getPluginParams(function(pluginId){
                            var params = Azf_Model_DbTable_Plugin.find(pluginId)['params'];
                            return Azf_Model_DbTable_Plugin._decode(params);
                        })
                    })
                } 
                
                // Construct instance
                return Azf_Plugin_Extension_Manager._constructPlugin(function(type, pluginParams){
                    var className = Azf_Plugin_Extension_Manager.getClassName(type);
                    return new className(function(pluginParams){
                        return Azf_Plugin_Extension_Abstract.__construct(function(pluginParams/*optional*/){
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
    Application_Resolver_ExtensionPlugin.removeExtensionPluginMethod(function(pluginId){
        // Load plugin type
        var type = Azf_Model_DbTable_Plugin.find(pluginId)['type'];
        
        // Remove plugin related resources
        Azf_Plugin_Extension_Manager.tearDown(function(type, pluginId){
            Azf_Plugin_Extension_Abstract = Azf_Plugin_Extension_Manager._getPluginInstance(type, pluginId);
            try{
                Azf_Plugin_Extension_Abstract.tearDown();
            } catch(e){
                    
            }
        });
    
        // Delete plugin
        Azf_Model_DbTable_Plugin.deleteById(function(pluginId){
            Azf_Model_DbTable_Plugin['delete']();
        })
    })
})


azfcms.model.cms.getTemplateRegionsStore(function(navigationId){
    return Application_Resolver_Template.getTemplateRegionsMethod(function(){
        var templateIdentifier = Azf_Model_Tree_Navigation.getDynamicParam(navigationId, 'templateIdentifier');
        return Azf_Template_Descriptor.getRegions(function(templateIdentifier){
            return this.getTemplate(templateIdentifier)['regions'];
        })
    })
})



Azf_Template_Descriptor.getTemplate(function(templateIdentifier){
    var templates = 
    Azf_Template_Descriptor.getTemplates(function(){
        if(Azf_Template_Descriptor._templates==null){
            Azf_Template_Descriptor._initTemplates(function(){
                var path = Azf_Template_Descriptor.getTemplateDirectoryPath(function(){
                    if(Azf_Template_Descriptor._templateDirectoryPath==null){
                        Azf_Template_Descriptor._initTemplateDirectoryPath();
                    }
                    return Azf_Template_Descriptor._templateDirectoryPath;
                });
    
                var potentialTemplateFilePaths = 
                Azf_Template_Descriptor._buildPotentialTemplateFilePaths(function(){
                    var potentialTemplateFilePaths = [];
                    var templateDirectoryPath = Azf_Template_Descriptor.getTemplateDirectoryPath();

                    var iterator = new DirectoryIterator(templateDirectoryPath);
                    foreach(function(iterator, path){
                        if(path.endsWith(".xml")){
                            potentialTemplateFilePaths.push(path);
                        }
                    });

                    return potentialTemplateFilePaths;
                });
    
                var parsedTemplates = 
                Azf_Template_Descriptor._parseTemplateFiles(function(potentialTemplateFilePaths){
                    var schema = Azf_Template_Descriptor.getSchemaSource();
                    var parsedTemplates = [];
        
                    for(var path in potentialTemplateFilePaths){
                        DomDocument.loadXmlFromFile(path);
                        if(DomDocument.schemaValidateFromSource(schema)){
                            parsedTemplates.push(Azf_Template_Descriptor.templateToArray(DomDocument));
                        }
                    }
        
                    return parsedTemplates;
                });
    
                Azf_Template_Descriptor.setTemplates(parsedTemplates);
            })
        }
        else {
            return Azf_Template_Descriptor._templates;
        }
    });
    
    return templates[templateIdentifier];
})


azfcms.model.cms.getRegionPluginsStore(function(nodeId, region){
    return Application_Resolver_ExtensionPlugin.getRegionExtensionPluginsMethod(function(nodeId, region){
        return Azf_Model_DbTable_NavigationPlugin.findAllByNavigationAndRegion(nodeId,region);
    });
})


azfcms.model.cms.enableExtensionPlugin(function(nodeId, pluginId,weight){
    return Application_Resolver_ExtensionPlugin.enableExtensionPluginMethod(function(nodeId, pluginId, weight){
        return Azf_Model_DbTable_NavigationPlugin.bind(nodeId, pluginId, weight);
    })

})


azfcms.model.cms.disableExtensionPlugin(function(nodeId,pluginId){
    return Application_Resolver_ExtensionPlugin.disableExtensionPluginMethod(function(nodeId,pluginId){
        Azf_Model_DbTable_NavigationPlugin.unbind(nodeId,pluginId);
        return true;
        
    });
})

azfcms.model.cms.setExtensionPluginValues(function(navigationId, pluginId, name,description,region,weight,enable){
    return Application_Resolver_ExtensionPlugin.setExtensionPluginValuesMethod(function(navigationId, pluginId, name,description,region,weight,enable){
        if(enabled==false){
            Azf_Model_DbTable_NavigationPlugin.unbind(navigationId, pluginId);
        } else {
            Azf_Model_DbTable_NavigationPlugin.updateWeightByNavigationIdAndPluginId(navigationId, pluginId, weight);
        }
        Azf_Model_DbTable_Plugin.updatePluginValues(pluginId, name,description, region);
        
    })
})

azfcms.model.getExtensionPluginStore(function(){
    return Application_Resolver_PluginDescriptor.getExtensionPlugins();
})


azfcms.controller.ExtensionEditorController.constructor(function(args){
    this.navigationId = args.navigationId;
    
})

azfcms.view.ExtensionEditorPane.constructor(function(args){
    args = {
        regionStore: azfcms.model.cms.getTemplateRegionsStore(navigationId),
        gridStore: azfcms.model.cms.getRegionPluginsStore(nodeId, region),
        typeStore: azfcms.model.cms.getExtensionPluginStore()
    }
    azfcms.view.ExtensionEditorPane.regionStore = args.regionStore;
    azfcms.view.ExtensionEditorPane.typeStore = args.typeStore;
    azfcms.view.ExtensionEditorPane.gridStore = args.pluginsStore;
});

azfcms.view.ExtensionEditorPane.postCreate(function(args){
    azfcms.view.ExtensionEditorPane.pluginGrid.set("store",azfcms.view.ExtensionEditorPane.gridStore);
    azfcms.view.ExtensionEditorPane.typeSelect.set("store",azfcms.view.ExtensionEditorPane.typeStore);
    azfcms.view.ExtensionEditorPane.regionSelect.set("store",azfcms.view.ExtensionEditorPane.regionStore);
   
})

azfcms.view.ExtensionEditorPane.onNew(function(name,description,type,region,weight,enable){
    if(region==false || type == false)return;
    azfcms.controller.ExtensionEditorController.onNew(function(){
        azfcms.view.ExtensionEditorPane.disable();
        var navigationId = azfcms.controller.ExtensionEditorController.navigationId;
        azfcms.model.cms.addExtensionPlugin(navigationId, description, type, region, weight, enable)(
        function(){
            
            azfcms.view.ExtensionEditorPane.reloadGrid(function(){
                azfcms.view.ExtensionEditorPane.pluginGrid.set("store",azfcms.view.ExtensionEditorPane.gridStore)
            });
            azfcms.view.ExtensionEditorPane.enable();
        })
    })
});


azfcms.view.ExtensionEditorPane.onSave(function(navigationId, pluginId, name,description,region,weight,enable){
    azfcms.controller.ExtensionEditorController.onSave(function(pluginId, name,description,region,weight,enable){
        azfcms.view.ExtensionEditorPane.disable();
        azfcms.model.cms.setExtensionPluginValues(navigationId, pluginId, name,description,region,weight,enable)(function(){
            azfcms.view.ExtensionEditorPane.enable();
        })(function(){
            azfcms.view.ExtensionEditorPane.reloadGrid();
            azfcms.view.ExtensionEditorPane.enable();
        })
    })
});

azfcms.view.ExtensionEditorPane.onDelete(function(pluginId){
    azfcms.controller.ExtensionEditorController.onDelete(function(pluginId){
        azfcms.view.ExtensionEditorPane.disable();
        azfcms.model.cms.removeExtensionPlugin(pluginId)(function(){
            azfcms.view.ExtensionEditorPane.reloadGrid();
            azfcms.view.ExtensionEditorPane.enable();
        })
    })
});


azfcms.view.ExtensionEditorPane.onDisable(function(){
    azfcms.controller.ExtensionEditorController.onDisable(function(navigationId, pluginId){
        azfcms.view.ExtensionEditorPane.disable();
        azfcms.model.cms.disableExtensionPlugin(navigationId, pluginId)(function(){
            azfcms.view.ExtensionEditorPane.reloadGrid();
            azfcms.view.ExtensionEditorPane.enable();
        })
    })
})

azfcms.view.ExtensionEditorPane.onEnable(function(navigationId, pluginId, weight){
    azfcms.controller.ExtensionEditorController.onEnable(function(navigationId, pluginId, weight){
        azfcms.view.ExtensionEditorPane.disable();
        azfcms.model.cms.enableExtensionPlugin(navigationId, pluginId, weight)(function(){
            azfcms.view.ExtensionEditorPane.enable();
        })
    })
})



azfcms.view.ExtensionEditorPane.onExtensionEdit(function(pluginId,type){
    azfcms.controller.ExtensionEditorController.onExtensionEdit(function(){
        var modules = azfcms.controller.ExtensionEditorController._buildRequire(type);
        require(modules,function(AbstractExtensionController, AbstractExtensionPane){
            var pane = azfcms.controller.ExtensionEditorController._buildEditorPane(function(AbstractExtensionPane){
                var pane  = new azfcms.view.AbstractExtensionPane._constructor();
                azfcms.view.ExtensionEditorPane.addChild(function(pane){
                    azfcms.view.ExtensionEditorPane.tabContainer.addChild(pane)
                });
                return pane;
            });
            
            var controller = azfcms.controller.ExtensionEditorController._buildController(function(pluginId, ExtensionEditorPane){
                var controller = new azfcms.controller.AbstractExtensionController();
                azfcms.controller.AbstractExtensionController.initializeDependencies(function(pluginId, pane){
                    azfcms.controller.AbstractExtensionController.pluginId = pluginId;
                    azfcms.controller.AbstractExtensionController.editorPane = pane;
                    azfcms.controller.AbstractExtensionController.init();
                })
            });
        })
    })
});

azfcms.view.ExtensionEditorPane.onItemSelect(function(item){
    azfcms.controller.ExtensionEditorController.onItemSelect(function(item){
        azfcms.view.ExtensionEditorPane.disable();
        azfcms.controller.ExtensionEditorController.pluginItem = plugin;
        var form = "form";
        azfcms.view.ExtensionEditorPane.set(function(form,plugin){
            azfcms.view.ExtensionEditorPane._setFormAttr(function(plugin){
                azfcms.view.ExtensionEditorPane.nameText.set("value",plugin.name);
                azfcms.view.ExtensionEditorPane.descriptionText.set("value",plugin.description);
                azfcms.view.ExtensionEditorPane.typeSelect.set("value",plugin.type);
                azfcms.view.ExtensionEditorPane.weightText.set("value",plugin.weight);
                azfcms.view.ExtensionEditorPane.disabledRadio.set("value",plugin.disabledRadio);
            })
        });
        azfcms.view.ExtensionEditorPane.enable();
        azfcms.view.ExtensionEditorPane.typeSelect.set("disabled",true);
    })
})


Azf_Controller_Action_Helper_ExtensionPlugin.postDispatch(function(){
    // Load navigationId
    var navigationId = Zend_Controller_Request_Http.getParam("id");
    // Produce rendered responses
    var responses = Azf_Plugin_Extension_Manager.render(function(navigationId){
        var plugins = Azf_Plugin_Extension_Manager.getPluginDefinitions(function(navigationId){
            return Azf_Model_DbTable_Plugin.findAllByNavigationId(function(navigationId){
                var rows = Zend_Db_Adapter_Abstract.fetchAll("");
                for(var row in rows){
                    row['params'] = Azf_Model_DbTable_Plugin._decode(row['params']);
                }
                return rows;
            });
        });
        
        var responseChunks = {};
        for(var plugin in plugins){
            var Azf_Plugin_Extension_Abstract = Azf_Plugin_Extension_Manager._getPluginInstance(plugin['type'],plugin['id'],plugin['params']);
            ob_start();
            Azf_Plugin_Extension_Abstract.render();
            responseChunks[plugin['region']].push(ob_get_end());
        }
        
        var responses = {};
        foreach(function(responseChunks, region, responseChunk){
            responses[region] = responseChunk.join("");
        });
        return responses;
    });
    
    // Inject responses into response object
    foreach(function(responses,region,body){
        Zend_Controller_Response_Http.setBody("region",body);
    });
    
    
})


