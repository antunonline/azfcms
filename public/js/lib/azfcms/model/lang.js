
define(["dojo/_base/declare","dojo/_base/xhr"],function(declare,xhr){
    var _Lang = declare([],{
        load:function(
            id,        // the string to the right of the !
            require,   // AMD require; usually a context-sensitive require bound to the module making the plugin request
            callback   // the function the plugin should call with the return value once it is done
            ){  
             // Do a POST request       
            xhr.post({
                url:"/json-lang.php",
                content:{expr:id},
                load: callback,
                error: function(){
                    if(typeof console !='undefined'){
                        console.debug(arguments);
                    }
                    throw "XHR Load error";
                }
            })
        }
    });
    
    // Create loader instance and store instance class into it for testing
    var instance = new _Lang();
    instance._Lang = _Lang;
    
    // Return instance
    return instance;
});

