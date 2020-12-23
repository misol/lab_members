<?php
/* Copyright (C) Kim, Min-Soo <misol.kr@gmail.com> */
/**
 * @file	lab_members.admin.model.php
 * @author	Min-Soo Kim (misol.kr@gmail.com)
 * @brief	admin model class of the lab_members module
 */
class lab_membersAdminModel extends lab_members
{
	/**
	 * Initialization
	 * @return void
	 */
	public function init()
	{
	}
	
	/**
	 * @brief Initialize editor form of lab_members admin page
	**/
	public function getLabMemberList($option = null)
	{
		$args = new stdClass();
		$columnList = array();
		
		if( $option === null ) $option = new stdClass();
		
		$int_parameters = array('category_srl', 'data_srl', 'list_count', 'page_count', 'page');
		$string_parameters = array('name', 'status');
		
		foreach ($int_parameters as $parameter) {
			if($option->args->{$parameter}) {
				$args->{$parameter} = intval($option->args->{$parameter});
			}
		}
		
		foreach ($string_parameters as $parameter) {
			if($option->args->{$parameter}) {
				$args->{$parameter} = strval($option->args->{$parameter});
			}
		}
		
		if (isset($option->columnList) && is_array($option->columnList)) {
			$columnList = $option->columnList;
		}
		
		$output = executeQuery('lab_members.getLabMemberList', $args, $columnList);
		if(!$output->toBool()) {
			throw new Rhymix\Framework\Exception('msg_fail_to_request_open');
		}
		return $output;
	}
	/**
	 * @brief Initialize editor form of lab_members admin page
	**/
	public function getEditor($data_srl = 0, $option = null)
	{
		if( $option === null ) $option = new stdClass();
		
		$data_srl = intval($data_srl);
		$oLabMembersModel = getModel('lab_members');
		
		// Initialize options.
		if (!is_object($option))
		{
			$option = new stdClass;
		}
		
		// Load language files.
		Context::loadLang('./modules/lab_members/lang');
		Context::loadLang('./modules/editor/lang');
		
		// Set editor sequence and upload options.
		if (!$data_srl)
		{
			$data_srl = getNextSequence();
		}
		
		
		if (!$option->editor_skin || !file_exists(RX_BASEDIR . 'modules/lab_members/skins/' . $option->editor_skin . '/editor.html'))
		{
			$module_config = $oLabMembersModel->getLabMemberConfig();
			$option->editor_skin = $module_config->editor_skin;
		}
		
		// Get file upload limits
		$file_config = FileModel::getUploadConfig();
		$file_config->allowed_attach_size = $file_config->allowed_attach_size*1024*1024;
		$file_config->allowed_filesize = $file_config->allowed_filesize*1024*1024;

		// Calculate the appropriate chunk size.
		$file_config->allowed_chunk_size = min(FileHandler::returnBytes(ini_get('upload_max_filesize')), FileHandler::returnBytes(ini_get('post_max_size')) * 0.95, 64 * 1024 * 1024);
		if ($file_config->allowed_chunk_size > 4 * 1048576)
		{
			$file_config->allowed_chunk_size = floor($file_config->allowed_chunk_size / 1048576) * 1048576;
		}
		else
		{
			$file_config->allowed_chunk_size = floor($file_config->allowed_chunk_size / 65536) * 65536;
		}
		
		// Do not allow chunked uploads in IE < 10, Android browser, and Opera
		$browser = Rhymix\Framework\UA::getBrowserInfo();
		if (($browser->browser === 'IE' && version_compare($browser->version, '10', '<')) || $browser->browser === 'Android' || $browser->browser === 'Opera')
		{
			$file_config->allowed_filesize = min($file_config->allowed_filesize, FileHandler::returnBytes(ini_get('upload_max_filesize')), FileHandler::returnBytes(ini_get('post_max_size')));
			$file_config->allowed_chunk_size = 0;
		}

		Context::set('file_config',$file_config);
		// Configure upload status such as file size
		$upload_status = FileModel::getUploadStatus();
		Context::set('upload_status', $upload_status);
		// Upload enabled (internally caching)
		$oFileController = getController('file');
		$oFileController->setUploadInfo($data_srl, $data_srl);
		// Check if the file already exists
		$files_count = FileModel::getFilesCount($data_srl);
		
		Context::set('files_count', (int)$files_count);
		
		$tpl_path = RX_BASEDIR . 'modules/lab_members/skins/' . $option->editor_skin . '/';
		Context::set('lab_members_editor_path', $tpl_path);
		$oTemplate = TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, 'editor.html');
	}
}
?>