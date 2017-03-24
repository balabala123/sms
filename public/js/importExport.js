function _export(){
	//alert(files);
	$.ajax({
	   type: "POST",
	   url: "{:U('import_export')}",
	   data: "queue_type=1&action=export",
	   success: function(msg){
	     art.dialog({
			id: 'warning',
			icon: 'warning',
			content: '导出成功正在列表，请稍后！',
			ok: function () {
				//reloadPage(window);
			}
		}); 
	  }
	});
}
function _import(){
    Wind.use('artDialog', 'iframeTools', function () {
        var ImportUrl = "{:U('import_index')}";
        art.dialog.open(ImportUrl, {
            title: '{:L("DATA_IMPORT")}',
            lock: true,
            height: 150,
            width: "40%",
            // 在open()方法中，init会等待iframe加载完毕后执行
            init: function () {
                var iframe = this.iframe.contentWindow;
                var top = art.dialog.top;// 引用顶层页面window对象

            },
            ok: function () {				
                return true;                
            },
            cancel: true
        });
    });
}