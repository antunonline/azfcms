<?php

/**
 * This document covers basics of CMS implementation. 
 * If you want to write your own extensions for this CMS, then it is crutial
 * to learn how this CMS works. 
 * 
 */

/**
 * This CMS is based on Navigational model  which is essentially tree structure that
 * can contain static, dynamic and plugin parameters (key value pairs of data).
 * Difference between static and dynamic parameters is in inheritance. Dynamic 
 * parameters inherit dynamic parameters from their parent nodes, while static do not.
 * Plugin parameters also inherit values from parent nodes. 
 * 
 * Values stored in navigational model contain unique values that identify nodes
 * on user page request, and contain values which will setup CMS so that appropriate 
 * content is generated.  
 * 
 * Navigation node values are stored in MySQL database within Navigation DB table. 
 * Currently Navigation table contains only these columns:
 * 
 *  # id - Unique Identifier (pk) of the node
 *  # parentId - Unique identifier of the parent node
 *  # tid - Unique id of tree, (multiple trees can exist in one table)
 *  # l - Tree structure metadata
 *  # r - Tree structure metadata
 *  # disabled - State of the navigational node
 *  # url - SEO url of the current web page, not used as identifier
 *  # final - Serialized static parameters
 *  # plugins - Serialized plugin parameters
 *  # abstract - Serialized dynamic parameters
 *  # home - Flag that is used to identify home page
 *  # title - Page title
 * 
 * 
 * 
 */


/**
 * ##Navigation node looup##
 * 
 * When the user requests a web page, he will implicitly provide some kind of URL.   
* That URL will be then parsed and will produce one of two kinds of values.
 * 
 * If the URL does not contain node identifier, value of -1 will be returned.
 * If the URL does contain identifier, that value will be returned. 
 * 
 * At that point route will provide calculated value to match() function of navigation model, and
 * navigation model will based on the provided value load appropriate node. 
 * If the value is greather than 0, then node will be looked up by that identifier, otherwise
 * node with home value of 1 will be returned.
 * 
 * When we have identified node id, we will pass that node id to Navigation model which will return 
 * parameters that will identify MVC stack.
 * 
 * 
 * All above described procedures will be executed in Azf default route implementation. 
 * 
 * R = Azf_Controller_Router_Route_Default
 * N = Azf_Model_Tree_Navigation
 * HR = Zend_Controller_Request_Http
 * 
 *               R              N
 *               |              |
 * match(HR)     |              |
 * ------------->|              |
 *               |              |
 *               |              |
 *               |  match(id)   |
 *               |------------->|
 *               |    id        |
 *               |< - - - - - - | 
 *               | getStaticParams(id)
 *               |------------->|
 *               |              |
 *               | staticParams |
 *               | < - - - - -  |
 *  staticParams |              |
 * < - - - - - - |
 * 
 * 
 * From sequence diagram shown above this text we can clearly see how the process
 * of generating MVC parameters work. Method match used in Navigation will validate given
 * id and return appropriate value. If given input id is not valid, id of the home page
 * will be returned. 
 * 
 * Then we have to load staticParams which will contain all required key/value pairs
 * which are required by the Zend MVC implementation.
 * 
 * 
 */


/**
 *  //  Navigation in Templates     //
 * 
 * To ease the development of templates we have written few view helpers
 * which expose Naivgation model hierarchies in template. 
 * These helpers also generate URL links and point to child nodes which can be used
 * to construct dynamic deep menus.
 * 
 * View helper class that shall provide build-in navigation trafersal API is called
 * Application_View_Helper_Navigation, and can be accessed through navigation()
 * call in template context.
 * 
 * Returned array structures will be standardnized and will contain prepared data
 * so that we don't have to escape or construct provided values.
 * 
 * Structure of navigation item is a simple associative array with following keys:
 * 
 * array(
 * // This key will point to user visible name of the page link
 * 'title'=>"",
 * 
 * // This key will point to URL of the page that this link represents
 * 'url'=>'',
 * 
 * // Through this key we can find out if this link points to currently opened page
 * 'isSelected'=>false,
 * 
 * // This key contains child nodes which are equivalent to this array.
 * 'childNodes'=>array()
 * )
 * 
 * If we want to load all menus, we can use following function within template
 * $this->navigation->getTree();
 * 
 * 
 * If we want to load child nodes of currently selected page 
 * $this->navigation->getContext()
 * 
 * To load create breadcrumbs menu we can use 
 * $this->navigation->getBreadCrumbs();
 */