/* 
 * @author Antun Horvat
 */
define("azfcms/acl",['dojo/_base/declare'],function(declare){
    var _class = declare(null,{
        constructor: function(){
            this._rules = {};
        },
        
        /**
         * @param {string} resource
         * @param {string} privilege
         * @return boolean
         */
        isAllowed: function(resource, privilege){
            if(
            resource in this._rules == false ||
            privilege in this._rules[resource] == false)
            {
                return false;
            }
            else {
                return true;
            }
        },
        
        /**
         * @param {Array} rules
         */
        setRules: function(rules){
            var resource,privilege = "";
            for(var i = 0; i < rules.length; i++){
                resource = rules[i].resource;
                privilege = rules[i].privilege;
                
                if(resource in this._rules == false){
                    this._rules[resource] = {};
                }
                this._rules[resource][privilege] = true;
            }
        },
        load: function(a,b,c){
            c(_class);
        }
    });
    
    var _classInstance = new _class();
    _classInstance.__class = _class;
    return _classInstance;
    
})
