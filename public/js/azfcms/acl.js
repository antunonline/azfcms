/* 
 * @author Antun Horvat
 */
define("azfcms/acl",['dojo/_base/declare','azfcms/bootstrap!getAcl'],function(declare, aclRules){
    var _class = declare(null,{
        constructor: function(aclRules){
            this._rules = aclRules;
        },
        
        /**
         * @param {string} resource
         * @param {string} privilege
         * @return boolean
         */
        isAllowed: function(resource, privilege){
            if(
            typeof this._rules[resource] == 'undefined' ||
            typeof this._rules[resource][privilege] == 'undefined')
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
            var resource,privilege;
            for(var i = 0; i < rules.length; i++){
                resource = rules[i].resource;
                privilege = rules[i].privilege;
                
                if(!resource in this._rules){
                    this._rule[resource] = {};
                }
                this._rule[resource][privilege] = true;
            }
        },
        load: function(a,b,c){
            c(_class);
        }
    });
    
    var _classInstance = new _class(aclRules);
    _classInstance.__class = _class;
    return _classInstance;
    
})
