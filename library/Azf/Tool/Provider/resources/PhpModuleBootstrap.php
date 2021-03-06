<?="<?php"?>

/**
 * Description of Bootstrap
 *
 * @author antun
 */
class <?=$ucModule?>_Bootstrap extends Azf_Bootstrap_Module{
    public function _initLoader() {
        $loader = new Zend_Loader_Autoloader_Resource(array(
            'basePath'=>APPLICATION_PATH.'/modules/<?=strtolower($module)?>',
            'namespace'=>'<?=$ucModule?>'
        ));
        $loader->addResourceType('plugin', 'plugins', 'Plugin');
        $loader->addResourceType('rests', 'rests', 'Rest');
        $loader->addResourceType('models', 'models', 'Model');
        $loader->addResourceType('filters', 'filters', 'Filter');
        $loader->addResourceType('resolvers', 'resolvers', 'Resolver');
    }
}

