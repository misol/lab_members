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
	function init()
	{
	}
	
	/**
	 * @brief Initialize editor form of lab_members admin page
	**/
	public static function getEditor($data_srl = null, $option = new stdClass)
	{
		// Load language files.
		Context::loadLang('./modules/lab_members/lang');
		Context::loadLang('./modules/editor/lang');
		
		// Set editor sequence and upload options.
		if ($data_srl)
		{
			$option->editor_sequence = $data_srl;
		}
		else
		{
			$data_srl = getNextSequence();
			$option->editor_sequence = $data_srl;
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
		if($data_srl) $files_count = FileModel::getFilesCount($data_srl);
		
		Context::set('files_count', (int)$files_count);
		
		$oTemplate = TemplateHandler::getInstance();
		return $oTemplate->compile($tpl_path, 'editor.html');
	}
}
?>