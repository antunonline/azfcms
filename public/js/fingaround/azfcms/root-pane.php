<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Demo: Connecting Tree to a Store</title>
        <link rel="stylesheet" href="/js/lib/dijit/themes/claro/claro.css" media="screen">
        <link rel="stylesheet" href="/js/lib/dojo/resources/dojo.css" media="screen">
        <link rel="stylesheet" href="/js/lib/dojox/grid/resources/claroGrid.css" media="screen" />
        <link rel="stylesheet" href="/js/lib/dojox/grid/resources/Grid.css" media="screen" />
    </script>
    <script src="/js/lib/dojo/dojo.js"
            data-dojo-config="isDebug: false, async: true">
    </script>
    <script>
        var env;
        require(
        ['azfcms/bootstrap/adminEnv','dojo/domReady'],function
        (adminEnv,ready){
            ready(function(){
                adminEnv.adminDialog.show();
                env = adminEnv;
            })
                
        }
    );
                            
                        
                        
    </script>
</head>
<body class="claro">

    <div id="rootPane"></div>


</body>
</html>
