define(['dojo/_base/declare','azfcms/module/default/AbstractGenerator','dijit/_WidgetBase',
'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dojo/text!./templates/NavigationGenerator',
'azfcms/model/navigation',
'dijit/Tree',
'dojo/dom-geometry',
'dojo/topic',
'dojo/_base/lang'


],
function(declare,AbstractGenerator, _WidgetBase,
_TemplatedMixin, _WidgetsInTemplate,templateString,
navigationStore, Tree, domGeometry, topic, lang){
    return declare([AbstractGenerator,_WidgetBase, _TemplatedMixin, _WidgetsInTemplate],{
        templateString:templateString,
        title:"Stranice",
        postCreate:function(){
            this.inherited(arguments);
            this._initTree();
            this.resize();
        },
        _initTree:function(){
            this.tree = new Tree({
                model:navigationStore,
                persist:false
            },this.treeDomNode);
            
            
          this.tree.on("click",lang.hitch(this,function(item){
              this.setSelection(item);
          })) ;
          
        },
        setSelection:function(item){
            this._selection = item;
        },
        getSelection:function(){
            return this._selection;
        },
        resize:function(){
            this.tree.resize(domGeometry.getContentBox(this.domNode));
        },
        _initView:function(){
            this.setView(this)
        },
        _initMetadata:function(){
            this.name = "DefaultNavigation"
            this.services = [
                'html'
            ]
        },
        buildHtml:function(promise){
            var node;
            if(node = this.getSelection()){
                var href = '/'+decodeURI(node.title)+'/'+node.id+'.html';
                promise.resolve('<a href="'+href+'">'+node.title+'</a>');
            }
            
        }
    })
})

