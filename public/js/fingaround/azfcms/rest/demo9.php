<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Demo: Connecting Tree to a Store</title>
		<link rel="stylesheet" href="/js/lib/dijit/themes/claro/claro.css" media="screen">
		<!-- load dojo and provide config via data attribute -->
<!--		<script src="/dojo/dojo.js"
				data-dojo-config="isDebug: true,parseOnLoad: true">
		</script>-->
		<script src="/js/lib/dojo/dojo.js"
			data-dojo-config="isDebug: true, async: true">
		</script>
		<script>
			
			function evaluate(){
                            var expr = document.getElementById("expr").value;
                            require(['azfcms/model/lang!'+expr],function(result){
                                console.debug("CLICK");
                                document.getElementById("result").innerHTML = result;
                            })
                        }
                        
                        require(["dojo/ready","dojo/on","dojo/_base/connect"],function(ready,on,connect){
                            on(document.getElementById("initiator"),'click',evaluate);
//                            connect(document.getElementById("initiator"),'onClick',evaluate);
                        })
                        
                        
		</script>
	</head>
	<body class="claro">
		
            <input id="expr" type="text" />
            <button id="initiator" >Test</button>
            <div id="result">
                
            </div>
                
                
	</body>
</html>
