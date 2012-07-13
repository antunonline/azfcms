/* 
 * @author Antun Horvat
 */
define([
    'require'
],function(require){
    require([
        './navigation/modules',
        './content/modules',
        './extensionPlugin/modules'
    ])
    doh.registerUrl("azfcms.tests.controller.ExtensionEditorController", require.toUrl('')+'/azfcms/tests/controller/ExtensionEditorController.html')
    doh.registerUrl("azfcms.tests.controller.AbstractEditorController", require.toUrl('')+'/azfcms/tests/controller/AbstractEditorController.html')
});


