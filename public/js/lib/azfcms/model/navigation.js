define(['azfcms/model','dojo/_base/lang'],function(model,lang){
    var preparedJsonStore = model.prepareRestStore("navigation","default");
    
    return lang.mixin(preparedJsonStore,{
        getRoot: function(onComplete, onError){
            this.get("1").then(onComplete, onError);
        },
            
        mayHaveChildren: function(item){
            return true;
        },
        
        getChildren: function(parentItem, onComplete){
            onComplete(parentItem.childNodes);
        },
        
        
        isItem: function(something){
            if(something.id){
                return true;
            } else {
                return false;
            }
        },
        
        getIdentity: function(item){
            return item.id;
        },
        
        getLabel: function(item){
            return item.url;
        }
    });
    
    
})
