/* 
 * @author Antun Horvat
 */
require(['doh','azfcms/model/config','dojo/_base/Deferred'],function(doh, config, Deferred){
    doh.registerTests("azfcms.model.config", [
        function (){
            var d = new Deferred();
            var conf = new config.__class({
                getStaticParams: function(id){
                    var d = new Deferred();
                    d.callback();
                    return d;
                }
            });
            
            conf.getStaticParams(1).
                addCallback(function(){
                d.callback(true);
                })
            
            return d;
        },
        function (){
            var d = new Deferred();
            var conf = new config.__class({
                getStaticParams: function(id){
                    var d = new Deferred();
                    if(id==33)d.callback();
                    else d.errback();
                    return d;
                }
            });
            
            conf.getStaticParams(33).
                addCallback(function(){
                d.callback(true);
                })
            
            return d;
        },
        function(){
            var d = new Deferred();
            var r = [1,2,3];
            var conf = new config.__class({
                getStaticParams: function(){
                    var d = new Deferred();
                    d.callback(r)
                    return d;
                }
            });
            
            conf.getStaticParams(33).addCallback(function(i){
                if(i === r)
                    d.callback(true);
                else 
                    d.callback(false);
            });
            return d;
        },
        function(){
            var d = new Deferred();
            var conf = new config.__class({
                getStaticParams: function(){
                    var d = new Deferred();
                    d.callback({a:"a"})
                    return d;
                }
            });
            conf.getStaticParam(33,"a").addCallback(function(r){
                if(r == "a")
                    d.callback(true);
                else 
                    d.callback(false);
            })
            return d;
        },
        function(){
            var d = new Deferred();
            var conf = new config.__class({
                getStaticParams: function(){
                    var d = new Deferred();
                    d.callback({a:"a"})
                    return d;
                }
            });
            conf.getStaticParam(33,"b").addCallback(function(r){
                if(r == null)
                    d.callback(true);
                else 
                    d.callback(false);
            })
            return d;
        },
        function(){
            var d= new Deferred();
            var conf = new config.__class({
                setStaticParam:function(id, name, value){
                    var d = new Deferred();
                    if(id==1 && name==2 && value==3){
                        d.callback(true);
                    }
                    return d;
                }
            });
            conf.setStaticParam(1,2,3).addCallback(function(r){
                if(r){
                    d.callback(true);
                }else {
                    d.callback(false);
                }
            })
            return d;
        },
        function(){
            var d = new Deferred();
            var conf = new config.__class({
                deleteStaticParam: function(id, name){
                    var d = new Deferred();
                    if(id==1 && name == 2){
                        d.callback(true);
                    }
                    return d;
                }
            });
            conf.deleteStaticParam(1,2).addCallback(function(f){
                d.callback(f);
            })
            return d;
        },
        function(){
            var d = new Deferred();
            var conf = new config.__class({
                getDynamicParams: function(){
                    var d= new Deferred();
                    d.callback();
                    return d;
                }
            });
            
            conf.getDynamicParams(33).addCallback(function(){
                d.callback(true);
            });
            return d;
        },
        function(){
            var d= new Deferred();
            var conf = new config.__class({
                setDynamicParam:function(id, name, value){
                    var d = new Deferred();
                    if(id==1 && name==2 && value==3){
                        d.callback(true);
                    }
                    return d;
                }
            });
            conf.setDynamicParam(1,2,3).addCallback(function(r){
                if(r){
                    d.callback(true);
                }else {
                    d.callback(false);
                }
            })
            return d;
        },
        function(){
            var d = new Deferred();
            var conf = new config.__class({
                deleteDynamicParam: function(id, name){
                    var d = new Deferred();
                    if(id==1 && name == 2){
                        d.callback(true);
                    }
                    return d;
                }
            });
            conf.deleteDynamicParam(1,2).addCallback(function(f){
                d.callback(f);
            })
            return d;
        },
        
        function(){
            var d = new Deferred();
            var seed = [1,2,3];
            var conf = new config.__class({
                getPluginsNames: function(r){
                    var d = new Deferred();
                    d.callback(seed);
                    return d;
                }
            });
            conf.getPluginsNames(33).then(function(response){
                d.callback(response === seed);
            });
            return d;
        },
        
        function(){
            var d = new Deferred();
            var conf = new config.__class({
                getPluginsNames: function(r){
                    var d = new Deferred();
                    d.callback(r==33);
                    return d;
                }
            });
            conf.getPluginsNames(33).then(function(response){
                d.callback(response);
            });
            return d;
        },
        
        function(){
            var d = new Deferred();
            var originId = "2";
            var originPlugin = "33";
            var conf = new config.__class({
                getPluginParams: function(id, plugin){
                    var d = new Deferred();
                    if(id === originId && plugin === originPlugin)
                        d.callback(true);
                    return d;
                }
            });
            conf.getPluginParams(originId, originPlugin).
                then(function(){
                d.callback(true);
                })
            return d;
        },
        
        function(){
            var d = new Deferred();
            var response = {a:"a"};
            var conf = new config.__class({
                getPluginParams: function(id, plugin){
                    var d = new Deferred();
                    d.callback(response);
                    return d;
                }
            });
            conf.getPluginParams(1,"2").
                then(function(r){
                d.callback(response === r);
                })
            return d;
        },
        function(){
            var d = new Deferred();
            var _id = "1";
            var _plugin = "one";
            var _name = "name";
            var _value = "value"
            var conf = new config.__class({
                setPluginParam: function(id, plugin, name, value){
                    var d= new Deferred();
                    if(_id===id && _plugin===plugin && name===_name 
                    && value === _value)
                    d.callback(true);
                else 
                    d.callback(false);
                    return d;
                }
            });
        conf.setPluginParam(_id, _plugin, _name, _value).then(function(r){
            d.callback(r);
        })
            return d;
        },
        function(){
            var d = new Deferred();
            var _id = "1";
            var _params = {"1":"2"};
            var conf = new config.__class({
                setPluginParams: function(id, params){
                    if(id === _id && params === _params)
                        return d;
                }
            });
            conf.setPluginParams(_id, _params).callback(true);
            return d;
        },
        function(){
            var d = new Deferred();
            var _id = "1";
            var _plugin = "plugin";
            var _name = "name";
            var conf = new config.__class({
                deletePluginParam: function(id, plugin, name){
                    if(id === _id && plugin === _plugin 
                    && name === _name)
                    return d;
                }
            });
        conf.deletePluginParam(_id, _plugin, _name).callback(true);
            return d;
        },
        function(){
            var d = new Deferred();
            var _id = "1";
            var _plugin = "plugin";
            var conf = new config.__class({
                deletePlugin: function(id, plugin){
                    if(id === _id && plugin === _plugin)
                    return d;
                }
            });
        conf.deletePlugin(_id, _plugin).callback(true);
            return d;
        }
    ]);
});


