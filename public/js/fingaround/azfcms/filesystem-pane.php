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
        <style>
            html,body {
                width:100%;height:100%;
            }
        </style>
        <script>
            var env;
            require(
            ['azfcms/view/FilesystemPane','azfcms/store/Filesystem'],function
            (FilesystemPane,FilesystemStore){
                
                var gridStore= new FilesystemStore({
                    idProperty:"name",
                    queryOptions:{
                        directory:false
                    }
                });
                var treeStore = new FilesystemStore({
                    idProperty:"name",
                    getOptions:{
                        file:false
                    }
                });
                
                var fsPane = new FilesystemPane({
                    gridStore:gridStore,
                    treeStore:treeStore
                });
                fsPane.placeAt(document.body);
                fsPane.resize();
                fsPane.on("treeselect",function(selection){
                    
                    fsPane.reload(selection);
                })
                window.pane = fsPane;
            }
        );
                            
                        
                        
        </script>
    </head>
    <body class="claro">

      


    </body>
</html>
