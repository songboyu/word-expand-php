﻿<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
	<div class="col-md-12">
		<!-- BEGIN EXAMPLE TABLE PORTLET-->
		<div class="portlet box blue-madison">
			<div class="portlet-title">
				<div class="caption">
					<i class="fa fa-globe"></i><?php echo $title ?>
				</div>
				<div class="actions">
					<a href="#add_task" data-toggle="modal" class="btn btn-default btn-sm">
						<i class="fa fa-pencil"></i> 新增任务 </a>
				</div>
			</div>
			<div class="portlet-body">
				<table class="table table-striped table-bordered table-hover" id="task_manage_table">
					<thead>
						<tr>
							<th>任务编号</th>
							<th>种子词</th>
							<th>搜索数量</th>
							<th>状态</th>
							<th>任务时间</th>
							<th width="70">操作</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td colspan="8">数据获取中，请稍后...</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		<!-- END EXAMPLE TABLE PORTLET-->
	</div>
</div>
<!-- END CONTENT -->
<script>
	jQuery(document).ready(function() {
		//全局js函数，header中定义
		global_function();
		//common_function.js中定义，渲染侧边栏
     	set_sidebar_selected('#word');
		//加载数据表格
		task_manage_table_init();
		
		auto_reload();
	});

	function auto_reload(){
		reload_datatable('#task_manage_table');
		setTimeout(function () { 
			auto_reload();
		}, 3000);
	}
	function task_manage_table_init(){
		var table = $('#task_manage_table');
		table.dataTable( {
			"pageLength": 50,
			"bProcessing": true,
			"bServerSide": true,
			"sPaginationType": "full_numbers",
			"sAjaxSource": "<?php echo base_url('word_manage/get_task_list')?>",
			"oLanguage": {
				"sLengthMenu": "每页显示 _MENU_ 条记录",
				"sZeroRecords": "抱歉， 没有找到",
				"sInfo": "从 _START_ 到 _END_ /共 _TOTAL_ 条数据",
				"sInfoEmpty": "没有数据",
				"sInfoFiltered": "(从 _MAX_ 条数据中检索)",
				"oPaginate": {  
					"sFirst": "首页",  
					"sPrevious": "前一页",
					"sNext": "后一页",  
					"sLast": "尾页"  
				}, 
				"sZeroRecords": "没有检索到数据",  
				"sProcessing": "<img src='<?php echo base_url('rs/global/img/loading-spinner-blue.gif');?>' />"
			},
			"aaSorting":[//设置默认排序列
				[ 0, "seeds" ]
			],
			'columns': //columns属性，配置具体列属性，详见http://sgyyz.blog.51cto.com/5069360/1408251
			[
				{ 'data': 'task_id', "className": "td_text_align_center" },
				{ 'data': 'seeds', "className": "td_text_align_center" },
				{ 'data': 'rs_count', "className": "td_text_align_center" },
				{ 'data': 'state', "className": "td_text_align_center" },
				{ 'data': 'time', "className": "td_text_align_center" },
			],
			"columnDefs": [
				{
					"targets": [0], // 目标列位置，下标从0开始
					"data": "task_id" // 数据列名
				},
				{
					"targets": [1],
					"data": "seeds",
					"render": function(data, type, full){
						keywords_str = '';
						keywords = data.split(' ');
						for(var i=0; i<keywords.length; i++){
							keywords_str += '<code>' + keywords[i] + '</code>';
						}
						return keywords_str;
					}
				},
				{
					"targets": [2], // 目标列位置，下标从0开始
					"data": "rs_count", // 数据列名
				},
				{//state
					"targets": [3],
					"data": "state",
					"render": function(data, type, full){
						if(data==0)
							return '<span class="label label-sm label-danger">任务未开始</span>';
						else if(data==1)
							return '<span class="label label-sm label-info">网页获取中</span>';
						else if(data==2)
							return '<span class="label label-sm label-warning">网页解析中</span>';
						else if(data==3)
							return '<span class="label label-sm label-info">结果排序中</span>';
						else if(data==4)
							return '<span class="label label-sm label-success">任务完成</span>';
					}
				},
				{
					"targets": [4], // 目标列位置，下标从0开始
					"data": "time", // 数据列名
				},
				// 增加一列，包括删除和修改，同时将我们需要传递的数据传递到链接中
				{
					"targets": [5],
					"data": "time", // 数据列名
					"render": function(data, type, full) { // 返回自定义内容
						return "<a target='_blank' href='"+ base_url + "word_manage/detail/?task_id=" + full.task_id + "'>结果列表</a></br><a href='javascript:void(0);' onclick='deleteTask("+ full.task_id + ");'>删除任务</a>";
					}
				}
			],
			//回调函数，修改每个tr中web_content的td的属性, 增加dataid属性
			"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull) {
				$(nRow).children('.task_manage_wc').attr("dataid", aData.id);
				return nRow;
			},
		} );
		//修改perpage下拉菜单样式为扁平化
		$('#task_manage_table_wrapper').find('.dataTables_length select').select2(); 
	}
	function deleteTask(dataid) {
		if(confirm("确认删除？")!=true)
			return false;
		taskid = dataid;
		$.get(base_url+"word_manage/deleteTask/"+taskid,function(data){
			reload_datatable('#task_manage_table');
		});
	}
</script>
