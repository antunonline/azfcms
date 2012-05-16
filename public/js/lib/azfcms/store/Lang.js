/* 
 * @author Antun Horvat
 */
define(
    ['dojo/store/Memory','dojo/_base/declare','dojo/store/util/QueryResults',
    'dojo/_base/Deferred'],function
    (Memory, declare, QueryResults,
        Deferred){
        var _class = declare([Memory],{
        
            constructor: function(args){
                this.model = args.model;
            },
            /**
         * Override fetch
         */
            query: function(args){
                if("isLoaded" in this){
                    return this.inherited(arguments);
                }
            
                var self = this;
                
                var result = this.model.invoke(self.expr);
                result.then(function(data){
                    self.setData(data);
                    self.isLoaded=true;
                });
                return QueryResults(result);
            }
        });
    
        return _class;
    })


