define(['azfcms/model','dojo/_base/lang','dojo/_base/Deferred',
    'dojo/_base/declare','dojo/_base/json'],function(model,lang,Deferred,
    declare,json){
    
    
        
        /**
         * SIngle quote escape
         */
        var _s = function(value){
            return value.replace(/'/g,"\\'");
        };
        
        
        /**
         * SIngle quote escape
         */
        var _d = function(value){
            return value.replace(/"/g,"\\\"");
        };
    
    var _class = declare(null,{
        constructor: function(args){
            if(typeof args == 'undefined')
                args = {};
            lang.mixin(this,args);
            
            if("model" in args == false){
                this.model = model;
            } 
            
            var preparedJsonStore = this.model.prepareRestStore("navigation","default");
            var remove = this.remove;
            lang.mixin(this,preparedJsonStore);
            this.remove = remove;
        },
        
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
            if(parentItem.childNodes){
                onComplete(parentItem.childNodes);
            } else {
                this.get(parentItem.id).then(function(item){
                    onComplete(item.childNodes);
                });
            }
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
            
            return parseInt(item.home)?item.title+" [Home Page]":item.title;
        },
        
        /**
         * @param {Object} childItem
         * @param {Object} oldParentItem
         * @param {Object} newParentItem
         * @param {Boolean} bCopy
         * @param {Number} index
         */
        pasteItem: function(childItem, oldParentItem, newParentItem, bCopy, index){
            this.getChildNodes([oldParentItem.id,newParentItem.id]).then(lang.hitch(this,function(results){
                this._pasteItem(childItem,results[0],results[1],bCopy,index);
            }))
        },
        
        /**
         * @param {Object} childItem
         * @param {Object} oldParentItem
         * @param {Object} newParentItem
         * @param {Boolean} bCopy
         * @param {Number} index
         */
        _pasteItem:function(childItem, oldParentItem, newParentItem, bCopy, index){
            var self = this;
            var updateServer;
            
            // Remove child from old parent
            this._removeChildFromParentItem(childItem,oldParentItem);
            
            // If childItem is not placed at specific place, but is dropped into some parent node directly
            if(typeof index == 'undefined'){
                // Move current child into the end of the new parent
                newParentItem.childNodes[newParentItem.childNodes.length] = childItem;
                // Push changes to the server
                updateServer = function(){
                    return self.moveInto(childItem.id, newParentItem.id);
                }
            // If child node is set to the exact position in tree, 
            } else {
                var atId=this._getBeforeChildId(newParentItem,index);
                
                if(atId != null) {
                    updateServer = function(){
                        return self.moveBefore(childItem.id,atId);
                    }
                } else {
                    atId=this._getAfterChildId(newParentItem,index);
                    updateServer = function(){
                        return self.moveAfter(childItem.id,atId);
                    }
                }
                // Update new parent
                newParentItem.childNodes.splice(index,0,childItem);
            }
            
            // Push updates to the server, and when that is done
            // update tree widget
            updateServer().then(function(){
                // Update tree model
                self.onChildrenChange(oldParentItem,oldParentItem.childNodes);
                self.onChildrenChange(newParentItem,newParentItem.childNodes);
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
         * @param {Array} forNodeIds An example would be [1,2,3]
         * @return {Deferred}
         */
        getChildNodes:function(forNodeIds){
            return this.model.singleInvoke('cms.navigation.getChildNodes',[forNodeIds]);
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
            
            return this.model.invoke(expr.join(""));
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
            return this.model.invoke(invoke);
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
            return this.model.invoke(invoke);
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
                tmp = "cms.navigation.setStaticParam("+nodeId+",'"+_s(key)+"','"+_s(params[key])+"')"
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
                tmp = "cms.navigation.setDynamicParam("+nodeId+",'"+_s(key)+"','"+_s(params[key])+"')"
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
            var calls = [];
            
            if(staticCalls.length){
                calls.push(staticCalls);
            }
            if(dynamicCalls.length){
                calls.push(dynamicCalls);
            }
            
            calls = calls.join(",");
            var invoke = ['[',calls,']'].join("");
            
            return this.model.invoke(invoke);
        },
        
        /**
         * @param {Number} node
         * @param {Number} before
         * @return {dojo.Deferred}
         */
        moveBefore:function(node, before){
            var call = ['cms.navigation.moveBefore(',node,',',before,')'].join('');
            return this.model.invoke(call);
        },
        
        /**
         * @param {Number} node
         * @param {Number} after
         * @return {dojo.Deferred}
         */
        moveAfter: function(node, after){
            var call = ['cms.navigation.moveAfter(',node,',',after,')'].join('');
            return this.model.invoke(call);
        },
        
        /**
         * @param {Number} node
         * @param {Number} into
         * @return {dojo.Deferred}
         */
        moveInto: function(node, into){
            var call = ['cms.navigation.moveInto(',node,',',into,')'].join('');
            return this.model.invoke(call);
        },
        
        
        /**
         * @param {Number} id
         * @param {String} title
         * @param {String} type
         * @return {dojo.Deferred}
         */
        insertInto: function(id,title,type){
            var context = this;
            var call = ['[cms.navigation.insertInto(',id,',{\'title\':\'',_s(title),'\'},\''+_s(type)+'\'),',
            'cms.navigation.getBranch(',id,')]'].join('');
            return this.model.invoke(call).then(function(args){
                context.onChildrenChange(args[1],args[1].childNodes);
            });
        },


        /**
         * @param {object|Number} item
         * @return {dojo.Deferred}
         */
        remove: function(item){
            if(typeof item == 'object'){
                item = item.id;
            }
            var self = this;
            var call = ['cms.navigation.deleteNode(',item,")"].join('');
            var response = this.model.invoke(call);
            response.then(function(response){
                self.onChildrenChange(response,response.childNodes);
            })
            
            return response;
        },
        
        
        
        /**
         * @param {Number} node
         * @param {String} title
         * @return {dojo.Deferred}
         */
        setTitle: function(node,title){
            var call = ['cms.navigation.setTitle(',node,',\'',_s(title),'\')'].join("");
            return this.model.invoke(call);
        },
        
        
        /**
         * @param {Number} node
         * @param {String} url
         * @return {dojo.Deferred}
         */
        setUrl: function(node,url){
            var call = ['cms.navigation.setUrl(',node,',\'',_s(url),'\')'].join("");
            return this.model.invoke(call);
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
                'cms.navigation.setDynamicParam(',node,',\'metaDescription\',\'',_s(description),'\'),',
                'cms.navigation.setDynamicParam(',node,',\'metaKeywords\',\'',_s(keywords),'\'),',
                'cms.navigation.getBranch('+node+")",
                ']'
            ].join("");
            
            var response =  this.model.invoke(call);
            var self = this;
            
            response.then(function(args){
                var node = args[3];
                self.onChange(node);
            })
            return response;
        },
        
        setContent: function(id, key, content){
            var call = this.model.createCall('cms.navigation.setContent',[id,key,content]);
            return this.model.invoke(call);
        },
        
        
        getContent: function(id,key){
            var call = this.model.createCall('cms.navigation.getContent',[id,key]);
            return this.model.invoke(call);
        },
        
        changePageType : function(nodeId,newType){
            return this.model.singleInvoke("cms.navigation.changePageType",[nodeId,newType]);
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
    
    var instance = new _class();
    instance._class = _class;
    
    return instance;
    
})
