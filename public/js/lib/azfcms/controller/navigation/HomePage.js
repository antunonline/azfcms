define(
    ['dojo/_base/declare','azfcms/model/cms',
        
    'azfcms/model/navigation','azfcms/view/util',
    'dojo/i18n!azfcms/resources/nls/view'],function
    (declare,  cms,
        navigationModel, util,
        nls)
        {
        var _class = declare([],{
        
            constructor: function(){
                /**
                 * Selected node reference
                 */
                this.node = null;
                
                for(var name in nls){
                    if(name.indexOf("homp")==0){
                        this[name] = nls[name];
                    }
                }
            },
            
            onHomeChange:function(node){
                if(!node)
                    return;
                
                util.confirm(function(confirmed){
                    if(!confirmed)
                        return;
                    
                    cms.setHomePage(node.id).then(function(navigationNodes){
                        var oldHomePage = navigationNodes[0];
                        var newHomePage = navigationNodes[1];
                        
                        navigationModel.onChange(oldHomePage);
                        navigationModel.onChange(newHomePage);
                    })
                },this.hompHomePageMessage);
            }
        });
    
        return _class;
    })