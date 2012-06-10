/* 
 * @author Antun Horvat
 */
define(['doh','dojo','require'],function(doh,dojo,require){
    
    require([
        './navigation/modules',
        './content/modules'
        ])
        doh.registerUrl("azfcms.tests.view.ExtensionEditorPane", require.toUrl('')+'/azfcms/tests/view/ExtensionEditorPane.html')
        
})


