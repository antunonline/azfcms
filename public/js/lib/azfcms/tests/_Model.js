/**
 * @author Antun Horvat
 */
define(['doh','azfcms/_Model','dojo/_base/Deferred'],function(doh, Model, Deferred){
    doh.registerTests("azfcms._Model", [
        function(){
            var model = new Model(function(url){
                doh.assertEqual(url ,"/json-rpc.php?module=module&provider=provider")
                
                this.index =function(){};
            },{},{});
            model.callRpc("index",[],"provider","module");
        },
        function(){
            var model = new Model(function(){
                this.index = function(arg1, arg2){
                    doh.assertEqual("one",arg1);
                    doh.assertEqual("two",arg2);
                }
            });
            
            model.callRpc("index",["one","two"],"index","index");
        },
        function(){
            var counter = 0;
            var model = new Model(function(url){
                
                counter++;
                doh.assertTrue(counter < 2);
                this.index = function(){};
            });
            
            model.callRpc("index",[],"index","index");
            model.callRpc("index",[],"index","index");
        },
        function (){
            var d = new Deferred();
            var model = new Model(function(url){
                if(("/json-rpc.php?module=module&provider=provider" == url)){
                    d.callback(true);
                }else {
                    d.callback(false);
                }
            });
            model.prepareRpc("provider","module");
            
            return d;
        },
        function(){
            var count = 0;
            var model = new Model(function(url){
                count++;
                doh.assertTrue(count < 2);
                
                this.index = new Function();
            });
            var provider = model.prepareRpc("index","index");
            provider.call("index",[]);
        },
        function(){
            var d = new Deferred();
            var model = new Model(function(){
                this.index  = function(a,b){
                    if(a == "a" && b == "b"){
                        d.callback(true);
                    }else {
                        d.callback(false);
                    }
                }
            })
            var prepared = model.prepareRpc("index","index");
            prepared.call("index","a","b");
            
            return d;
        },
        function(){
            var model = new Model(function(){
                this.index = function(){
                    return "OK";
                }
            });
            
            doh.assertEqual("OK",model.prepareRpc("i","i").call("index"));
        },
        function(){
            var d = new Deferred();
            var model = new Model(null,function(url){
                if(url == "/json-rest.php/module/provider/")
                    d.callback(true);
                else 
                    d.callback(false);
                return new Function();
            });
            model.callRest("get",{},0,"provider","module");
            return d;
        },
        function(){
            var model = new Model(null,function(url){
                return new Function();
            });
            var pass = null;
            try{
                model.callRest("get",{},0,"provider");
                pass = false
            }catch(e){
                pass = true;
            } 
            doh.assertTrue(pass);
        },
        function(){
            var model = new Model(null,function(){
                return function(input){
                    return input;
                }
            });
            
            var expected = "first,last";
            doh.assertEqual(expected,model.callRest("get",expected,1,"index","index"))
        }
    ])
});
