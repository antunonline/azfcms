<!DOCTYPE HTML>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Demo: Connecting Tree to a Store</title>
        <link rel="stylesheet" href="/js/lib/dijit/themes/claro/claro.css" media="screen">
        <link rel="stylesheet" href="/js/lib/dojo/resources/dojo.css" media="screen">
        <!-- load dojo and provide config via data attribute -->
<!--		<script src="/dojo/dojo.js"
                        data-dojo-config="isDebug: true,parseOnLoad: true">
        </script>-->
        <script src="/js/lib/dojo/dojo.js"
                data-dojo-config="isDebug: true, async: true">
        </script>
        <script>
            var env;
            require(
            ['azfcms/bootstrap/adminEnv'],function
            (adminEnv){
                adminEnv.adminDialog.show();
                env = adminEnv;
                
            }
        );
                            
                        
                        
        </script>
    </head>
    <body class="claro">

        <div id="rootPane"></div>


    </body>
</html>
