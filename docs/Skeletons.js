/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */


azfcms.model = {
    createCall:function(method,arguments){}, //TODO
    invokeWithForm:function(call,form){
        return dojo.Deferred()
    }
};

Azf_Model_DbTable_NavigationPlugin = {
    bind: function(nodeId,pluginId,weight){},
    unbind: function(nodeId,pluginId){},
    findAllByNavigationAndRegion:function(nodeId,region){},
    updateWeightByNavigationIdAndPluginId: function(navigationId, pluginId, weight){}
}

Azf_Model_DbTable_Plugin = {
    insertPlugin:function(name,description,type,region,params){},
    getPluginParams:function(pluginId){},
    setPluginParams:function(pluginId,params){},
    findAllByNavigationId: function(navigationId){},
    $_decode: function(params){},
    deleteById:function(id){},
    find:function(id){}
}

Azf_Controller_Action_Helper_ExtendedPlugin = {
    postDispatch: function(){}
}


Template = {
    name:"",
    description:"",
    identifier:"",
    regions:[
    {
        name:"",
        identifier:""
    }
    ]
}


Azf_Template_Descriptor = {
    $_templates: null,
    $_templateDirectoryPath: null,
    $_classPath:null,
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
    $_initTemplateDirectoryPath: function(){},
    $_initTemplates: function(){},
    $_buildPotentialTemplateFilePaths: function(){},
    $_parseTemplateFiles:function(templateFilePaths){}
}

Azf_Model_Tree_Navigation = {
    getDynamicParam: function(id,param){},
    getStaticParam: function(id,param){},
    setHome:function(navigationId){},
    findByHome:function(){}
}

azfcms.controller.navigation.HomePage = {
    onHomeChange:function(navigationItem){}
}


azfcms.model.navigation = {
    constructor: function(args){},
    getRoot: function(onComplete, onError){},
    mayHaveChildren: function(item){},
    getChildren: function(parentItem, onComplete){},
    isItem: function(something){},
    getIdentity: function(item){},
    getLabel: function(item){},
    pasteItem: function(childItem, oldParentItem, newParentItem, bCopy, index){},
    $_removeChildFromParentItem: function(childItem, parentItem){},
    $_getBeforeChildId: function(newParentItem,index){},
    $_getAfterChildId: function(newParentItem, index){},
    getNodeParams: function(nodeId){},
    setStaticParams: function(nodeId, params){},
    setDynamicParams: function(nodeId, params){},
    $_constructSetStatic: function(nodeId,params){},
    $_constructSetDynamic: function(nodeId,params){},
    setParams: function(nodeId, staticParams, dynamicParams){},
    moveBefore:function(node, before){},
    moveAfter: function(node, after){},
    moveInto: function(node, into){},
    insertInto: function(id,title,type){},
    remove: function(item){},
    setTitle: function(node,title){},
    setUrl: function(node,url){},
    setMetaValues: function(node, title, description, keywords){},
    setContent: function(id, key, content){},
    getContent: function(id,key){},
    onChange: function(item){},
    onChildrenChange: function(parent, newChildrenList){}
}

Azf_Plugin_Extension_Manager = {
    $_navigationModel:null,
    setUp:function(type,pluginId){},
    tearDown:function(type,pluginId){},
    render:function(navigationId){},
    $_getPluginInstance: function(type, pluginId, pluginParams/*optional*/){},
    getPluginParams: function(pluginId){},
    getPluginDefinitions: function(navigationId){},
    getModel: function(){},
    getNavigationModel:function(){},
    getClassName: function(type){},
    $_constructPlugin: function(type,pluginParams){},
    setValue:function(pluginId,key,value){},
    setValues:function(pluginId,values){},
    getValue:function(pluginId,key){},
    getValues:function(pluginId){}
    
}

