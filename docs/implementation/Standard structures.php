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
 * 
 * // Value that will identify Zend MVC controller
 *  'controller'=>'index, // REQUIRED
 * 
 * // Value that will identify controller action
 *  'action'=>'index', // REQUIRED
 * 
 * // Value that will be used on the client side to identify javascript
 * // classes which can provide UI for content administration.
 *  'pluginIdentifier'=>'dijitEditor', // REQUIRED
 * )
 * 
 * 
 *      //dynamicParams//
 * 
 * Dynamic params is associative array that is merged with dynamic params
 * of it's parent nodes. This is very useful if we want to inherit configuration
 * from parent nodes. The downside of such feature is that if we want to remove
 * specific parameter, we will have to set such parameter to null value.
 * 
 * array(
 *  // Value that will be used in metda keywords element
 *  'metaKeywords'=>'', // REQUIRED
 * 
 *  // value that will be used in meta description element
 *  'metaDescription'=>'', // REQUIRED
 * 
 *  // Value that will uniquely identify template which should be used 
 *  // to render the content
 *  'templateIdentifier'=>'', // REQUIRED
 * )
 * 
 * 
 *      //pluginParams//
 * 
 * Plugin params represent data values that are exclusively used by
 * plugins. To be exact these parameters are used to identify and configure
 * plugins used within the page. Plugin parameters also inherit other parameters 
 * from parent nodes, which means that plugins defined in parent nodes will be
 * shown in child nodes, unles overriden in child nodes.
 * 
 * Each plugin is composed of pluginIdentifier and plugin values. Plugin identifier
 * can be retrieved by Azf_Model_Tree_Navigation.getPluginNames() method, which 
 * will return list of plugin names present in the current node. 
 * 
 * Values are encapsulated in associative array that has few predefined keys
 * 
 * array(
 * // This key points to value that will identify classes on the client side
 * 'pluginIdentifier'=>'', // REQUIRED
 * 
 * // This key identifies position in the template, where the plugin will be shown
 * 'position'=>'', // REQUIRED
 * 
 * // This key defines weight of the plugin. The weight of the plugin
 * // is used to calculate ordering of plugins which are placed in the same position
 * 'positionWeight'=>'' // REQUIRED
 * )
 * 
 * 
 * 
 */