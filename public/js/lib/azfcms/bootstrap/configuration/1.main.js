[
{
    i18nButtonLabelPointer: 'npCreatePageAction',
    iconClass:'dijitIconNewTask',
    init: function(initCallback,adminDialog){
        var context = this;
        require(
            ['azfcms/module/default/configurationPlugin/contentManagment/view/CreatePageDialog','azfcms/module/default/configurationPlugin/contentManagment/controller/CreatePage',
            'azfcms/store/registry!ContentPluginTypeStore'],function
            (CPD,CPC,
                contentPluginTypeStore){
                context.cpd = new CPD({
                    store:contentPluginTypeStore
                });
                context.cpc = new CPC(context.cpd);
                            
                initCallback();
            })
    },
    callback: function(item){
        if(item){
            this.cpc.show(item);
        }
                        
    }
},
{
    i18nButtonLabelPointer: 'npEditPageAction',
    iconClass:'dijitIconEdit',
    init: function(initCallback, adminDialog){
        this.ad = adminDialog;
        var self = this;
        require(['azfcms/module/default/configurationPlugin/contentManagment/view/ContentEdit','azfcms/module/default/configurationPlugin/contentManagment/controller/ContentEdit','azfcms/store/registry!ContentPluginTypeStore'],function
            (cep,cec,contentPluginTypeStore){
                self.CEP = cep;
                self.CEC = cec;
                                
                self.typeStore = contentPluginTypeStore
                self.typeStore.idProperty = "pluginIdentifier";
                self.typeStore.query({}).then(function(){
                    initCallback();
                })
                                
            })
                        
    },
    callback: function(item){
        if(!item){
            return;
        }
        var cep = new this.CEP({
            typeStore:this.typeStore,
            title:"Editor za \""+item.title+"\""
        });
        var cec = new this.CEC();
        cec.init(item,cep);
        this.ad.addChild(cep);
    }
},
{
    i18nButtonLabelPointer: 'npHomeChange',
    iconClass:'dijitIconBookmark',
    init: function(initCallback,adminDialog, item){
        var self = this;
        require(
            ['azfcms/module/default/configurationPlugin/contentManagment/controller/HomePage'],function
            (HPC){
                self.hpc = new HPC();
                initCallback()
            })
    },
    callback: function(item){
        this.hpc.onHomeChange(item);
    }
},
                
{
    i18nButtonLabelPointer: 'npDeletePageAction',
    iconClass:'dijitIconDelete',
    init: function(initCallback,adminDialog){
        var self = this;
        require(
            ['azfcms/module/default/configurationPlugin/contentManagment/view/DeletePageDialog','azfcms/module/default/configurationPlugin/contentManagment/controller/DeletePage',
            'azfcms/model'],function
            (DPD,DPC,
                model){
                var view = new DPD();
                self.controller = new DPC({
                    view:view
                });
                            
                initCallback();
            })
    },
    callback: function(item){
        if(item){
            this.controller.activate(item);
        }
                        
    }
},
{
    i18nButtonLabelPointer: 'npPagePluginsAction',
    iconClass:'dijitIconConfigure',
    init: function(initCallback,adminDialog, item){
        var self = this;
        require(
            ['azfcms/module/default/configurationPlugin/extensionManagment/view/ExtensionEditorPane','azfcms/module/default/configurationPlugin/extensionManagment/controller/ExtensionEditorController',
            'azfcms/store/registry!ExtensionPluginTypeStore','azfcms/store/registry!TemplateRegionsStore','azfcms/store/registry!ExtensionPluginStore'],function
            (EEP,EEC,
                extensionPluginTypeStore,regionStore,extensionPluginStore){
                self.EEP = EEP;
                self.EEC = EEC;
                self.extensionPluginTypeStore = extensionPluginTypeStore;
                self.regionStore = regionStore;
                self.extensionPluginStore = extensionPluginStore;
                self.adminDialog = adminDialog;
                initCallback();
            })
    },
    callback: function(item){
        var view = new this.EEP({
            regionStore:this.regionStore,
            gridStore:this.extensionPluginStore,
            typeStore:this.extensionPluginTypeStore,
            closable:true,
            title:"Dodaci stranice"
        });
        this.adminDialog.addChild(view);
        new this.EEC({
            editorPane:view,
            model:this.extensionPluginStore
        });
                        
    }
},
{
    i18nButtonLabelPointer: 'npPagePluginsStatusAction',
    iconClass:'dijitIconSample',
    init: function(initCallback,adminDialog, item){
        var self = this;
        require(
            ['azfcms/module/default/configurationPlugin/extensionManagment/view/ExtensionPluginStatusPane','azfcms/store/registry!ExtensionPluginStatusStore'],function
            (ExtensionPluginStatusPane, extensionPluginStatusStore){
                self.extensionPluginStatusStore = extensionPluginStatusStore
                self.ExtensionPluginStatusPane = ExtensionPluginStatusPane;
                self.adminDialog = adminDialog;
                initCallback();
            })
    },
    callback: function(item){
        var pane = new this.ExtensionPluginStatusPane({
            gridStore:this.extensionPluginStatusStore,
            style:"width:100%;height:100%;"
        });
        this.adminDialog.addChild(pane);
    }
},
{
    i18nButtonLabelPointer: 'npFilesystemAction',
    iconClass:'dijitIconSample',
    init: function(initCallback,adminDialog, item){
        var self = this;
        require(
            ['azfcms/module/default/configurationPlugin/filesystemManagment/view/FilesystemManagment','azfcms/module/default/configurationPlugin/filesystemManagment/controller/FilesystemManagment',
            'azfcms/store/Filesystem','dojo/store/Observable','dojo/i18n!azfcms/resources/i18n/cms/common/nls/common'],function
            (FP,FPC,FSStore, Observable,
            nls){
                self.adminDialog = adminDialog;
                self.FP = FP
                self.FPC = FPC;
                self.gridStore = new FSStore({
                                    
                    })
                self.treeStore =new FSStore({
                    queryOptions:{
                        file:false
                    },
                    getOptions:{
                        file:false
                    },
                    isTreeModel:true
                });
                self.nls = nls;
                initCallback()
            })
    },
    callback: function(item){
        var fp = new this.FP({
            closable:true,
            title:this.nls.npFilesystemAction,
            gridStore:this.gridStore,
            treeStore:this.treeStore
        });
        var fpc = new this.FPC({
            view:fp
        })
                        
        this.adminDialog.addChild(fp);
        fp.resize();
                        
    }
}
]