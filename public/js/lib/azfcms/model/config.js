define(['dojo/_base/declare','dojo/_base/lang',
    'dojo/_base/Deferred','azfcms/model'],function(declare,lang,Deferred,model){
        var __class = declare(null,{
            STATIC:"static",
            DYNAMIC:"dynamic",
            constructor: function(configRpc){
                this.configRpc = configRpc;
            },
            /**
             * @return {dojo.Deferred}
             */
            _getParams: function(type, id){
                switch(type){
                    case this.STATIC:
                        return this.configRpc.getStaticParams(id);
                        break;
                    case this.DYNAMIC:
                        return this.configRpc.getDynamicParams(id);
                        break;
                }
            },
            /**
             * @return {dojo.Deferred}
             */
            _setParam: function(type, id, name, value){
                switch(type){
                    case this.STATIC:
                        return this.configRpc.setStaticParam(id,name,value);
                        break;
                    case this.DYNAMIC:
                        return this.configRpc.setDynamicParam(id,name,value);
                        break;
                }
                
            },
            /**
             * @return {dojo.Deferred}
             */
            _deleteParam: function(type, id, name){
                switch(type){
                    case this.STATIC:
                        return this.configRpc.deleteStaticParam(id,name);
                        break;
                    case this.DYNAMIC:
                        return this.configRpc.deleteDynamicParam(id,name);
                        break;
                }
            },
            
            /**
             * Get static params for id node
             * @param {int} id
             * @return {dojo.Deferred}
             */
            getStaticParams: function(id){
                var d = new Deferred();
                this._getParams(this.STATIC, id).
                addCallback(function(params){
                    d.callback(params);
                });
                return d;
            },
            /**
             * @param {int} id
             * @param {String} name
             * @return {dojo.Deferred}
             */
            getStaticParam: function(id, name){
                var d= new Deferred();
                this._getParams(this.STATIC, id).
                    addCallback(function(params){
                        var response ;
                        if(params[name]){
                            response = params[name];
                        }else {
                            response = null;
                        }
                        d.callback(response);
                    })
                return d;
            },
            /**
             * @param {int} id
             * @param {String} name
             * @param {mixed} value
             * @return {dojo.Deferred}
             */
            setStaticParam: function(id, name, value){
                var d = new Deferred();
                this._setParam(this.STATIC, id, name, value).
                    addCallback(function(response){
                    d.callback(response);
                    })
                return d;
            },
            /**
             * @param {int} id
             * @param {String} name
             * @return {dojo.Deferred}
             */
            deleteStaticParam: function(id, name){
                var d = new Deferred();
                this._deleteParam(this.STATIC, id, name).
                    addCallback(function(response){
                        d.callback(response);
                    })
                return d;
            },
            /**
             * @param {int} id
             * @return {dojo.Deferred}
             */
            getDynamicParams: function(id){
                var d = new Deferred();
                this._getParams(this.DYNAMIC,id).addCallback(function(params){
                    d.callback(params);
                })
                return d;
            },
            /**
             * @param {int} id
             * @param {String} name
             * @return {dojo.Deferred}
             */
            getDynamicParam: function(id, name){
                var d = new Deferred();
                this._getParams(this.DYNAMIC,id).addCallback(function(response){
                    response = response[name]?response[name]:null;
                    d.callback(response);
                });
                return d;
            },
            /**
             * @param {int} id
             * @param {String} name
             * @param {String} value
             * @return {dojo.Deferred}
             */
            setDynamicParam: function(id, name, value){
                var d = new Deferred();
                this._setParam(this.DYNAMIC, id, name, value).addCallback(function(response){
                    d.callback(response); 
                });
                return d;
            },
            /**
             * @param {int} id
             * @param {String} name
             * @return {dojo.Deferred}
             */
            deleteDynamicParam: function(id, name){
                var d = new Deferred();
                this._deleteParam(this.DYNAMIC, id, name).addCallback(function(response){
                    d.callback(response);
                });
                return d;
            },
            /**
             * @param {int} id
             * @return {dojo.Deferred}
             */
            getPluginsNames: function(id){
                var d = new Deferred();
                
                this.configRpc.getPluginsNames(id).
                    addCallback(d,"callback");
                    return d;
            },
            /**
             * @param {int} id
             * @param {String} plugin
             * @return {dojo.Deferred}
             */
            getPluginParams: function(id, plugin){
                return this.configRpc.getPluginParams(id, plugin);
            }, 
            /**
             * @param {int} id
             * @param {String} plugin
             * @param {String} name
             * @return {dojo.Deferred}
             */
            getPluginParam: function(id, plugin, name){
                var d = new Deferred();
                this.configRpc.getPluginParams(id, plugin).
                    then(function(params){
                    var param;
                    
                    if(param = params[name]);
                    else param = null;
                    d.callback(param);
                });
                return d;
            },
            /**
             * Returned callback method
             * @param {int} id
             * @param {String} plugin
             * @param {String} name
             * @param {mixed} value
             * @return {dojo.Deferred}
             */
            setPluginParam: function(id, plugin, name, value){
                return this.configRpc.setPluginParam(id, plugin, name, value);
            },
            /**
             * @param {int} id
             * @param {String} plugin
             * @param {Object} plugin
             * @return {dojo.Deferred}
             */
            setPluginParams: function(id, plugin, params){
                return this.configRpc.setPluginParams(id, plugin, params);
            },
            /**
             *
             * @param {int} id
             * @param {String} plugin
             * @param {String} name
             * @return {dojo.Deferred}
             */
            deletePluginParam: function(id, plugin, name){
                return this.configRpc.deletePluginParam(id, plugin, name);
            },
            /**
             * @param {int} id
             * @param {String} plugin
             * @return {dojo.Deferred}
             */
            deletePlugin: function(id, plugin){
                return this.configRpc.deletePlugin(id, plugin);
            }
       });
    
        var instance = new __class();
        instance.__class = __class;
        return instance;
    });


