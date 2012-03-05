define(['./_Model','dojo/rpc/JsonService','dojox/rpc/Rest','dojox/data/JsonRestStore'],
function(Model, JsonRpc, RestRpc, JsonRestStore){
    return new Model(JsonRpc, RestRpc, JsonRestStore);
})