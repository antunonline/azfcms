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
                                    _getChildNodeIndex: function(child,parent){
                                        var id = child.id;
                                        for(var i = 0; i < parent.childNodes.length;i++){
                                            if(id==parent.childNodes[i].id)
                                                return i;
                                        }
                                        
                                        return -1;
                                    },
                                    pasteItem: function(childItem, oldParentItem, newParentItem, bCopy, newChildIndex){
                                        // Calculate old parent child index
                                        var oldChildIndex = this._getChildNodeIndex(childItem,oldParentItem);
                                        
                                        // Remove child from the old position
                                        oldParentItem.childNodes.splice(oldChildIndex, 1);
                                        
                                        // Insert child node into new place
                                        newParentItem.childNodes.splice(newChildIndex,0,childItem);
                                        
                                        // If parent is the same, and only ordering is updated
                                        if(oldParentItem.id==newParentItem.id){
                                            // If new and old parents are the same
                                            
                                        } else {
                                            // If parents differ
                                            this.onChildrenChange(oldParentItem,oldParentItem.childNodes);
                                        }
                                        
                                        
                                        
                                        // Trigger Tree UI changes
                                        this.onChange(newParentItem);
                                        this.onChildrenChange(newParentItem,newParentItem.childNodes);
                                    },
                                    newItem: function(args, parent, insertIndex){
                                        console.debug("newItem parent: "+ parent.url);
                                    },
                                    onChange: function(item){
                                        
                                    }, 
                                    onChildrenChange: function(parent, newChildrenList){
                                        
                                    }
                                    
                                });
                                
var tree = new Tree({model:new Observable(store),dndController: dndSource,
                                style:"width:400px;",betweenThreshold:5},'tree');
                                tree.startup();
			});
                        
                        
		</script>
	</head>
	<body class="claro">
		
		<div id="tree"></div>
	</body>
</html>
