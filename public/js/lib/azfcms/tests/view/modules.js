/* 
 * @author Antun Horvat
 */
define(['doh','dojo','require'],function(doh,dojo,require){
    
    require([
        './navigation/modules',
        './content/modules'
        ])
        doh.registerUrl("azfcms.tests.view.ExtendedEditorPane", require.toUrl('')+'/azfcms/tests/view/ExtendedEditorPane.html')
        
})


