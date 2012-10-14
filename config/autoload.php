<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2012 Leo Feyer
 * 
 * @package Tags_members
 * @link    http://contao.org
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

	// Modules
	'Contao\ModuleMemberListTags'   => 'system/modules/tags_members/modules/ModuleMemberListTags.php',
	'Contao\ModuleTagCloudMembers'  => 'system/modules/tags_members/modules/ModuleTagCloudMembers.php',
));
