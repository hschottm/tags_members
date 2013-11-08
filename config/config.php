<?php

/**
 * @copyright  Helmut Schottmüller 2009-2013
 * @author     Helmut Schottmüller <https://github.com/hschottm/tags_members>
 * @package    Backend
 * @license    LGPL
 * @filesource
 */


/**
 * Front end modules
 */
array_insert($GLOBALS['FE_MOD']['tags'], 1, array
(
	'tagcloudmembers'    => 'ModuleTagCloudMembers'
));

$GLOBALS['TL_FFL']['tag'] = 'TagFieldMemberFrontend';

$GLOBALS['tags_extension']['sourcetable'][] = 'tl_member';
$GLOBALS['TL_HOOKS']['setMemberlistOptions'][] = array('TagMemberHelper', 'setMemberlistOptions');

?>