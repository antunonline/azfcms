/**
 * @author Antun Horvat
 */


var azfcms = {
    init: function(){
        
    },
    model : {
        preparedRpcs: {},
        preparedRests: {},
        preparedRestStores:{},
        _getPreparedRpc: function(provider, module){
            var name = module+provider;
            if(typeof this.preparedRpcs[name] != 'undefined'){
                return this.preparedRpcs[name];
            } else {
                return null;
            }
        },
        _setPreparedRpc: function(provider, module, rpc){
            var name = module+provider;
            this.preparedRpcs[name] = rpc;
        },
        _prepareRpc: function(provider, module){
            // Load dojo rpc and initialize it
            dojo.require("dojo.rpc.JsonService");
            var url = "/json-rpc?module="+module+"&provider="+provider;
            var rpc = new dojo.rpc.JsonService(url);
            
            // Add "abstract" methods that will represent "RPC call API"
            rpc.call = function(name){
                return this.callArray(name, Array.prototype.slice.call(arguments,1, arguments.length));
            }
            rpc.callArray = function(name, args){
                if(typeof this[name] == 'undefined'){
                    return null;
                } else {
                    return this[name].apply(this, args);
                }
            }
            
            return rpc;
        },
        _prepareRest: function(provider, module){
            var name = provider+module;
            dojo.require("dojox.rpc.Rest");
            var rest = new dojox.rpc.Rest("/json-rest.php/"+module+"/"+provider+"/",true);
            return rest;
        },
        _setPreparedRest: function(provider, module, rest){
            this.preparedRests[name] = rest;
        },
        _getPreparedRest: function(provider, module){
            var name = provider+module;
            if(typeof this.preparedRests[name] != 'undefined'){
                return this.preparedRests[name];
            } else {
                return false;
            }
        },
        _initRestStore: function(provider, module){
            dojo.require("dojox.data.JsonRestStore");
            var targetUrl = "/json-rest.php/"+module+"/"+provider+"/";
            
            var restStore = new dojox.data.JsonRestStore({target: targetUrl});
            return restStore;
            
        },
        _getPreparedRestStore: function(provider, module){
            var name = module+provider;
            if(typeof this.preparedRestStores[name] == 'undefined'){
                return false;
            } else {
                return this.preparedRestStores[name]; 
            }
        },
        _setPreparedRestStore: function(provider, module,restStore){
            var name = module+provider;
            this.preparedRestStores[name] = restStore;
        },
        _ucfirst: function(value){
            return value[0].toUpperCase()+value.substring(1);
        },
        
        
        
        callRpc: function(method, args, provider, module){
            var rpc = null;
            if(rpc = this.prepareRpc(provider, module)){
                return rpc.callArray(method, args);
            }
            return null;
        },
        
        /**
         * @param {string} provider
         * @param {string} module
         * @return {dojo.rpc.JsonService|false}
         */
        prepareRpc: function(provider, module){
            var rpc = null;
            if(rpc = this._getPreparedRpc(provider, module)){
                return rpc;
            } else {
                rpc = this._prepareRpc(provider, module);
                if(rpc){
                    this._setPreparedRpc(provider,module, rpc);
                    return rpc;
                }
                else {
                    return false;
                }
            }
        },
        
        
        /**
         * @param {string} method
         * @param {string} id
         * @param {Array} data
         * @param {string} provider
         * @param {string} module
         * @return response
         */
        callRest: function(method,id, data, provider, module){
            var rest = this.prepareRest(provider,module);
            if(rest){
                return rest[method.toLowerCase()](id,data);
            } else {
                return false;
            }
        },
        
        prepareRest: function(provider, module){
            var rest;
            if(rest = this._getPreparedRest(provider, module)){
                return rest;
            } else {
                rest = this._prepareRest(provider, module);
                if(rest){
                    this._setPreparedRest(provider, module,rest);
                    return rest;
                } else {
                    return false;
                }
            }
        },
        
        prepareRestStore: function(provider, module){
            provider = this._ucfirst(provider);
            module = this._ucfirst(module);
            
            var restStore = this._getPreparedRestStore(provider, module);
            if(!restStore){
                restStore = this._initRestStore(provider, module);
                this._setPreparedRestStore(provider,module,restStore);
            }
            
            return restStore;
        }
    }
};

azfcms.init();

