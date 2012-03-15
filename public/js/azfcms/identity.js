
/**
 * @author Antun Horvat
 */
define(['dojo/_base/declare', 'azfcms/bootstrap!getIdentity'],function(declare, identity){
    var _class = declare(null,{
        constructor: function(identity){
            this._identity=identity;
        },
        /**
         * @param {Object} identiyt
         */
        setIdentity: function(identity){
            this._identity = identity;
        },
        
        /**
         * @return {string}
         */
        getLoginName: function(){
            return this._identity.loginName;
        },
        
        /**
         * @return {int}
         */
        getId: function(){
            return this._identity.id;
        },
        load: function(id,requiest,callback){
            callback(_class);
        }
    });
    
    var _classInstance = new _class(identity);
    _classInstance.__class = _class;
    return _classInstance;
})
