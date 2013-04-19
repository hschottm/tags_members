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

$GLOBALS['TL_FFL']['tag'] = 'TagFieldMemberFrontend';

$GLOBALS['tags_extension']['sourcetable'][] = 'tl_member';
$GLOBALS['TL_HOOKS']['setMemberlistOptions'][] = array('TagMemberHelper', 'setMemberlistOptions');

?>