Azf_Plugin_Extension_Abstract = {
    $_id:null,
    $_params:{},
    $_isParamsDirty:false,
    setup: function(){},
    teardown: function(){},
    $__construct: function(params){},
    setId:function(id){},
    getId:function(){},
    setParams: function(params){},
    setParam:function(key,value){},
    getParams:function(){},
    getParams:function(keys){},
    isParamsDirty:function(){},
    clearParamsDirty:function(){},
    setValue:function(key,value){},
    setValues:function(values){},
    getValue:function(key){},
    getValues:function(){}
}


Application_Resolver_Template = {
    getTemplateRegionsMethod: function(){}

}

Application_Resolver_ExtensionPlugin = {
    addExtensionPluginMethod: function(navigationId,name,description,type,region,weight,enable){},
    removeExtensionPluginMethod: function(type,pluginId){},
    enableExtensionPluginMethod:function(nodeId, pluginId, weight){},
    disableExtensinPluginMethod:function(nodeId,pluginId){},
    getRegionExtensionPluginsMethod: function(nodeId, region){},
    findPluginsByNavigationAndRegionMethod:function(navigationId,region){},
    getNavigationPluginModel:function(){},
    setExtensionValueMethod:function(id, key, value){},
    setExtensionValuesMethod:function(id, values){},
    getExtensionValueMethod:function(key,value){},
    getExtensionValuesMethod:function(){}
    
}

Application_Resolver_PluginDescriptor = {
    getContentPlugins: function(){},
    getExtensionPlugins: function(){}
}

var azfcms = {
    controller:{},
    view: {},
    resources:{}
}


azfcms.controller.ExtendedEditorController = {
    /**
     * @property {azfcms.view.ExtendedEditorPane}
     */
    editorPane: azfcms.view.ExtendedEditorPane,
    /**
     * @property {azfcms.model.cms}
     */
    model: azfcms.model.cms,
    pluginItem:null,
    
    /**
     * @property {Number}
     */
    navigationId:null,
    
    $_buildRequire: function(type){},
    $_buildController: function(pluginId, extendedEditorPane){},
    onNew: function(name,description,type,region,weight,enable){},
    onSave: function(pluginId, name,description,region,weight,enable){},
    onDelete:function(item){},
    onDisable: function(navigationId, pluginId){},
    onEnable: function(navigationId, pluginId, weight){},
    onExtendedEdit: function(pluginId,type){},
    onItemSelect: function(item){}
}


azfcms.view.ExtensionEditorPane = {
    tabContainer:null,
    regionSelect:null,
    pluginGrid:null,
    nameText:null,
    descriptionText:null,
    typeSelect:null,
    formRegionSelect:null,
    weightText:null,
    disableCheckBox:null,
    saveButton:null,
    customEditButton:null,
    removeButton:null,
    addButton:null,
    regionStore:null,
    typeStore:null,
    gridStore:null,
    regionSelectionNode:null,
    formNode:null,
    constructor:function(args){},
    postCreate: function(args){},
    resize: function(){},
    getDomNodeBox: function(){},
    getTabContainerBox: function(){},
    setChildHeights: function(regionSelectionSize,gridSize,formSize){},
    disable:function(){},
    enable:function(){},
    addChild: function(pane){},
    reloadGrid: function(){},
    $_setFormAttr: function(plugin){},
    onNew: function(name,description,type,region,weight,enable){},
    onSave: function(id, name,description,region,weight,enable){},
    onDelete: function(pluginId){},
    onDisable: function(pluginId){},
    onEnable: function(pluginId, weight){},
    onExtendedEdit: function(pluginId,type){},
    onItemSelect: function(item){},
    onRegionSelect:function(region){},
    resetForm:function(){}
}

azfcms.controller.AbstractExtendedController = {
    editorPane:null,
    pluginId:null,
    initializeDependencies: function(pluginId, editorPane){},
    init: function(){}
}

azfcms.view.AbstractExtensionPane = {
    
    }


