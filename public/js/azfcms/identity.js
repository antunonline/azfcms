
/**
 * @author Antun Horvat
 */
define(['dojo/_base/declare'],function(declare){
    var _class = declare(null,{
        constructor: function(){
            this._identity={};
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
        }
    });
    
    var _classInstance = new _class();
    _classInstance.__class = _class;
    return _classInstance;
})
