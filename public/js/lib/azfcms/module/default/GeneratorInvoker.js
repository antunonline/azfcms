/**
 * @author Antun Horvat
 */

define(['dojo/_base/declare', 'dojo/_base/array','azfcms/module/default/view/GeneratorInvokerPane','azfcms/model/cms',
'dojo/_base/Deferred','dojo/topic'],
function(declare, array, GeneratorInvokerPane, adminModel,
    Deferred, topic){
    var _class = declare([],{
        constructor: function(args){
            this._services = {};
            this.model = args.model;
        },
        setServiceType:function(serviceType){
            this._serviceType = serviceType;
        },
        getServiceType:function(){
            return this._serviceType
        },
        setCallback:function(callback){
            this._callback = callback;
        }, 
        getCallback:function(){
            return this._callback;
        },
        setServiceName:function(serviceName){
            this._serviceName = serviceName;
        },
        getServiceName:function(){
            return this._serviceName;
        },
        setActiveGenerator:function(AbstractGenerator){
            this._activeService = AbstractGenerator;
        }, 
        getActiveGenerator:function(){
            return this._activeService;
        },
        addService:function(AbstractGenerator){
            this._services[AbstractGenerator.getName()] = AbstractGenerator;
        },
        getServices:function(){
            return this._services;
        },
        setServiceParams:function(params){
            this._serviceParams = params;
        },
        getServiceParams: function(){
            return this._serviceParams;
        },
        setActiveGeneratorParams:function(params){
            var AbstractGenerator = this.getActiveGenerator();
            if(AbstractGenerator){
                AbstractGenerator.setParams(params);
            }
        },
        getActiveGeneratorParams:function(){
            return this.getActiveGenerator().getParams();
        },
        _buildView:function(){
            var GeneratorInvoker = this;
            this.view = new GeneratorInvokerPane({
                onAccept:function(){
                    GeneratorInvoker.doAccept();
                },
                onChildSelect:function(child){
                        // may be simply ui widget, connected to AbstractGenerator
                        GeneratorInvoker.setActiveGenerator(child);
                }
            });
        },
        
        _init:function(){
            var GeneratorInvoker = this;
            this._buildView();
            this.model.getGeneratorAmdPaths().then(function(requirePaths){
                require(requirePaths,function(){
                    array.forEach(arguments, function(_class, index){
                        var AbstractGenerator = new _class();
                        var GeneratorInvokerPane = GeneratorInvoker.getView();
                        GeneratorInvokerPane.addChild(AbstractGenerator.getView());
                        GeneratorInvoker.addService(AbstractGenerator);
                        if(index== 0) GeneratorInvoker.setActiveGenerator(AbstractGenerator);
                    })
                    
                  
                  if(GeneratorInvoker.getServiceType()){
                    GeneratorInvoker.filterServicesByType(GeneratorInvoker.getServiceType());
                    }

                    if(GeneratorInvoker.getServiceName()){
                        GeneratorInvoker.focusService(GeneratorInvoker.getServiceName());
                        if(GeneratorInvoker.getServiceParams()){
                            GeneratorInvoker.setActiveGeneratorParams(GeneratorInvoker.getServiceParams());
                        }
                    }

                    GeneratorInvoker.getView().show();
                    })
            })
        },
        
        filterServicesByType:function(serviceType){
            var services = this.getServices();
            var AbstractGenerator;
            for(var name in services){
                AbstractGenerator = services[name];
                if(AbstractGenerator.hasServiceType(serviceType)){
                    AbstractGenerator.enable();
                } else {
                    AbstractGenerator.disable();
                }
            }
        },
        
        focusService:function(serviceName){
            var AbstractGenerator = this.getService(serviceName);
            this.view.selectChild(AbstractGenerator.getView());
            this.setActiveGenerator(AbstractGenerator);
        },
        getView:function(){
            if(!this.view){
                this._init();
                return false;
            } 
            return this.view;
        },
        show:function(){
            var GeneratorInvokerPane = this.getView();
            if(!GeneratorInvokerPane){
                return false;
            }
            
            GeneratorInvokerPane.show();
            return true;
        },
        invoke:function(callback, serviceType, serviceName, serviceParams){
            this.setCallback(callback);
            this.setServiceType(serviceType);
            
            if(serviceName){
                this.setServiceName(serviceName);
                if(serviceParams){
                    this.setServiceParams(serviceParams);
                }
            }
            
            if(serviceParams){
                this.setServiceParams(serviceParams);
            }
            
            var isShown = this.show();
            if(!isShown){
                return;
            }
            
            this.filterServicesByType(serviceType);
            if(serviceName){
                this.focusService(serviceName);
                if(serviceParams){
                    this.setActiveGeneratorParams(serviceParams);
                }
            }
        },
        
        doAccept:function(){
            var GeneratorInvoker = this;
            var AbstractGenerator = this.getActiveGenerator();
            if(AbstractGenerator.hasServiceType(this.getServiceType())){
                var response = AbstractGenerator.invoke(this.getServiceType(),this.getServiceParams());
                
                if(!response){
                    return;
                } else {
                    response.then(function(response){
                        GeneratorInvoker.getCallback()(response);
                        GeneratorInvoker.getView().hide();
                    },function(e){
                        alert(e);
                    })
                }
            }
        }
    });
    
    var service = new _class({
        model:adminModel
    });
    return service;
})


