/* 
 * @author Antun Horvat
 */
define([
    'require'
],function(require){
    require([
        './navigation/modules',
        './content/modules'
    ])
    doh.registerUrl("azfcms.tests.controller.ExtensionEditorController", require.toUrl('')+'/azfcms/tests/controller/ExtensionEditorController.html')
});


