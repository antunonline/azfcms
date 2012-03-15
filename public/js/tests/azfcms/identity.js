require(['doh','azfcms/identity!class'],function(doh, identity){
    
    doh.registerTests('azfcms.identity!class',[
        function(){
            var instance= new identity({
                loginName:"identity",
                id: 1
            });
            doh.assertEqual("identity",instance.getLoginName());
            doh.assertEqual(1,instance.getId());
        }
    ])
})