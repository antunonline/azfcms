<html>
    <head>
        <title>Rest store</title>
        <link rel="stylesheet" type="text/css" href="/js/dojo/dojo/resources/dojo.css" />
        <link rel="stylesheet" type="text/css" href="/js/dojo/dijit/themes/claro/claro.css" />
        <link rel="stylesheet" type="text/css" href="/js/dojo/dojox/grid/resources/claroGrid.css" />
        <style type="text/css" >
            root,html,body {
                height: 100%;
                margin:  0px;
                padding:  0px;
            }

            #gridElement {
                height:90%;
            }
        </style>
        <script type="text/javascript">
            var dojoConfig = {
                async:true,
                packages: [
                    {name:'dojo',location:'.'},
                    {name:'azfcms',location:'/js/azfcms'}
                ]
            }
        </script>
        <script type="text/javascript" src="/js/dojo/dojo/dojo.js"></script>
        <script type="text/javascript" src="/js/azfcms/azfcms.js"></script>
        <script type="text/javascript">
            require(['dojo/ready'],function(ready){
                ready(function(){
                    require(['dojo/store/JsonRest','dijit/Tree','dijit/tree/dndSource'
                        ],function(JsonRest,Tree,dndSource){
                        
                        var store = new JsonRest({
                            target:"/json-rest.php/default/navigation/",
                            mayHaveChildren: function(item){
                                
                                return true;
                            },
                            getChildren: function(item, onComplete, onError){
                                onComplete(item.childNodes);
                            },
                            getRoot: function(onItem, onError){
                                this.get("1").then(onItem,onError)
                            },
                            getLabel: function(object){
                                return object.id;
                            },
                            put: function(load){
                                alert("PUT");
                                  load({},{})
                            },
                            pasteItem: function(child, oldParent, newParent, bCopy, insertIndex){
                                alert("OK")
                            },
                            remove: function(){
                                alert("remove")
                            }
                        })
                        
                        
                        var tree = new Tree({
                            model:store,
                             dndController: dndSource
                        },"tree");
                        tree.startup();
                        
                    })
                })
            })
            
            
        </script>
    </head>
    <body class="claro">

        <div id="tree" >

        </div>
    </body>
</html>
