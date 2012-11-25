define([
	"dojo/_base/declare", // declare
	"dojo/dom-style", // domStyle.getComputedStyle
	"dojo/_base/kernel", // kernel.experimental
	"dojo/_base/lang", // lang.hitch
	"dijit/_editor/_Plugin",
	"dijit/form/DropDownButton",
        'dojo/dom-attr',
        'dojo/dom-class',
        'azfcms/module/default/GeneratorInvoker',
        'dijit/DropDownMenu',
        'dijit/MenuItem'
], function(declare, domStyle, kernel, lang, _Plugin, Button, domAttr, domClass, GeneratorInvoker,
Menu,MenuItem){

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


		// Override _Plugin.buttonClass to use a ToggleButton for this plugin rather than a vanilla Button
		buttonClass: Button,

		_initButton: function(){
                    var self = this;
                    // Override _Plugin._initButton() to setup handler for button click events.
                    this.dropDown = new Menu();
                    this.dropDown.addChild(new MenuItem({
                        label:"Poveznica",
                        onClick:function(){
                            GeneratorInvoker.invoke(function(result){
                                self.editor.execCommand('insertHTML',result);
                            },'htmlLink')
                        }
                    }))
                    this.dropDown.addChild(new MenuItem({
                        label:"Slika",
                        onClick:function(){
                            GeneratorInvoker.invoke(function(result){
                                self.editor.execCommand('insertHTML',result);
                            },'htmlImage')
                        }
                    }))
                    this.inherited(arguments);
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
