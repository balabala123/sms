$(function(){
	// 用户退出
	$(".userout").on("mouseover",function(){
		$(this).children("li").children("ul").css("display","block");
	});
	$(".userout").on("mouseout",function(){
		$(this).children("li").children("ul").css("display","none");
	});
	// 一级分类列表点击
	$(".main-nav>li>ul").slideUp();
	$(".main-nav>li").off("click");
	$(".main-nav>li").on("click",function(){	
		if($(this).children("ul").css("display") =="none"){
			$(".main-nav>li").removeClass("current");
			$(this).addClass("current");
			$(".main-nav>li>ul").slideUp(500);
			$(this).children("ul").stop().slideDown(500);
		}
	});
	// $(".main-nav>li>ul").eq(0).slideDown(300);
	//二级分类列表点击
	$(".main-nav>li>ul>li").off("click");
	$(".main-nav>li>ul>li").on("click",function(){
		var s = $(this).children().children().eq(1).html();
		if(!$(this).hasClass("line-current")&&(s != undefined)){
			$(".main-nav>li>ul>li>a>span").removeClass("line-current");		
			$(this).children().children(".line").addClass("line-current");
			var url = $(this).attr("url");
			$(".main-content").load(url);
		}
	});
	 $(".main-nav>li").eq(0).trigger("click");	
	 $(".main-nav>li>ul>li").eq(1).trigger("click"); 
})