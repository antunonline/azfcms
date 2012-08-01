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
    <style type="text/css" >
        root,html,body {
            width:100%;height:100%;
            margin:0px;
        }
    </style>
    <script src="/js/lib/dojo/dojo.js"
            data-dojo-config="async: true">
    </script>
    <script>
        var env;
        require(
        ['azfcms/bootstrap/adminEnv','dojo/domReady'],function
        (adminEnv,ready){
            ready(function(){
                adminEnv.startup("adminPane")
            })
                
        }
    );
                            
                        
                        
    </script>
</head>
<body class="claro">
    <div style="width:100%;height:100%;" id="adminPane">
        
    </div>
</body>
</html>
