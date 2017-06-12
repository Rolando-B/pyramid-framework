/*CUSTOM SCRIPT GOES HERE*/
function fixedMenu() {
	var headerHeight = $(".site-header").height();
	var menuHeight = $(".menu_container").height();
	if ($(window).scrollTop() > headerHeight-menuHeight){
		$(".menu_container").attr("id","fixedMenu");
	}else{
		$(".menu_container").removeAttr("id");
	}
}

function fixWrap() {
	var pageWrap = $(".page-wrap");
	var footerHeight = $(".site-footer").height();
	var windowHeight = $(window).height();
	var computedHeight = windowHeight - footerHeight;
	
	if(computedHeight < 830) {
		pageWrap.css("paddingBottom", Math.max(70, 50 + (830 - computedHeight)));
	} else {
		pageWrap.css("paddingBottom", "");
	}
}

$(document).ready(function(){
	fixWrap();
	var date = $(".imgDate").attr("id");

	$("<div id='textImage'>").appendTo("#"+date).text(date).css({
		position:'absolute',
		left:'68%',
		top:'61%',

		"z-index":'1',
		"font-size":'5vw',
		"font-weight":'bold',
		"font-family":'verdana',
		"color":'white',
		"opacity":'0.5'});

	if($(".imgDate").width()>=620){
		$("#textImage").css("font-size","45px");
	}else{
		$("#textImage").css("font-size","5.75vw");
	}

	$(window).resize(function(){
		if($(".imgDate").width()>=620){
			$("#textImage").css("font-size","45px");
		}else{
			$("#textImage").css("font-size","5.75vw");
		}
	});


	var count = $(".sliderContainer").children().length;
	var bob = Math.floor((Math.random() * count) + 1);
	$(".sliderContainer").children("div").hide();
	$(".sliderContainer").children(".sliderDiv:nth-child("+bob+")").fadeIn(1500);
	setInterval(function(){ 
		$(".sliderContainer").children(".sliderDiv:nth-child("+bob+")").fadeOut(3000);
		bob++;
		if (bob>count)
			bob=1;
		$(".sliderContainer").children(".sliderDiv:nth-child("+bob+")").fadeIn(3000);
	}, 6000);


	$(window).scroll(fixedMenu);
	$(window).resize(fixWrap);
	
	fixedMenu();
	fixWrap();
});