define(['azfcms/model','dojo/_base/lang','dojo/_base/Deferred'],function(model,lang,Deferred){
    var preparedJsonStore = model.prepareRestStore("navigation","default");
    
    return lang.mixin(preparedJsonStore,{
        getRoot: function(onComplete, onError){
            this.get("1").then(onComplete, onError);
        },
            
        mayHaveChildren: function(item){
            return true;
        },
        
        /**
         * Return array of child nodes
         * @return {Array}
         */
        getChildren: function(parentItem, onComplete){
            onComplete(parentItem.childNodes);
        },
        
        
        /**
         * @return {Boolean}
         */
        isItem: function(something){
            if(something.id){
                return true;
            } else {
                return false;
            }
        },
        
        /**
         * Return item identifier
         * @return {mixed}
         */
        getIdentity: function(item){
            return item.id;
        },
        
        /**
         * Get Item label
         * @return {String} 
         */
        getLabel: function(item){
            return item.title;
        },
        
        /**
         * @param {Object} childItem
         * @param {Object} oldParentItem
         * @param {Object} newParentItem
         * @param {Boolean} bCopy
         * @param {Number} index
         */
        pasteItem: function(childItem, oldParentItem, newParentItem, bCopy, index){
            var navigationModel = this;
            var updateServer;
            
            // Remove child from old parent
            this._removeChildFromParentItem(childItem,oldParentItem);
            
            // If childItem is not placed at specific place, but is dropped into some parent node directly
            if(typeof index == 'undefined'){
                // Move current child into the end of the new parent
                newParentItem.childNodes[newParentItem.childNodes.length] = childItem;
                // Push changes to the server
                updateServer = function(){
                    return navigationModel.moveInto(childItem.id, newParentItem.id);
                }
            // If child node is set to the exact position in tree, 
            } else {
                var atId=this._getBeforeChildId(newParentItem,index);
                
                if(atId != null) {
                    updateServer = function(){
                        return navigationModel.moveBefore(childItem.id,atId);
                    }
                } else {
                    atId=this._getAfterChildId(newParentItem,index);
                    updateServer = function(){
                        return navigationModel.moveAfter(childItem.id,atId);
                    }
                }
                // Update new parent
                newParentItem.childNodes.splice(index,0,childItem);
            }
            
            // Push updates to the server, and when that is done
            // update tree widget
            updateServer().then(function(){
                // Update tree model
                navigationModel.onChildrenChange(oldParentItem,oldParentItem.childNodes);
                navigationModel.onChildrenChange(newParentItem,newParentItem.childNodes);
            });
            
            
        },
        
        /**
         * @param {Object} childItem
         * @param {Object} parentItem
         */
        _removeChildFromParentItem: function(childItem, parentItem){
            var childNodes = parentItem.childNodes;
            var childId = childItem.id;
            
            // Iterate ove child nodes
            for(var i in childNodes){
                if(childNodes[i].id == childId){
                    childNodes.splice(i,1);
                    return
                }
            }
        },
        
        /**
         * @param {Object} newParentItem
         * @param {Number} newParentItem
         * @return {Number|null}
         */
        _getBeforeChildId: function(newParentItem,index){
            var childNodes = newParentItem.childNodes;
            
            // If there is a child node before given index
            if(index<childNodes.length){
                return childNodes[index].id;
            } else {
                return null;
            }
        },
        
        /**
         * @param {Object} newParentItem
         * @param {Number} newParentItem
         * @return {Number|null}
         */
        _getAfterChildId: function(newParentItem, index){
            var childNodes = newParentItem.childNodes;
            
            if(index>0){
                return childNodes[index-1].id;
            } else {
                return null;
            }
        },
        
        /**
         *
         * @return {dojo.Deferred}
         */
        getNodeParams: function(nodeId){
            var expr = [
            '[',
            'cms.navigation.getStaticParams('+nodeId+'),',
            'cms.navigation.getDynamicParams('+nodeId+')',
            ']'
            ];
            
            return model.invoke(expr.join(""));
        },
        
        /**
         * Set static parameters on node identified by
         * nodeId
         * @param {number} nodeId
         * @param {Object} params - Key/Value pairs
         */
        setStaticParams: function(nodeId, params){
            // Create calls
            var calls = this._constructSetStatic(nodeId,params);
            // Wrap calls in array
            var invoke = ['[',calls,']'].join("");
            // Call server
            return model.invoke(invoke);
        },
        
        /**
         * Set dynamic parameters on node identified by
         * nodeId
         * @param {number} nodeId
         * @param {Object} params - Key/Value pairs
         */
        setDynamicParams: function(nodeId, params){
            
            // Create calls
            var calls = this._constructSetDynamic(nodeId,params);
            // Wrap calls in array
            var invoke = ['[',calls,']'].join("");
            // Call server
            return model.invoke(invoke);
        },
        
        /**
         * Construct set static param calls
         * @param {string} nodeId
         * @param {Object} params
         */
        _constructSetStatic: function(nodeId,params){
            var expr = [];
            
            var tmp = "";
            for(var key in params){
                tmp = "cms.navigation.setStaticParam("+nodeId+",'"+key+"','"+params[key]+"')"
                expr[expr.length]=tmp;
            }
            return expr.join(",");
        },
        
        /**
         * Construct set static param calls
         * @param {string} nodeId
         * @param {Object} params
         */
        _constructSetDynamic: function(nodeId,params){
            var expr = [];
            
            var tmp = "";
            for(var key in params){
                tmp = "cms.navigation.setDynamicParam("+nodeId+",'"+key+"','"+params[key]+"')"
                expr[expr.length]=tmp;
            }
            return expr.join(",");
        },
        /**
         * Set static and dynamic params
         * @param {string} nodeId
         * @param {Object} staticParams
         * @param {Object} dynamicParams
         * @return {dojo.Deferred}
         */
        setParams: function(nodeId, staticParams, dynamicParams){
            
            var staticCalls = this._constructSetStatic(nodeId,staticParams);
            var dynamicCalls = this._constructSetDynamic(nodeId,dynamicParams);
            
            var calls = [staticCalls,dynamicCalls].join(",");
            var invoke = ['[',calls,']'].join("");
            
            return model.invoke(invoke);
        },
        
        /**
         * @param {Number} node
         * @param {Number} before
         * @return {dojo.Deferred}
         */
        moveBefore:function(node, before){
            var call = ['cms.navigation.moveBefore(',node,',',before,')'].join('');
            return model.invoke(call);
        },
        
        /**
         * @param {Number} node
         * @param {Number} after
         * @return {dojo.Deferred}
         */
        moveAfter: function(node, after){
            var call = ['cms.navigation.moveAfter(',node,',',after,')'].join('');
            return model.invoke(call);
        },
        
        /**
         * @param {Number} node
         * @param {Number} into
         * @return {dojo.Deferred}
         */
        moveInto: function(node, into){
            var call = ['cms.navigation.moveInto(',node,',',into,')'].join('');
            return model.invoke(call);
        },
        
        
        insertInto: function(id,title,type){
            var context = this;
            var call = ['[cms.navigation.insertInto(',id,',{\'title\':\'',title,'\'},\''+type+'\'),',
            'cms.navigation.getBranch(',id,')]'].join('');
            return model.invoke(call).then(function(args){
                context.onChildrenChange(args[1],args[1].childNodes);
            });
        },
        
        
        /**
         * @param {Number} node
         * @param {String} title
         * @return {dojo.Deferred}
         */
        setTitle: function(node,title){
            var call = ['cms.navigation.setTitle(',node,',\'',title,'\')'].join("");
            return model.invoke(call);
        },
        
        
        /**
         * @param {Number} node
         * @param {String} url
         * @return {dojo.Deferred}
         */
        setUrl: function(node,url){
            var call = ['cms.navigation.setUrl(',node,',\'',url,'\')'].join("");
            return model.invoke(call);
        },
        
        
        /**
         * @param {Number} ndoe
         * @param {String} title
         * @param {String} description
         * @param {String} keywords
         */
        setMetaValues: function(node, title, description, keywords){
            var call = [
                '[',
                'cms.navigation.setTitle(',node,',\'',title,'\'),',
                'cms.navigation.setDynamicParam(',node,',\'metaDescription\',\'',description,'\'),',
                'cms.navigation.setDynamicParam(',node,',\'metaKeywords\',\'',keywords,'\'),',
                'cms.navigation.getBranch('+node+")",
                ']'
            ].join("");
            
            var response =  model.invoke(call);
            var self = this;
            
            response.then(function(args){
                var node = args[3];
                self.onChange(node);
            })
            return response;
        },
        
        
        // =======================================================================
        // Callbacks

        onChange: function(item){
        // summary:
        //		Callback whenever an item has changed, so that Tree
        //		can update the label, icon, etc.   Note that changes
        //		to an item's children or parent(s) will trigger an
        //		onChildrenChange() so you can ignore those changes here.
        // item: dojo.data.Item
        // tags:
        //		callback
        },

        onChildrenChange: function(parent, newChildrenList){
        // summary:
        //		Callback to do notifications about new, updated, or deleted items.
        // parent: dojo.data.Item
        // newChildrenList: dojo.data.Item[]
        // tags:
        //		callback
        }
        
        
        
        
    });
    
    
})
