define(['dojo/_base/declare','azfcms/module/default/AbstractGenerator','dijit/_WidgetBase',
'dijit/_TemplatedMixin','dijit/_WidgetsInTemplateMixin','dojo/text!./templates/<?=$ucName?>Generator'


],
function(declare,AbstractGenerator, _WidgetBase,
_TemplatedMixin, _WidgetsInTemplate,templateString){
    return declare([AbstractGenerator,_WidgetBase, _TemplatedMixin, _WidgetsInTemplate],{
        templateString:templateString,
        title:"<?=$ucName?>",
        postCreate:function(){
            this.inherited(arguments);
        },
        resize:function(){
            
        },
        _initView:function(){
            this.setView(this)
        },
        _initMetadata:function(){
            this.name = "<?=$ucModule?><?=$ucName?>"
            this.services = [
                'html'
            ]
        },
        buildHtml:function(promise){
            promise.resolve('Hello <?=$ucModule?><?=$ucName?>Generator');
        }
    })
})

