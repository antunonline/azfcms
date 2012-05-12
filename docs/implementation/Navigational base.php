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
 * 
 * 
 * 
 */


/**
 * ##Navigation node looup##
 * 
 * When the user requests a web page, he implicitly provide us with some kind of URL.
 * That URL will be then parsed and will produce one of two kinds of values.
 * 
 * If the URL does not contain node identifier, value of -1 will be returned.
 * If the URL does contain identifier, it's value will be returned. 
 * 
 * At that point route will provided calculated value to match() function of navigation model, and
 * navigation model will based on the provided value load appropriate node. 
 * If the value is greather than 0, then node will be looked up by that identifier, otherwise
 * node with home value of 1 will be returned. 
 * 
 */