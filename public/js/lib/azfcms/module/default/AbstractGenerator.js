define(['dojo/_base/declare','dojo/_base/array','dojo/_base/Deferred',
    'dojo/dom-geometry','dojo/aspect','dojo/dom-style'],
function(declare,array,Deferred,
    domGeometry,aspect,domStyle){
    return declare([],{
        constructor:function(){
            this.hideElement;
            this.name = "";
            this.services = [];
            this._params = null;
            this._initMetadata();
            this._initView();
        },
        _initMetadata:function(){
            throw Error("Overrite me (_initMetadata)");
        },
        _initView:function(){
            throw Error("Overrite me (_initView)");
        },
        getHideElement:function(){
            if(!this.hideElement){
                this.hideElement = document.createElement("div");
                this.hideElement.style.position= 'absolute';
                this.hideElement.style.backgroundColor = '#f7f7f7';
                this.getView().domNode.style.position = 'relative';    
                this.getView().domNode.appendChild(this.hideElement);
                domStyle.set(this.hideElement,'opacity',0.5);
                var self = this;
                aspect.after(this.getView(),"resize",function(response){
                    domGeometry.setMarginBox(self.hideElement,domGeometry.getContentBox(self.getView().domNode));
                    return response;
                })

            }
            return this.hideElement;
        },
        disable:function(){
            this.getHideElement().style.display = 'block';
        },
        enable:function(){
            this.getHideElement().style.display = 'none';
        },
        getName:function(){
          return this.name;  
        },
        getParam:function(name){
            if(this.hasParam(name)){
                return this._params[name];
            } else {
                return null;
            }
        },
        hasParam:function(name){
            return typeof this._params == 'object' &&
                typeof this._params[name] != 'undefined';
        },
        getParams:function(){
            return this._params;
        },
        setParams: function(params){
            this._params = params;
        },
        setParam: function(name,value){
            this._params[name] = value;
        },
        getView:function(){
            return this.view;
        },
        setView:function(view){
            this.view = view;
        },
        hasServiceType:function(service){
            return array.some(this.services,function(element){
                return service==element;
            });
        },
        ucFirst: function(str){
            return str.substring(0,1).toUpperCase()+str.substring(1);
        },
        invoke:function(serviceType, params){
            this.setParams(params);
            if(!this.hasServiceType(serviceType)){
                return false;
            } else {
                var promise = new Deferred();
                var methodName = "build"+this.ucFirst(serviceType);
                if(typeof this[methodName] != 'function'){
                    throw new Error("Method "+methodName+" does not exists");
                }
                this[methodName](promise);
                
                return promise;
            }
        }
    })
})