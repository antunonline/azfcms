/* 
 * @author Antun Horvat
 * 
 */
define(['azfcms/main','azfcms/model'],function(main, model){
    
    var loader = {
        getAcl: function(require, callback){
            model.callRpc("getAcl",[],'bootstrap','default').
            then(function(r){
                callback(r);
            });
        },
        getIdentity: function(require, callback){
            model.callRpc("getIdentity",[],"bootstrap","default").
            then(function(r){
                callback(r);
            })
        }
    }
    
    var module = {
        load: function(id, require, callback){
            loader[id](require, callback);
        },
        bootstrap: function(){
            require(["azfcms/identity","azfcms/acl"], function(identity, acl){
                var user = identity.getLoginName();
                alert("Hello "+user);
            })
        }
    };
    
    return module;
})


