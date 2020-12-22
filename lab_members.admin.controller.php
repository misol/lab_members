<?php
/* Copyright (C) Kim, Min-Soo <misol.kr@gmail.com> */
/**
 * @file	lab_members.admin.controller.php
 * @author	Min-Soo Kim (misol.kr@gmail.com)
 * @brief	admin controller class of the lab_members module
 */
class lab_membersAdminController extends lab_members
{
	/**
	 * @brief Initialization
	 */
	public function init()
	{
	}

	public function procLabMembersAdminConfig()
	{
		$oModuleController = getController('module');
		$config = new stdClass();

		$oModuleController->insertModuleConfig('lab_members', $config);
		return;
	}

	/**
	 * @brief 입력 또는 수정
	 * @author Min-Sooo Kim (misol.kr@gmail.com)
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
		$args->content = json_decode(Context::get('content'));
		$args->lang_code = Context::getLangType();
		
		if(is_array ( $args->content ) {
			$args->content = array_map ( "htmlspecialchars", $args->content )
		} else {
			$args->content = array();
		}

		// 정수형이고, 값이 존재할 경우, 기록이 있는지 확인
		if($args->data_srl > 0)
		{
			$output = executeQuery('lab_members.getLabMemberContent', $args);
		}

		// 존재하는 경우, 업데이트 진행
		if($output->data->regdate)
		{
			$oDB = DB::getInstance();
			$oDB->begin();
			
			$output = executeQuery('lab_members.updateLabMemberContent', $args);
			if(!$output->toBool()) {
				$oDB->rollback();
				throw new Rhymix\Framework\Exception('fail_to_update');
			}
			else {
				$oDB->commit();
			}
			$this->add("data_status", 'update');
		}
		else // 존재하지 않는 지도일 경우 항목 생성. 시퀀스 번호 생성, 항목 입력
		{
			$oDB = DB::getInstance();
			$oDB->begin();
			
			$args->data_srl = getNextSequence();//다음 시쿼스 번호
			$output = executeQuery('lab_members.insertLabMemberContent', $args);
			
			if(!$output->toBool()) {
				$oDB->rollback();
				throw new Rhymix\Framework\Exception('fail_to_registed');
			} else {
				$oDB->commit();
			}
			$this->add("data_status", 'insert');
		}

		$this->add("data_srl", $args->data_srl);
		$this->add("name", $args->name);
		return;
	}

	/**
	 * @brief 항목 삭제
	 * @author Min-Sooo Kim (misol.kr@gmail.com)
	 * @param int $data_srl 삭제할 정보 번호. 항목이 있는지 확인 후 삭제. 항목이 없을 경우 아무 변화도 일어나지 않는다.
	 */
	public function procLabMembersAdminDelete()
	{
		$args = new stdClass();
		$args->data_srl = intval(trim(Context::get('data_srl')));

		// 삭제 진행
		if($args->data_srl > 0) {
			$oDB = DB::getInstance();
			$oDB->begin();
			$output = executeQuery('lab_members.deleteLabMemberContent', $args);
			if(!$output->toBool()) {
				$oDB->rollback();
				throw new Rhymix\Framework\Exception('fail_to_delete');
			}
			else {
				$oDB->commit();
			}
		} else {
			throw new Rhymix\Framework\Exception('msg_invalid_request');
		}
		return;
	}

	/**
	 * @brief 카테고리 생성
	**/
	
	


	function procLabMembersAdminLoadEditor()
	{
		$data_srl = Context::get('data_srl');
		
		
	}

	/**
	 * @brief 테이블 삭제
	 * @author Min-Sooo Kim (misol.kr@gmail.com)
	 * @see 모듈에 저장된 자료를 깔끔하게 삭제하고 싶을때. 삭제 전 진심인지 의사를 물어봄.(복구 불가)
	 */
	public function procLapMembersAdminTableDrop()
	{
		DB::dropTable('lap_members');
		DB::dropTable('lab_members_photos');
	}
}
/* End of file maps.admin.controller.php */
/* Location: ./modules/maps/maps.admin.controller.php */
