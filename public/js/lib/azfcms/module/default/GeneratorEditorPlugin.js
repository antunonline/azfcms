define([
	"dojo/_base/declare", // declare
	"dojo/dom-style", // domStyle.getComputedStyle
	"dojo/_base/kernel", // kernel.experimental
	"dojo/_base/lang", // lang.hitch
	"dijit/_editor/_Plugin",
	"dijit/form/Button",
        'dojo/dom-attr',
        'dojo/dom-class',
        'azfcms/module/default/GeneratorInvoker'
], function(declare, domStyle, kernel, lang, _Plugin, Button, domAttr, domClass, GeneratorInvoker){

	// module:
	//		dijit/_editor/plugins/ToggleDir

	kernel.experimental("dijit._editor.plugins.ToggleDir");
        
	var GeneratorPlugin = declare("azfcms.module.default.GeneratorEditorPlugin", _Plugin, {
		// summary:
		//		This plugin is used to toggle direction of the edited document,
		//		independent of what direction the whole page is.

		// Override _Plugin.useDefaultCommand: processing is done in this plugin
		// rather than by sending commands to the Editor
		useDefaultCommand: false,

		command: "generator",

		// Override _Plugin.buttonClass to use a ToggleButton for this plugin rather than a vanilla Button
		buttonClass: Button,

		_initButton: function(){
                    // Override _Plugin._initButton() to setup handler for button click events.
                    this.inherited(arguments);
                    this.button.on("click",lang.hitch(this,function(){
                        GeneratorInvoker.invoke(lang.hitch(this,function(result){
                            this.editor.execCommand('insertHTML', result);
                        }),'html');
                    }))
                    domClass.add(this.button.iconNode,'dijitEditorIconInsertImage');
                    
                    
                    
                    this._connectTagEvents();
		},
                getLabel:function(){
                    return "Dodaj objekt"
                },
                _connectTagEvents: function(){
                        // summary:
                        //		Over-ridable function that connects tag specific events.
                        this.editor.onLoadDeferred.then(lang.hitch(this, function(){
                                this.connect(this.editor.editNode, "ondblclick", this._onDblClick);
                        }));
                },
                _onDblClick:function(e){
                    if(e && e.target){
                        var t = e.target;
                        if(domAttr.get(t,'data-dojo-widget-name')){
                            GeneratorInvoker.invoke();
                        }
                    }
                    
                },


		updateState: function(){
		}
	});

	// Register this plugin.
	_Plugin.registry["generator"] = function(){
		return new GeneratorPlugin({command: "generator"});
	};

	return GeneratorPlugin;
});
