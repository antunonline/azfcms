/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


Azf_Model_DbTable_NavigationPlugin = {
    bind: function(nodeId,pluginId,weight){},
    unbind: function(nodeId,pluginId){},
    findAllByNavigationAndRegion:function(nodeId,region){},
    updateWeightByNavigationIdAndPluginId: function(navigationId, pluginId, weight){}
}

Azf_Model_DbTable_Plugin = {
    insertPlugin:function(name,description,type,region,params){},
    getPluginParams:function(pluginId){},
    findAllByNavigationId: function(navigationId){},
    _decode: function(params){},
    deleteById:function(id){}
}

Azf_Controller_Action_Helper_ExtendedPlugin = {
    postDispatch: function(){}
}


Template = {
    name:"",
    description:"",
    identifier:"",
    regions:[
        {name:"",identifier:""}
    ]
}


Azf_Template_Descriptor = {
    _templates: null,
    _templateDirectoryPath: null,
    _classPath:null,
    setTemplates: function(templates){},
    getTemplates: function(){},
    getTemplate:function(templateIdentifier){},
    getRegions:function(templateIdentifier){},
    getTemplateDirectoryPath: function(){},
    setTemplateDirectoryPath: function(path){},
    getClassPath: function(){},
    setClassPath:function(path){},
    getSchemaSource: function(){},
    templateToArray: function(DomDocument){},
    _initTemplateDirectoryPath: function(){},
    _initTemplates: function(){},
    _buildPotentialTemplateFilePaths: function(){},
    _parseTemplateFiles:function(templateFilePaths){}
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
    render:function(navigationId){},
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
    getTemplateRegionsMethod: function(){}
    
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
    pluginItem:null,
    
    _buildRequire: function(type){},
    _buildController: function(pluginId, extendedEditorPane){},
    onNew: function(name,description,type,region,weight,enable){},
    onSave: function(pluginId, name,description,region,weight,enable){},
    onDelete:function(item){},
    onDisable: function(navigationId, pluginId){},
    onEnable: function(navigationId, pluginId, weight){},
    onExtendedEdit: function(pluginId,type){},
    onItemSelect: function(item){}
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
    _setFormAttr: function(plugin){},
    onNew: function(name,description,type,region,weight,enable){},
    onSave: function(pluginId, name,description,region,weight,enable){},
    onDelete: function(pluginId){},
    onDisable: function(navigationId, pluginId){},
    onEnable: function(navigationId, pluginId, weight){},
    onExtendedEdit: function(pluginId,type){},
    onItemSelect: function(item){}
}

azfcms.controller.AbstractExtendedController = {
    editorPane:null,
    pluginId:null,
    initializeDependencies: function(pluginId, editorPane){},
    init: function(){}
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