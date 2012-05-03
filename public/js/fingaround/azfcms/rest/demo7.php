<!DOCTYPE HTML>
<html lang="en">
    <head>
        <title>Rest store</title>
        <link rel="stylesheet" type="text/css" href="/js/dojo/dojo/resources/dojo.css" />
        <link rel="stylesheet" type="text/css" href="/js/dojo/dijit/themes/claro/claro.css" />
        <style type="text/css" >
            html,body {
                height: 100%;
                margin:  0px;
                padding:  0px;
            }

            
        </style>
        <script type="text/javascript">
            var dojoConfig = {
                async:true,
                isDebug:true
            }
        </script>
        <script type="text/javascript" src="/js/dojo/dojo/dojo.js"></script>
        <script type="text/javascript" src="/js/azfcms/azfcms.js"></script>
        <script type="text/javascript">
            require(['dojo/ready'],function(ready){
                ready(function(){
                    require(['dojo/store/JsonRest','dijit/Tree','dijit/tree/dndSource',
                        'dojo/aspect'
                    ],function(JsonRest,Tree,dndSource,
                                aspect){
                        
                        var store = new JsonRest({
                            target:"/json-rest.php/default/navigation/",
                            getRoot: function(onItem, onError){
                                this.get(1).then(function(item){
                                    item.children = item.childNodes;
                                    item.childNodes=null;
                                    onItem(item)
                                }, onError);
                            },
                            getLabel: function(item, onComplete, onError){
                                return item.url;
                            },
                            
                            mayHaveChildren: function(){
                                return true;
                            },
                            getChildren: function(item,onComplete){
                                this.get(item.id).then(function(loaded){
                                    loaded.children = loaded.childNodes;
                                    onComplete(loaded.children);
                        })
                            },
                            isItem: function(){
                                return true;
                            },
                            fetchItemByIdentity: function(args){
                                alert("SHIT");
                            },
                            getIdentity: function(item){
                        
                                return item.id;
                            },
                            newItem: function(){
                                alert("SHIT")
                            },
                            pasteItem: function(){
                                alert("SHIT")
                            }
                        });
                        
                        
                        
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
        <div id='tree' style="width:400px;height:400px;">
            
        </div>
    </body>
</html>
