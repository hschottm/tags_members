<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * @copyright  Helmut Schottmüller 2009
 * @author     Helmut Schottmüller <typolight@aurealis.de>
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

$GLOBALS['FE_MOD']['user']['memberlist'] = 'ModuleMemberListTags';
$GLOBALS['TL_FFL']['tag'] = 'TagFieldMemberFrontend';

$GLOBALS['tags']['sourcetable'][] = 'tl_member';

?>