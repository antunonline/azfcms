require(['doh','azfcms/identity'],function(doh, identity){
    
    doh.registerTests('azfcms.identity',[
        function(){
            var instance= new identity.__class();
            instance.setIdentity({
                loginName:"identity",
                id: 1
            });
            doh.assertEqual("identity",instance.getLoginName());
            doh.assertEqual(1,instance.getId());
        }
    ])
})