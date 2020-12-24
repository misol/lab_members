function getLabMembersList() {
	var lab_members_list = jQuery('#lab_members_list');
	var cartList = [];
	lab_members_list.find(':checkbox[name=cart]').each(function(){
		if(this.checked) cartList.push(this.value); 
	});

	var params = new Array();
	var response_tags = ['error', 'message', 'lab_members_list'];
	params["data_srls"] = cartList;

	exec_xml('lab_members','procLabMembersAdminSelect',params, function(ret_obj, response_tags) {
		var htmlListBuffer = '';
		var statusNameList = {"PUBLIC":"Public", "SECRET":"Secret", "PRIVATE":"Private", "TEMP":"Temp"};
		if(ret_obj['lab_members_list'] == null)
		{
			htmlListBuffer = '<tr>' +
								'<td colspan="3" style="text-align:center;">'+ret_obj['error']+': '+ret_obj['message']+'</td>' +
							'</tr>';
		}
		else
		{
			var lab_members_list = ret_obj['lab_members_list']['item'];
			if(!jQuery.isArray(lab_members_list)) lab_members_list = [lab_members_list];
			for(var x in lab_members_list)
			{
				var objDocument = lab_members_list[x];
				htmlListBuffer += '<tr>' +
									'<td class="title">'+ objDocument.variables.title +'</td>' +
									'<td class="nowr">'+ objDocument.variables.nick_name +'</td>' +
									'<td class="nowr">'+ statusNameList[objDocument.variables.status] +'</td>' +
								'</tr>'+
								'<input type="hidden" name="cart[]" value="'+objDocument.document_srl+'" />';
			}
			jQuery('#selectedDocumentCount').html(lab_members_list.length);
		}
		jQuery('#LabMembersManageListTable>tbody').html(htmlListBuffer);
		
	}, response_tags);
}
