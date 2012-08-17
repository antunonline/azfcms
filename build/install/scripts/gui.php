<?php 
if(defined("DEFINE_BUILD_DEPS")){
    /* @var $this InstallScriptBuilder */
    $this->addResource("dojo",  file_get_contents(__DIR__."/../../resources/dojo.js"));
    return;
}

$install[] = function(){
?>
<html>
    <head>
        <title>AZFCMS Installer Script</title>
        <script type="text/javascript">
            var dojoConfig = {
                async:true
            }
        </script>
        <script type="text/javascript" src="<?= basename(__FILE__)."?action=resource&name=dojo" ?>"></script>
        <script type="text/javascript">
            require(['dojo/ready','dojo/request/xhr','dojo/dom-construct'],function(ready,xhr,domConstruct){
                ready(function(){
                    function doRequest(){
                        xhr('<?= basename(__FILE__) ?>?action=ajax').then(function(result){
                            if(result.length){
                                domConstruct.place("<div>"+result+"</div>",document.body);
                                doRequest();
                            } else {
                                domConstruct.place("<div>Done!</div>",document.body);
                            }
                        })
                    }
                    doRequest();
                })
            })
        </script>
    </head>
    <body>
        <div>Starting install script</div>
        
    </body>
</html>
<?php 
}
?>