azfcms.model.cms = {
    addExtensionPlugin: function(navigationId, name,description,type,region,weight,enable){},
    getRegionPluginsStore:function(nodeId, region){},
    getTemplateRegionsStore:function(templateIdentifier){},
    getTemplateRegionsForNavigation:function(navigationId){},
    getExtensionPluginStore: function(){},
    setExtensionPluginValues: function(navigationId,pluginId,name,description,region,weight,enable){},
    removeExtensionPlugin:function(pluginId){},
    disableExtensionPlugin:function(nodeId,pluginId){},
    enableExtensionPlugin:function(nodeId, pluginId,weight){},
    findPluginsByNavigationAndRegion:function(navigationId,region){},
    getFilesystemStore:function(directory,filter){},
    getFilesystemDirectoryTreeStore:function(directory /** Optional*/){}, //TODO 
    uploadFilesMethod:function(directory){},//TODO
    deleteFiles:function(files){
        files==[JsFile]
    },
    isFileArray:function(file, requiredKeys){},
    setHomePage:function(nodeId){},
    setExtensionValue:function(id, key, value){},
    setExtensionValues:function(id, values){},
    getExtensionValue:function(id, key){},
    getExtensionValues:function(id){}
}

Zend_Registry = {
    /**
     * Default template registry key
     */
    defaultTemplate:"",
    /**
     * @type {Azf_Model_Tree_Navigation}
     */
    navigationModel:"",
    /**
     * @type {Zend_Acl}
     */
    acl:null,
    get:function(key){}
}

var application={};
application.ini = {
    /**
     * Default template identifier
     */
    defaultTemplate:""
}


// IMPLEMENT
azfcms.view.FilesystemPane = {
    treeStore:null,
    gridStore:null,
    toolbar:null,
    tree:null,
    grid:null,
    postCreate:function(){},
    addAction:function(name,callback, dijitIconClass){},
    $_createButton:function(name,callback,dijitIconClass){},
    enable:function(){},
    disable:function(){},
    reloadGrid:function(item){},//TODO
    reloadTree:function(){},//TODO
    reload:function(){},//TODO
    getGridSelection:function(){
        return [
        JsFile
        ]
    },
    getTreeSelection:function(){
        return JsFile|null
    },
    onTreeSelect:function(item){
        item==JsFile||null
    }
}



// IMPLEMENT    
azfcms.controller.FilesystemPaneController = {
    uploadDialog:null,
    uploadPane:"",
    constructor:function(){},
    attachEventHandlers:function(){},
    onUpload:function(selectedTreeItem,selectedGridItems){
        selectedGridItems = selectedTreeItem = JsFile;
    },
    getUploadDialog:function(){},//TODO
    doUpload:function(form){},
    onDelete:function(selectedTreeItem,selectedGridItems){},
    onTreeSelect:function(item){}

}

// IMPLEMENT
Application_Resolver_Filesystem = {
    $_baseDir:null,
    setBaseDir:function(path){},
    getBaseDir:function(){},
    constructRealPath:function(path){
        path==String|JsFile;
        return String|null;
    },
    normalizeFilter:function(filter){
        return JsFileFilter;
    },
    getDirectoryIterator:function(path){},
    getDirectoryFileList:function(directory,filter){},
    getFileListMethod:function(directory){
        return {
            directory:"",
            files:[JsFile]
        }
    },
    getFilesystemDirectoryTreeMethod:function(directory,filter){
        return [JsFile]
    },
    isPathSecure:function(path){
        return Boolean;
    },
    uploadFiles:function(dirname){},
    $_isUploadedFile:function(file){},
    $_moveUploadedFile:function(source,destination){},
    deleteFilesMethod:function(files){
        files==[JsFile]
    },
    deleteFile:function(file){
        file == JsFile
    },
    createDirectoryMethod:function(directory){
        directory==String
    }
}



JsFile = {
    dirname:"",
    name:"",
    date:"",
    type:"",
    size:"",
    permissions:""
}

JsFileFilter = {
    directory:true,
    file:true,
    hidden:true
}



