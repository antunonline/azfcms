/* 
 * @author Antun Horvat
 */
define(['azfcms/model'],function(model){
    
    var loader = {
        identity: function(callback){
            model.callRpc("getIdentity",[],"bootstrap","default").
            then(function(r){
                callback(r);
            })
        },
        
        acl: function(callback){
            model.callRpc("getAcl",[],'bootstrap','default').
            then(function(r){
                callback(r);
            })
        }
    }
    
    return {
        load: function(id, require, callback){
            loader[id](callback);
        }
    }
})


