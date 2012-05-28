/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Application_Model_DbTable_NavigationPlugin = {
    bind: function(nodeId,pluginId,weight){},
    unbind: function(nodeId,pluginId){},
    findAllByNavigationAndRegion:function(nodeId,region){},
    updateWeightByNavigationIdAndPluginId: function(navigationId, pluginId, weight){}
}

Application_Model_DbTable_Plugin = {
    insertPlugin:function(name,description,type,region,params){},
    getPluginParams:function(pluginId){},
    findAllByNavigationId: function(navigationId){},
    _decode: function(params){},
    deleteById:function(id){}
}

Azf_Controller_Action_Helper_ExtendedPlugin = {
    
}

Azf_Plugin_Extension_Manager = {
    
}

Azf_Template_Descriptor = {
    getRegions:function(templateIdentifier){},
    getTemplate:function(templateIdentifier){}
}

Azf_Model_Tree_Navigation = {
    getDynamicParam: function(id,param){}
}

Application_Plugin_Extension_Abstract = {
    setUp:function(){},
    tearDown:function(){}
}

Azf_Plugin_Extension_Manager = {
    setUp:function(type,pluginId){},
    tearDown:function(type,pluginId){},
    render:function(navigationId,response){},
    _getPluginInstance: function(type, pluginId, pluginParams/*optional*/){},
    getPluginParams: function(pluginId){},
    getPluginDefinitions: function(navigationId){},
    getModel: function(){},
    getClassName: function(type){},
    _constructPlugin: function(type,pluginParams){}
}

Azf_Plugin_Extension_Abstract = {
    _params:{},
    setup: function(){},
    teardown: function(){},
    __construct: function(params){},
    setParams: function(params){}
}


Application_Resolver_Template = {
    getTemplateRegions: function(){}
    
}

Application_Resolver_ExtensionPlugin = {
    addExtensionPlugin: function(name,description,type,region,weight,enable){},
    removeExtensionPlugin: function(type,pluginId){},
    enableExtensionPlugin:function(nodeId, pluginId, weight){},
    disableExtensinPlugin:function(nodeId,pluginId){},
    getRegionExtendedPluginsStore: function(nodeId, region){}
}

Application_Resolver_PluginDescriptor = {
    getContentPlugins: function(){},
    getExtensionPlugins: function(){}
}

var azfcms = {
    controller:{},
    view: {}
}


azfcms.controller.ExtendedEditController = {
    /**
     * @property {azfcms.view.ExtendedEditorPane}
     */
    editorPane: azfcms.view.ExtendedEditorPane,
    /**
     * @property {azfcms.model.cms}
     */
    model: azfcms.model.cms,
    _buildRequire: function(type){},
    _buildController: function(pluginId, extendedEditorPane){},
    onNew: function(name,description,type,region,weight,enable){},
    onSave: function(pluginId, name,description,region,weight,enable){},
    onDelete:function(item){},
    onDelete: function(pluginId){},
    onDisable: function(navigationId, pluginId){},
    onEnable: function(navigationId, pluginId, weight){},
    onExtendedEdit: function(pluginId,type){}
}


azfcms.view.ExtendedEditorPane = {
    tabContainer:null,
    regionSelect:null,
    pluginGrid:null,
    nameText:null,
    descriptionText:null,
    typeSelect:null,
    weightText:null,
    disabledRadio:null,
    saveButton:null,
    customEditButton:null,
    removeButton:null,
    addButton:null,
    regionStore:null,
    typeStore:null,
    gridStore:null,
    constructor:function(args){},
    postCreate: function(args){},
    disable:function(){},
    enable:function(){},
    addChild: function(pane){},
    reloadGrid: function(){},
    onNew: function(name,description,type,region,weight,enable){},
    onSave: function(pluginId, name,description,region,weight,enable){},
    onDelete: function(pluginId){},
    onDisable: function(navigationId, pluginId){},
    onEnable: function(navigationId, pluginId, weight){},
    onExtendedEdit: function(pluginId,type){}
}

azfcms.controller.AbstractExtendedController = {
    
}

azfcms.view.AbstractExtendedPane = {
    
}


azfcms.model.cms = {
    getRegionPluginsStore:function(nodeId, region){},
    getTemplateRegionsStore:function(navigationId){},
    getExtensionPluginStore: function(){},
    addExtensionPlugin:function(name,description,type,region,weight,enable){},
    setExtensionPluginValues: function(navigationId, pluginId, name,description, weight){},
    removeExtensionPlugin:function(pluginId){},
    disableExtensionPlugin:function(nodeId,pluginId){},
    enableExtensionPlugin:function(nodeId, pluginId,weight){}
}