<?php
/* Copyright (C) Min-Soo Kim <misol@snu.ac.kr> */

/**
 * @class  lab_membersModel
 * @author Min-Soo Kim <misol@snu.ac.kr>
 * @brief  lab_members module high class
 **/
class lab_members extends ModuleObject
{

	/**
	 * constructor
	 *
	 * @return void
	 */
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * @brief install the module
	 **/
	function moduleInstall()
	{
		// enabled in the admin model
		$oModuleController = getController('module');

	}

	/**
	 * @brief chgeck module method
	 **/
	function checkUpdate()
	{
		$oModuleModel = getModel('module');

		return false;
	}

	/**
	 * @brief update module
	 **/
	function moduleUpdate()
	{
		$oModuleModel = getModel('module');
		$oModuleController = getController('module');
	}

	function moduleUninstall()
	{
		set_time_limit(0);

		$oModuleModel = getModel('module');
		$oModuleController = getController('module');

		return new BaseObject();
	}
}