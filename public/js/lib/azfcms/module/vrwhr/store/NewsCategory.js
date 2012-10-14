define(['dojo/_base/lang','dojo/_base/declare','azfcms/store/QueryLangStore'],
function(lang,declare, QueryLangStore){
    return  declare([QueryLangStore],{
        idProperty:"id",
        queryMethod:"cms.vrwhr.test.query",
        getMethod:"cms.vrwhr.test.get",
        putMethod:"cms.vrwhr.test.put",
        addMethod:"cms.vrwhr.test.add",
        removeMethod:"cms.vrwhr.test.remove"
    });
});