// IMPLEMENT
azfcms.model.QueryLangStore = {
    idProperty:"",
    queryMethod:"",
    queryOptions:{},
    addMethod:"",
    addOptions:{},
    getMethod:"",
    getOptions:{},
    putMethod:"",
    putOptions:{},
    removeMethod:"",
    removeOptions:{},
    model:null,
    constructor:function(args){},
    getIdentity:function(){},
    query:function(query,options){},
    add:function(object){},
    get:function(id){},
    put:function(object,options){},
    remove:function(id){}

}

// IMPLEMENT
azfcms.model.QueryLangStoreQuery = {
    /**
     * Simple dictionary object that will be introspected by the
     * server side.
     */
    }


// IMPLEMENT
azfcms.model.store.Filesystem = declare([azfcms.model.QueryLangStore]) = {
    queryMethod:"",
    queryOptions:{},
    getMethod:"",
    getOptions:{}
}


azfcms.resources = {
    nls:{}
}

azfcms.resources.nls = {
    // FSP = FilesystemPane
    fspUploadAction :"",
    fspNoFileSelected:"",
    fspDeleteSelectionMessage:"",
    
    
    // UPL = UploadPane
    uplMessage:"",
    uplCancelLabel:"",
    uplUploadLabel:"",
    
    // cop = Confirm Pane
    copReject:"",
    copAccept:""
}

// CONTINUE
azfcms.view.UploadPanel = {
    constructor:function(args){},
    uplMessage:"",
    cancelLabel:"",
    uploadLabel:"",
    uploadButton:null,
    cancelButton:null,
    file1Input:null,
    file2Input:null,
    file3Input:null,
    form:null,
    enable:function(){},
    disable:function(){},
    $_onUpload:function(){},
    $_onCancel:function(){},
    onUpload:function(form){},
    onCancel:function(){},
    reset:function(){},
    getForm:function(){}
}

azfcms.view.ConfirmPane = {
    rejectLabel:"",
    acceptLabel:"",
    message:"",
    acceptButton:null,
    rejectButton:null,
    callback:null,
    constructor:function(args){},
    confirm:function(callback,message,acceptLabel/*optional*/,rejectLabel/*optional*/){},
    $_onReject:function(){},
    $_onAccept:function(){}
}


azfcms.view.Util = {
    $_confirmDialog:null,
    $_confirmPane:null,
    alert:function(message){},
    getConfirmPane:function(){},
    getConfirmDialog:function(){},
    confirm:function(callback,message,acceptLabel,rejectLabel){}
}



Application_Resolver_Navigation = {
    overrideSetHomePage:function(navigationid){},
    changePageTypeMethod:function(nodeId, newType){}
}


azfcms.controller.navigation.ContentEdit = {
    cep:null,
    staticParams:null,
    dynamicParams:null,
    node:null,
    navigationModel:null,
    constructor: function(args){},
    init: function(node, cep){},
    $_initListeners: function(){},
    $_build: function(staticParams, dynamicParams){},
    $_buildEditorPane: function(EditorPane,cep){},
    $_buildController: function(EditorController, nodeId, ep){},
    $_onParamsLoad: function(){},
    onMetadataSave: function(title, description, keywords){},
    onTypeChange:function(newType){}
}

azfcms.view.navigation.ContentEdit = {
    templateString:templateString,
    closable:true,
    borderContainer:null,
    pageType:null,
    spremi:null,
    pageName:null,
    pageDescription:null,
    pageKeywords:null,
    constructor: function(args){},
    postCreate: function(){},   
    resize:function(){},
    $_save: function(){},
    $_onChangeType:function(){},
    addChild: function(child){},
    $_setTitleAttr: function(title){},
    $_setDescriptionAttr: function(description){},
    $_setKeywordsAttr: function(keywords){},
    onMetadataSave: function(title, description, keywords){},
    onTypeChange:function(type){}
}


identity = {
    id:0,
    loginName:"",
    firstName:"",
    lastName:"",
    email:null,
    cTime:null,
    rTime:null,
    verified:null
}

Zend_Acl = {
    
}

Zend_Acl.system = {
    rootAccess:true
}
