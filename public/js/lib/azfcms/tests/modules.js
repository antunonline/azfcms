/* 
 * @author Antun Horvat
 */
define(['require','doh'],function(require,doh){
    
    require([
        './_Model',
        './view/modules',
        './controller/modules',
        './model/modules',
        './store/modules'
        ])
        
        doh.registerUrl("azfcms.tests.CallChain", require.toUrl('')+'/azfcms/tests/CallChain.html')
        
})


