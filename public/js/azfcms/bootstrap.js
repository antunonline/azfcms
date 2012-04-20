/* 
 * @author Antun Horvat
 * 
 */
define([
    'azfcms/model',   'azfcms/identity',   'azfcms/acl',
    './_bootstrap/loader!acl',            './_bootstrap/loader!identity'
    ],
    function(model,             identity,           acl, 
        aclRules,           identityObject){
    
        // Set acl
        acl.setRules(aclRules);
    
        // Set identity
        identity.setIdentity(identityObject);
    
        var module = {
            bootstrap: function(){
            
            }
        };
    
        return module;
    })


