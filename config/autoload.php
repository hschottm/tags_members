<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package Tags_members
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	// Classes
	'Contao\TagFieldMemberFrontend' => 'system/modules/tags_members/classes/TagFieldMemberFrontend.php',
	'Contao\TagListMembers'         => 'system/modules/tags_members/classes/TagListMembers.php',
	'Contao\TagMemberHelper'        => 'system/modules/tags_members/classes/TagMemberHelper.php',

	// Modules
	'Contao\ModuleTagCloudMembers'  => 'system/modules/tags_members/modules/ModuleTagCloudMembers.php',
));
