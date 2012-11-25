define(
['dojo/_base/declare',
    'dijit/_Widget','dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin',
    'dojo/text!./templates/Fs.html','dojo/i18n!./resource/i18n/nls/Fs',
    'dojo/_base/lang','azfcms/module/default/view/util',
    'dijit/form/Button',
    'azfcms/module/default/store/FSStore',
    'dijit/Tree',
    'dojo/data/ObjectStore',
    'dojo/Deferred',
    'azfcms/module/default/GeneratorInvoker',
    'dojo/_base/array',
    './view/FilePane',
    './view/UploadPane',
    'dojo/topic',
    'dojo/date/locale',
    
    'dijit/layout/TabContainer','dijit/layout/BorderContainer','dijit/Toolbar',
    'dijit/layout/ContentPane',
    'dojox/grid/DataGrid'
],function
(declare, 
_Widget, _TemplatedMixin, _WidgetsInTemplate, templateString,
    nls,lang, util,Button, FSStore,Tree, ObjectStore, Deferred, Generator,array, FilePane,
    UploadPane, topic, locale)
{
    var _class = declare([_Widget,_TemplatedMixin,_WidgetsInTemplate],{
        constructor:function(params){
            
            this.model = params.model;
            /**
             * Toolbar reference
             */
            this.toolbar;
            /**
             * Tab container ref.
             */
            this.tabContainer;
            
            this.title = nls.tabTitle;
            
            
            this.init();
        },
        // This property represents a template string which will be used to 
        // dynamicall construct user interface elements
        templateString: templateString,
        closable:true,
        title:nls.tabName,
        nls:nls,
        init:function(){
            var FSPane = this;
            this.model.on('anyChange',function(){
                FSPane.reloadGrid();
            })
        },
        
        postCreate:function(){
            this.inherited(arguments);
            this._createTree();
            this._createFsGrid();
        },
        
        _createTree:function(){
           this.tree.on("click",lang.hitch(this,this.handleTreeSelect));
        },
        
        _createFsGrid :function(){
            this.grid.layout.cells[1].formatter = this.formatGridSize;
            this.grid.layout.cells[2].formatter = this.formatGridDate;
            
            var FSPane = this;
            this.model.getRoot().then(function(response){
                FSPane.grid.setStore(new ObjectStore({objectStore:FSPane.model}), response.data)
            })
        },
        
        getTreeSelection:function(){
            var selections = this.tree.selectedItems;
            if(selections && selections.length>0){
                return selections[0];
            } else {
                return false;
            }
        },
        getGridSelection:function(){
            var selection = this.grid.selection.getSelected();
            if(selection.length>0){
                return selection[0];
            } else {
                return false;
            }
        },
        
        clearGridSelection:function(){
            this.grid.selection.clear();
        },
        
        handleTreeSelect:function(item){
            this.grid.setQuery(item);
        },
        
        resize:function(){
            this.inherited(arguments);
            
        },
        
        addChild:function(child){
            this.tabContainer.addChild(child);
            child.resize();
        },
        
        removeChild: function(child){
            this.tabContainer.removeChild(child);
        },
        
        selectChild: function(child){
            this.tabContainer.selectChild(child);
        },
        
        addToolbarChild:function(child){
            this.toolbar.addChild(child);
        },
        
        removeToolbarChild:function(child){
            this.toolbar.removeChild(child);
        },
        
        reloadGrid:function(){
            var query = this.getTreeSelection();
            if(!query)
                return;
            this.grid.setQuery(query);
        },
        
        alert:function(msg){
            alert(msg);
        },
        
        prompt:function(msg,input){
            var d = new Deferred();
            d.resolve(prompt(msg,input?input:''));
            return d;
        },
        
        confirm:function(msg){
            var d = new Deferred();
            d.resolve(window.confirm(msg));
            return d;
        },
        
        formatGridSize:function(size){
            if(size < Math.pow(2,10)){
                return size+"B";
            } else if(size < Math.pow(2,20)){
                return Math.round(size/Math.pow(2,10))+"KB"
            } else if(size < Math.pow(2,30)){
                return Math.round(size/Math.pow(2,20))+"MB"
            } else {
                return Math.round(size/Math.pow(2,30))+"GB"
            }
        },
        
        formatGridDate:function(date){
            return locale.format(new Date(date*1000),{
                locale:"hr-hr",formatLength:'short'
            });
        },
        
        doDirectoryCreate:function(){
            var selection = this.getTreeSelection();
            if(!selection){
                this.alert("Prvo izaberite ciljani direktorij u lijevom stablu");
                return;
            }
            
            var FSPane = this;
            this.prompt("Unesite naziv direktorija").then(function(input){
                if(!input)
                    return;
                selection.content = false;
                FSPane.model.add(input, selection)
            })
            
            this.clearGridSelection();
        },
        
        doClone:function(){
            var selection = this.getGridSelection();
            if(!selection){
                this.alert("Prvo morate izabrati direktorij koji \u017eelite kopirati");
                return;
            }
            
            var FSPane = this;
            Generator.invoke(function(dstObj){
                FSPane.model.cloneDirectory(selection,dstObj);
                FSPane.clearGridSelection();
            },'FSObject');
        },
        
        doDelete:function(){
            var selection = this.getGridSelection();
            if(!selection){
                this.alert("Prvo morate izabrati dadoteku ili direktorij");
                return;
            }
            
            var FSPane = this;
            this.confirm('Želite li izbrisati izabrane dadoteke?').
                then(function(response){
                if(response){
                    FSPane.model.remove(selection).then(function(){
                        FSPane.clearGridSelection();
                    })
                }
            })
        },
        
        doRename:function(){
            var selection = this.getGridSelection();
            if(!selection){
                return;
            }
            
            var FSPane = this;
            this.prompt("Unesite novi naziv",selection.name)
            .then(function(newName){
                if(!newName)
                    return;
                FSPane.model.rename(selection, newName);
            })
        },
        
        doFileCreate:function(){
            var selection = this.getTreeSelection();
            if(!selection){
                return;
            }
            
            var FSPane = this;
            var pane = new FilePane({
                path:selection,
                onCreate:function(pane, value){
                    FSPane.model.add(value.name, {
                        path:value.path,
                        content:value.content
                    }).then(function(response){
                        if(response.status==FSPane.model.STATUS_OK){
                            FSPane.removeChild(pane);
                            pane.destroyRecursive();
                        } else{
                            throw new Error(response);
                        }
                    })
                }
            })
            this.addChild(pane);
        },
        
        doFileEdit:function(){
            var selection = this.getGridSelection();
            if(!selection || selection.isDir){
                return;
            }
            var FSPane = this;
            
            this.model.getFileContents(selection)
            .then(function(response){
                if(response.status==FSPane.model.STATUS_OK){
                    FSPane.addChild(new FilePane({
                        path:selection,
                        value:{
                            content:response.data,
                            name:selection.name
                        },
                        title:'Dadoteka: \''+selection.name+'\'',
                        onSave:function(pane, value){
                            FSPane.model.put(selection,value.content)
                            .then(function(response){
                                if(response.status == FSPane.model.STATUS_OK){
                                    FSPane.removeChild(pane);
                                } else{
                                    throw new Error(response);
                                }
                            })
                        }
                    }))
                } else{
                    throw new Error(response);
                }
            })
        },
        
        doMove:function(){
            var from = this.getGridSelection();
            if(!from){
                return;
            }
            var FSPane = this;
            
            Generator.invoke(function(to){
                if(!to.isDir){
                    return;
                }
                
                FSPane.model.move(from,to)
            },'FSObject');
        },
        
        doUpload:function(){
            var selection = this.getTreeSelection();
            if(!selection)
                return;
            
            var FSPane = this;
            var uploadPane = new UploadPane({
                path:selection,
                model:this.model,
                onUploadComplete:function(pane, uploadDstPath){
                    FSPane.removeChild(pane);
                    pane.destroyRecursive(pane);
                    
                    topic.publish('azfcms/FSStore:refreshChildren',uploadDstPath);
                }
            });
            
            this.addChild(uploadPane);
        }
            
    });
    
    return _class;
})