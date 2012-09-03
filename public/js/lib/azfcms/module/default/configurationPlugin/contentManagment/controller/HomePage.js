define(
    ['dojo/_base/declare','azfcms/model/cms',
        
    'azfcms/model/navigation','azfcms/view/util',
    'dojo/i18n!azfcms/resources/i18n/cms/common/nls/common'],function
    (declare,  cms,
        navigationModel, util,
        nls)
        {
        var _class = declare([],{
        
            constructor: function(args){
                if(!args){
                    args = {}
                }
                /**
                 * Selected node reference
                 */
                this.node = null;
                
                for(var name in nls){
                    if(name.indexOf("homp")==0){
                        this[name] = nls[name];
                    }
                }
                
                if(!args.util){
                    this.util = util;
                } else {
                    this.util = args.util;
                }
                
                if(!args.model){
                    this.model = cms;
                } else {
                    this.model = args.model;
                }
                
                if(!args.navigationModel){
                    this.navigationModel = navigationModel;
                } else {
                    this.navigationModel = args.navigationModel;
                }
            },
            
            onHomeChange:function(node){
                if(!node)
                    return;
                var self = this;
                this.util.confirm(function(confirmed){
                    if(!confirmed)
                        return;
                    
                    self.model.setHomePage(node.id).then(function(navigationNodes){
                        var oldHomePage = navigationNodes[0];
                        var newHomePage = navigationNodes[1];
                        
                        self.navigationModel.onChange(oldHomePage);
                        self.navigationModel.onChange(newHomePage);
                    })
                },this.hompHomePageMessage);
            }
        });
    
        return _class;
    })