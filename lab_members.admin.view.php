<?php
/* Copyright (C) Min-Soo Kim <misol@snu.ac.kr> */

/**
 * @class  lab_membersAdminView
 * @author Min-Soo Kim <misol@snu.ac.kr>
 * @brief  admin view of the lab_members class
 **/
class lab_membersAdminView extends lab_members
{
	/**
	 * @brief Initialization
	 */
	public function init()
	{
		// check module_srl is existed or not
		$module_srl = Context::get('module_srl');
		if(!$module_srl && $this->module_srl) {
			$module_srl = $this->module_srl;
			Context::set('module_srl', $module_srl);
		}
		
		// generate module model object
		$oModuleModel = getModel('module');

		if($module_info && $module_info->module != 'lab_members')
		{
			throw new Rhymix\Framework\Exceptions\InvalidRequest;
		}
		
		// get the module category list
		$module_category = $oModuleModel->getModuleCategories();
		Context::set('module_category', $module_category);

		$security = new Security();
		$security->encodeHTML('module_info.');
		$security->encodeHTML('module_category..');
		
		// install order (sorting) options
		$order_target = array();
		foreach($this->order_target as $key) $order_target[$key] = lang($key);
		$order_target['update_order'] = lang('last_update');
		Context::set('order_target', $order_target);
		
		// install order (sorting) options
		$search_option = array();
		foreach($this->search_option as $key) $search_option[$key] = lang($key);
		Context::set('search_option', $search_option);
		
	}
	
	
	/**
	 * @brief Administrator Setting page
	 * Settings to enable/disable editor component and other features
	 */
	public function dispLab_membersAdminIndex()
	{
		$oLabMembersAdminModel = getAdminModel('lab_members');
		// Options to get a list
		$option = new stdClass();
		$args = new stdClass();
		
		$int_parameters = array('category_srl', 'data_srl', 'list_count', 'page_count', 'page');
		$string_parameters = array('name', 'status');
		
		foreach ($int_parameters as $parameter) {
			if(Context::get($parameter)) {
				$args->{$parameter} = intval(Context::get($parameter));
			}
		}
		
		foreach ($string_parameters as $parameter) {
			if(Context::get($parameter)) {
				$args->{$parameter} = strval(Context::get($parameter));
			}
		}
		
		$option->args = $args;
		
		$output = $oLabMembersAdminModel->getLabMemberList($args);
		if(!$output->toBool()) {
			throw new Rhymix\Framework\Exception('msg_fail_to_request_open');
		}
		
		// use context::set to setup variables on the templates
		Context::set('total_count', $output->total_count);
		Context::set('total_page', $output->total_page);
		Context::set('page', $output->page);
		Context::set('lab_members_list', $output->data);
		Context::set('page_navigation', $output->page_navigation);

		$security = new Security();
		$security->encodeHTML('lab_members_list..name','lab_members_list..content', 'lab_members_list..last_updater', 'lab_members_list..last_updater');

		// 템플릿 파일 지정
		$this->setTemplatePath($this->module_path.'tpl');
		$this->setTemplateFile('index');
	}
	
	public function dispLab_membersAdminInsert()
	{
	}
}
?>