<?php

/**
 * TODO 12.05.2012
 * 
 * 1. Create API that will expose capabilities of present templates. Also
 *      create a web service that will expose these capabilities to client side programs.
 * 2. Implement template autoconfiguration, which will setup appropriate template
 *      in postRoute hook.
 * 3. Create client side model for server side navigational model.
 * 4. 
 * 
 */

/**
 * TODO 12.05.2012
 * 
 * @task Implement ACL checks in resolver classes 
 * @task Test auto-resolver implementation, no tests have been run against non-default modules
 */


/**************************************************************/
/**************************************************************/
/********************DONE**************************************/
/**************************************************************/
/**************************************************************/


/**
 * @done 12.05.2012 23:12
 *Create I18N service that can be used to easily translate client side programs to 
 *      other languages. 
 */

/**  
 * @DateStartedWriting 12.05.2012
 * @TimeStartedWriting 18:39
 * @TimeEndedWriting 22:49
 * @Title Refactoring json-lang service resolver implementation
 * 
 * @Description
 * Current json-lang resolver implementation is based on the API that requires
 * us to register a resolver for each method namespace. Even if we automatically
 * add class to APPLICATION_PATH/resolvers directory and follow Zend class naming 
 * convencion these classes still have to be manually added to the json-lang.php 
 * service entrypoint. Since this introduces unnecessary modification of service
 * entrypoint, we shall preconfigure only one default namespace, which will 
 * automatically identify classes present in resolvers directory. Other
 * namespaces if needed could be added in bootstrap class.
 * 
 * Name of the namespace which will identify auto-resolver is "cms".
 * 
 * To cover as much as possible with this auto-resolver we will describe few 
 * use cases which will guide us through the implementation. 
 * 
 * 
 * cms.user.getIdentity() 
 * // Here cms identifies auto-resolver, user identifies Application_Resolver_User class
 * // and getIdentity() identifies method getIdentity()
 * 
 * cms.ldap.user.getIdentity()
 * // This example very similar in syntaxt to example show above this one ,but contains
 * // additional namespace ldap. If provided call contains 4 namespaces, this means
 * // provided call explicitly identifies ZF module. In this case module of the 
 * // resolver would be ldap, and the class would be called Ldap_Resolver_User.
 * // Name of the method is getIdentity()
 * 
 * 
 * APPLICATION_PATH/resolvers classes
 *  
 * All classes located in resolvers directory must subclass Azf_Service_Lang_Resolver
 * class and implement ACL checking which will guard methods from malicious users.
 * Also, since all methods are visible to the resolver class, we need to create whitelist
 * of all methods which can be invoked through json-lang service. 
 * This whitelist should be integraded into the ACL.
 * 
 * 
 */