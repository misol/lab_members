<?php
/* Copyright (C) Kim, MinSoo <misol.kr@gmail.com> */
/**
 * @file	lab_members.admin.controller.php
 * @author	MinSoo Kim (misol.kr@gmail.com)
 * @brief	admin controller class of the lab_members module
 */
class lab_membersAdminController extends maps
{
	/**
	 * @brief Initialization
	 */
	public function init()
	{
	}

	public function procLabMembersAdminCofig()
	{
		$oModuleController = getController('module');
		$config = new stdClass();

		$config->daum_local_api_key = trim(Context::get('daum_local_api_key'));
		$config->map_api_key = trim(Context::get('map_api_key'));
		$config->maps_api_type = '';

		// API 종류 정하기 다음/네이버/구글
		if(strlen($config->map_api_key) == 40)
		{
			$config->maps_api_type = 'daum'; /* Daum maps */
		}
		elseif(strlen($config->map_api_key) == 32)
		{
			$config->maps_api_type = 'naver'; /* NAVER maps */
		}
		elseif(strlen($config->map_api_key) == 64)
		{
			$config->maps_api_type = 'microsoft'; /* bing maps */
		}
		else
		{
			$config->maps_api_type = 'google'; /* Google maps */
		}

		$oModuleController->insertModuleConfig('maps', $config);
		$this->setRedirectUrl(Context::get('error_return_url'));
	}

	/**
	 * @brief 입력 또는 수정
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param string $data_srl 입력/수정할 지도 번호이다. 숫자가 아닐 경우 새로운 항목으로 입력, 숫자가 입력될 경우 기존 항목인지 확인 후 수정한다. 숫자인데 기존 항목이 아닐 경우 새로운 항목으로 입력.
	 * @param string $name, $content 입력할 항목 정보를 포함한다.
	 */
	public function procLabMembersAdminInsert()
	{
		$action = ''; //insert or update
		$logged_info = Context::get('logged_info');
		$args = new stdClass();
		$args->data_srl = intval(trim(Context::get('data_srl'))); // 정수형이 아닐 경우 제거
		$args->member_srl = $logged_info->member_srl;
		$args->name = htmlspecialchars(trim(Context::get('name')));
		$args->content = htmlspecialchars(trim(Context::get('map_description')));
		$args->ipaddress = $_SERVER['REMOTE_ADDR'];
		$args->last_update = 


		// 정수형이고, 값이 존재할 경우 실제 존재하는 지도인지 확인(업데이트 날짜가 존재하는지 확인)
		if($args->maps_srl > 0)
		{
			$output = executeQuery('maps.getMapUpdate', $args);
		}

		// 존재하는 지도일 경우, 업데이트 진행
		if($output->data->update)
		{
			$output = executeQuery('maps.updateMapsContent', $args);
		}
		else // 존재하지 않는 지도일 경우 지도 생성. 시퀀스 번호 생성, 지도 입력
		{
			$args->maps_srl = getNextSequence();//다음 시쿼스 번호
			$output = executeQuery('maps.insertMapsContent', $args);
		}

		$this->add("maps_srl", $args->maps_srl);
		return;
	}

	/**
	 * @brief 지도 삭제
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @param int $maps_srl 삭제할 지도번호. 지도가 있는지 확인 후 삭제. 지도가 없을 경우 아무 변화도 일어나지 않는다.
	 */
	public function procLabMembersAdminDelete()
	{
		$args = new stdClass();
		$args->maps_srl = intval(trim(Context::get('data_srl')));

		// 삭제 진행
		if($args->data_srl > 0) {
			$oDB = DB::getInstance();
			$oDB->begin();
			$output = executeQuery('maps.deleteMapContent', $args);
			if(!$output->toBool()) {
				$oDB->rollback();
				throw new Rhymix\Framework\Exception('fail_to_delete');
			}
			else {
				$oDB->commit();
			}
		}
		else {
			throw new Rhymix\Framework\Exception('msg_invalid_request');
		}
		return;
	}

	/**
	 * @brief 테이블 삭제
	 * @author MinSoo Kim (misol.kr@gmail.com)
	 * @see 모듈에 저장된 자료를 깔끔하게 삭제하고 싶을때. 삭제 전 진심인지 의사를 물어봄.(복구 불가)
	 */
	public function procLapMembersAdminTableDrop()
	{
		DB::dropTable('lap_members');
	}
}
/* End of file maps.admin.controller.php */
/* Location: ./modules/maps/maps.admin.controller.php */
