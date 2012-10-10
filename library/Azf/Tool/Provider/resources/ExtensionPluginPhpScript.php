<?="<?php"?>

/**
 * Description of <?=$ucName?>
 *
 * @author antun
 */
class <?=strtolower($module)=="default"?"Application":$ucModule?>_Plugin_Extension_<?=$ucName?> extends Azf_Plugin_Extension_Abstract {

    public function render() {
        $this->renderTemplate("index");
    }

    public function setUp() {
        $this->setParam("body", "");
    }

    public function tearDown() {
        
    }

}

