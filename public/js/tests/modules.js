/* 
 * @author Antun Horvat
 */
define(['dojo/_base/kernel'],function(dojo){
    dojo.registerModulePath('azfcms','/js/azfcms');
    
    require([
        'tests/azfcms/_Model',
        'tests/azfcms/model/config',
        'tests/azfcms/identity'
])
})


