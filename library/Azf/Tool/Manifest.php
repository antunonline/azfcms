<?php

require_once 'Provider/ExtensionPlugin.php';
require_once 'Provider/AzfModule.php';
require_once 'Provider/ContentPlugin.php';
require_once 'Provider/Configuration.php';
require_once 'Provider/ServerStore.php';

class Azf_Tool_Manifest implements Zend_Tool_Framework_Manifest_ProviderManifestable {
    public function getProviders() {
        return array(
            new Azf_Tool_Provider_ExtensionPlugin(),
            new Azf_Tool_Provider_AzfModule(),
            new Azf_Tool_Provider_ContentPlugin(),
            new Azf_Tool_Provider_Configuration(),
            new Azf_Tool_Provider_ServerStore()
        );
    }
}
