<?php
/* Copyright (C) Min-Soo Kim <misol@snu.ac.kr> */

/**
 * @class  lab_membersModel
 * @author Min-Soo Kim <misol@snu.ac.kr>
 * @brief  lab_members module high class
 **/
class lab_members extends ModuleObject
{
	protected $search_option = array('name_content','name','content', 'admission_year', 'graduation_year'); // 검색 옵션
	protected $order_target = array('list_order', 'update_order', 'regdate', 'name', 'admission_date', 'graduation_date'); // 정렬 옵션

	
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