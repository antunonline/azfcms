define(['azfcms/bootstrap','azfcms/acl','azfcms/identity'],
function(   bootstrap,      acl,            identity){
    return {
        // Define bootstrap reference
        bootstrap: bootstrap,
        // Define acl reference
        acl: acl,
        // Define identity reference
        identity: identity
    };
});