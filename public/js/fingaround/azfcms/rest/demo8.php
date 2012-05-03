<!DOCTYPE HTML>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<title>Demo: Connecting Tree to a Store</title>
		<link rel="stylesheet" href="/js/dojo/dijit/themes/claro/claro.css" media="screen">
		<!-- load dojo and provide config via data attribute -->
<!--		<script src="/dojo/dojo.js"
				data-dojo-config="isDebug: true,parseOnLoad: true">
		</script>-->
		<script src="/js/dojo/dojo/dojo.js"
			data-dojo-config="isDebug: true, async: true">
		</script>
		<script>
			
			require(["dojo/store/JsonRest", "dojo/store/Observable", "dijit/Tree", "dijit/tree/dndSource", "dojo/query", "dojo/domReady!"], function(JsonRest, Observable, Tree, dndSource, query) {
				
				var store = new JsonRest({
                                    target:"/json-rest.php/default/navigation/",
                                    getRoot: function(onItem, onError){
                                        this.get(1).then(onItem,onError);
                                    },
                                    mayHaveChildren: function(item){
                                        return true;
                                    },
                                    getChildren: function(item, onComplete){
                                        onComplete(item.childNodes);
                                    },
                                    getIdentity: function(item){
                                        return item.id;
                                    },
                                    getLabel: function(item){
                                        return item.url;
                                    },
                                    pasteItem: function(childItem, oldParentItem, newParentItem, bCopy){
                                        console.debug("child: "+ childItem.url);
                                        console.debug("oldParent: "+oldParentItem.url)
                                        console.debug("newParent: " +newParentItem.url)
                                        console.debug(arguments[4])
                                    },
                                    newItem: function(args, parent, insertIndex){
                                        console.debug("newItem parent: "+ parent.url);
                                    },
                                    put: function(){
                                        
                                    }
                                    
                                });
                                
                                var tree = new Tree({model:store,dndController: dndSource,
                                style:"width:400px;",betweenThreshold:5},'tree');
                                tree.startup();
			});
                        
                        
		</script>
	</head>
	<body class="claro">
		
		<div id="tree"></div>
	</body>
</html>
