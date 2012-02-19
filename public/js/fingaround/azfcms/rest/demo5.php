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
        <script type="text/javascript" src="/js/dojo/dojo/dojo.js" ></script>
        <script type="text/javascript" src="/js/azfcms/azfcms.js"></script>
        <script type="text/javascript">
            var rest;
            dojo.addOnLoad(function(){
               
            })
            
            var send  = function (){
                azfcms.model.callRest("get",document.forms.lookup.uid.value,null,"sessionStore","user")
                .addCallback(function(r){
                    var form = document.forms.user;
                    form.firstName.value = r.firstName;
                    form.lastName.value = r.lastName;
                });
            }
        </script>
    </head>
    <body class="claro">
        
        <form name="lookup">
            <label>Lookup id: <input name="uid" type="text"  /></label><Input type="button" onclick="send()" value="Find" />
        </form>
        
            <form name="user">
                <fieldset  title="User">
                    <legend>User</legend>
                <label  for="firstName">First name: <input type="text" id="firstName" name="firstName" /> </label>
                <label  for="lastName">First name: <input type="text" id="lastName" name="lastName" /> </label>
                </fieldset>
            </form>
    </body>
</html>
