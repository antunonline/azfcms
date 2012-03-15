require(['doh','azfcms/acl'],function(doh, AclObject){
    
    var Acl = AclObject.__class;
    doh.registerTests('azfcms.identity!class',[
        function(){
            var rules = [
                {resource:"image",privilege:"read"}
            ];
            var instance = new Acl();
            
            instance.setRules(rules);
            
            doh.assertTrue(instance.isAllowed("image","read"));
            doh.assertFalse(instance.isAllowed("image","write"));
        }
    ])
})