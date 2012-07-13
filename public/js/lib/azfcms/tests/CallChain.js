/* 
 * @author Antun Horvat
 */
define(['dojo/_base/declare','dojo/_base/lang','dojo/_base/array','dojo/_base/Deferred'],function(declare,lang,array,Deferred){
    
    return declare([],{
        /**
         * doh instance passed to the constructor
        */
//        doh:null,
        /**
         * doh.Deferred object returned from the test context through return value
         * This value will mark test as asynchronous
         */
//        async:null,
        /**
         * Number of tests that shall be produced, and expected to be executed
         */
//        testCount:0,
        /**
         * Status array that contains status of each test
         */
//        _stats:[],
        /**
         * Status pointer
         */
//        _curStat:0,
        constructor:function(args){
            /**
             * args.doh - Instance of doh object
             * 
             * args.testCount  - Amount of test methods that shall be used
             */
            lang.mixin(this,args);
            if(!this.doh){
                throw new Error("azfcms/tests/CallChain: doh refernce is not provided");
            }
            this._stats = [];
            this._curStat = 0;
            this.async = new this.doh.Deferred();
            this._init();
            
            
        },
        _init:function(){
            var name = "";
            for(var i = 0, len = this.testCount; i < len; i++){
                name = "test"+i;
                this._initTest(name,i);
                this._stats[i] = false;
            }
            
        },
        _initTest:function(name,testId){
            var self = this;
            this[name] = function(){
                return self.doTest(testId,arguments);
            }
        },
        t:function(value){
            this.doh.t(value);
        },
        f:function(value){
            this.doh.f(value);
        },
        is:function(expected,actual){
            this.doh.is(expected,actual)
        },
        isFin:function(i){
            return this._stats[i]
        },
        doTest:function(testId,args){
            if(args.length == 0){
                this._markTestComplete(testId);
            }
            else {
                if(args[0]){
                    this._testArgConditions(args[0]);
                    this._markTestComplete(testId);
                }
            }
            return this;
        },
        _testArgConditions:function(conditions){
            // If true conditions are provided
            if(conditions.t){
                var t = conditions.t;
                // If condition is an array, test every provided condition for true
                if(lang.isArray(t)){
                    for(var i = 0, len = t.length; i < len; i++){
                        this.t(t[i]);
                    }
                } else {
                    this.t(t);
                }
            }
            
            // If false conditions are provided
            if(conditions.f){
                var f = conditions.f;
                // If condition is an array, test every provided condition for false
                if(lang.isArray(f)){
                    for(var i = 0, len = f.length; i < len; i++){
                        this.f(f[i]);
                    }
                } else {
                    this.f(f);
                }
            }
            
            if(conditions.is){
                var is = conditions.is;
                
                for(var i = 0, len = Math.floor(is.length/2)*2; i < len; i+=2){
                    this.is(is[i],is[i+1]);
                }
            }
        },
        _markTestComplete:function(testId){
            this._stats[testId] = true;
            this._curStat++;
            
            if(this._stats.length == this._curStat){
                if(array.every(this._stats,function(stat){return stat;})){
                    this.async.callback(true);
                } else {
                    this.async.callback(false);
                }
            }
        },
        deferred:function(){
            var d = new Deferred();
            d.callback.apply(d,arguments);
            return d;
        }
    })
})


