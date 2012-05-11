<?php

/**
 * To simplify development of the CMS we must try to standardize all structures
 * which are used across the application. 
 * 
 * All structures will be represented as associative or indexed arrays, with detailed description
 * of each key used within the array.
 * 
 * 
 */

/*******NAVIGATION*******/
/**
 * Navigation is the main part of this CMS. Navigation contains informations
 * which are used to compose the requested WEB page. It is very important to define
 * stable and intuitive navigation structure so that in the future we can easily 
 * add new features and maintain existing ones.
 * 
 * We won't describe here how the data is stored in the database; we will
 * only name few classes and methods used to store and retrieve the data. 
 * 
 * 
 * Navigation record is composted of few sub-structures that control different
 * aspect of WEB page. 
 * Three sub-structures which compose navigation are:
 * +staticParams
 * +dynamicParams
 * +pluginParams
 * 
 *      //staticParams//
 * 
 * Static params is associative array which not shared between navigation nodes.
 * Static parameters are very important since they directly direct Zend MVC implementation 
 * to appropriate module, controller and action. 
 * 
 * array(
 * // Zend MVC module, used to identify module 
 *  'module'=>'default', // REQUIRED
 * // Value that will identify Zend MVC controller
 *  'controller'=>'index, // REQUIRED
 * // Value that will identify controller action
 *  'action'=>'index', // REQUIRED
 * // Value that will be used on the client side to identify javascript
 * // classes which can provide UI for content administration.
 *  'pluginIdentifier'=>'dijitEditor', // REQUIRED
 * // Value that will be used as the body of the <title> element in the page
 *  'pageTitle'=>'Title' // REQUIRED
 * )
 * 
 * 
 *      //dynamicParams//
 * 
 * Dynamic params is associative array that is merged with dynamic params
 * of it's parent nodes. This is very useful if we want to inherit configuration
 * from parent nodes.
 * 
 * 
 * 
 */