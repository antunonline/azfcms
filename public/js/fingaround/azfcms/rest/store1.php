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
                height: 100%;
            }
        </style>
        <script type="text/javascript" src="/js/dojo/dojo/dojo.js" ></script>
        <script type="text/javascript" src="/js/azfcms/azfcms.js"></script>
        <script type="text/javascript">
            dojo.require("dojox.data.JsonRestStore");
            dojo.require("dojox.grid.DataGrid");
            var rest;
            dojo.addOnLoad(function(){
                rest = new dojox.data.JsonRestStore({target:"/json-rest.php/user/user",idAttribute:"id"});
                
                gridLayout = [
                    { name: 'First name', field: 'firstName', editable: false},
                    { name: 'Last name', field: 'lastName'},
                    { name: 'Id', field: 'id'}];
                var grid = new dojox.grid.DataGrid({
                    store: rest,
                    structure: gridLayout
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
