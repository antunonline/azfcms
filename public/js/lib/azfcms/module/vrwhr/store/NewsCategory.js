define(['dojo/_base/lang','dojo/_base/declare','azfcms/store/QueryLangStore'],
function(lang,declare, QueryLangStore){
    return  declare([QueryLangStore],{
        idProperty:"id",
        queryMethod:"cms.vrwhr.newsCategory.query",
        getMethod:"",
        putMethod:"",
        postMethod:""
    });
});

