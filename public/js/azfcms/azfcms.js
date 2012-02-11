/**
 * @author Antun Horvat
 */


var azfcms = {
    init: function(){
        
    },
    model : {
        preparedRpcs: {},
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
        }
    }
};

azfcms.init();

