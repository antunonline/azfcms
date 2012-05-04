define(['./_Model','dojo/rpc/JsonService','dojox/rpc/Rest','dojo/store/JsonRest'],
function(Model, JsonRpc, RestRpc, JsonRestStore){
    return new Model(JsonRpc, RestRpc, JsonRestStore);
})