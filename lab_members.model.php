<?php
/* Copyright (C) Min-Soo Kim <misol@snu.ac.kr> */

/**
 * @class  lab_membersModel
 * @author Min-Soo Kim <misol@snu.ac.kr>
 * @brief  lab_members module  Model class
 **/
class lab_membersModel extends module
{
	/**
	 * @brief initialization
	 **/
	function init()
	{
	}
	
	
	/**
	 * Get lab_members configurations
	 *
	 * @return object returns configuration.
	 */
	public static function getLabMemberConfig()
	{
		$config = ModuleModel::getModuleConfig('lab_members');
		$config = is_object($config) ? clone $config : new stdClass();
		// Default setting if not exists
		$config->editor_skin = $config->editor_skin ?? 'default';
		
		return $config;
	}
}