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
                    require(['azfcms/model','dijit/tree/TreeStoreModel','dijit/Tree','dijit/tree/dndSource'],function(model,TreeStoreModel,Tree,dndSource){
                        var navigationStore = model.prepareRestStore('navigation','default');
                        navigationStore.put = function(){
                            
                        }
                        
                       
                        var treeModel = new TreeStoreModel({
                            store:navigationStore,
                            childrenAttrs: ['childNodes'],
                            query:{id:'1'},
                            labelAttr:'id'
                        });
                        
                        
                        new Tree({
                            model:treeModel,
                            dndController: dndSource
                        },"tree");
                        
                    })
                })
            })
            
            
        </script>
    </head>
    <body class="claro">
        
        <div id="tree" style="width:600px;
             height:600px;">
            
        </div>
    </body>
</html>
