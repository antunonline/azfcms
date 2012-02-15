<html>
    <head>
        <title>Rest store</title>
        <link rel="stylesheet" type="text/css" href="/js/dojo/dojo/resources/dojo.css" />
        <link rel="stylesheet" type="text/css" href="/js/dojo/dijit/themes/claro/claro.css" />
        <link rel="stylesheet" type="text/css" href="/js/dojo/dojox/grid/enhanced/resources/EnhancedGrid_rtl.css" />
        <link rel="stylesheet" type="text/css" href="/js/dojo/dojox/grid/enhanced/resources/claro/EnhancedGrid.css" />
        <style type="text/css" >
            root,html,body {
                height: 100%;
                margin:  0px;
                padding:  0px;
            }

            #gridElement {
                height: 100%;
            }
        </style>
        <script type="text/javascript" src="/js/dojo/dojo/dojo.js" ></script>
        <script type="text/javascript" src="/js/azfcms/azfcms.js"></script>
        <script type="text/javascript">
            dojo.require("dojox.data.JsonRestStore");
            dojo.require("dojox.grid.EnhancedGrid");
            dojo.require("dojox.grid.EnhancedGrid");
            dojo.require("dojox.grid.enhanced.plugins.DnD");
            dojo.require("dojox.grid.enhanced.plugins.NestedSorting");
            dojo.require("dojox.grid.enhanced.plugins.IndirectSelection");
            dojo.require("dojox.grid.enhanced.plugins.Filter");
            
            var rest;
            dojo.addOnLoad(function(){
                rest = new dojox.data.JsonRestStore({target:"/json-rest.php/user/user",idAttribute:"id"});
                
                gridLayout = [
                    { name: 'First name', field: 'firstName', editable: false},
                    { name: 'Last name', field: 'lastName'},
                    { name: 'Id', field: 'id'}];
                var grid = new dojox.grid.EnhancedGrid({
                    store: rest,
                    structure: gridLayout,
                    plugins:{
                        dnd:true,
                        nestedSorting:true,
                        indirectSelection: true,
                        filter: {
                            isServerSide: true,
                            setupFilterQuery: function(commands, request){
                                if(commands.filter && commands.enable){
                                    var key, value;
                                    request.query = {
                                            
                                    };
                                    if(commands.filter.data.op == 'string'){
                                        key = commands.filter.data[0].data +":" + commands.filter.op;
                                        value = commands.filter.data[1].data;
                                        request.query[key] = value;
                                    }else {
                                        
                                        for(var index in commands.filter.data){
                                            var op = commands.filter.data[index].op;
                                            if(op == "not"){
                                                var data = commands.filter.data[index].data[0].data;
                                                op = data.op+op;
                                            } else {
                                                var data = commands.filter.data[index].data;
                                            }
                                            
                                            key = data[0].data + ":" + op;
                                            value = data[1].data;
                                            request.query[key] = value;
                                        }
                                    }
                                    
                                    
                                }
                            }
                        }
                    }
                }, dojo.byId("gridElement"));
                grid.startup();
            })
        </script>
    </head>
    <body class="claro">
        <div  id="gridElement">

        </div>
    </body>
</html>
