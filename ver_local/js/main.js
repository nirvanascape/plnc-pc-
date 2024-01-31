$(document).ready(function(){


	//ItemBox, SubTitle, tab_content
	$('.main_section').addClass('active'); 
	// $('#tab-content >div').hide(); 
	// $('#tab-content > div:first-child').fadeIn();

    //MainVisual
    var oldidx = 0,
		idx = 0,
		visualBtn =  $('.mainvisual .btn>span'),
		visualImg =  $('.mainvisual ul li'),
		imgLength = visualImg.length,
		duration = 5000;

	function changeImg(idx) {

		if(oldidx!=idx){  
			visualBtn.eq(idx).addClass("active"); 
			visualBtn.eq(oldidx).removeClass("active"); 
			visualImg.eq(idx).stop(true,true).fadeIn(300); 
			visualImg.eq(oldidx).stop(true,true).fadeOut(300); 
		}
		oldidx=idx; 
	};
 
	function changeAuto(){
		idx++;
		if(idx>imgLength-1) {
			idx=0;
		};
		changeImg(idx);
    }
    
	timer=setInterval(changeAuto,duration);

	visualBtn.click(function(){
		clearInterval(timer);
		idx= $(this).index();
		changeImg(idx);
		timer=setInterval(changeAuto,duration);

	});

	chkPlay=true;
	 $(".playbt").click(function(){

		if(chkPlay){
			clearInterval(timer);
			 $(this).css({"background":"url('images/main_play.png')no-repeat 0 0 / cover"});
			chkPlay=false;
		}else{
			timer=setInterval(changeAuto,duration);
			 $(this).css({"background":"url('images/main_stop.png')no-repeat 0 0 /cover"});
			chkPlay=true;
		}
	});

	 $(".prev").click(function(e){
		e.preventDefault();
		clearInterval(timer);
		idx--;
		if(idx<0) {
			idx=imgLength-1;
		};
		changeImg(idx);
		timer=setInterval(changeAuto,duration);

	});

	 $(".next").click(function(e){
		e.preventDefault();
		clearInterval(timer);
		idx++;
		if(idx>imgLength-1) { 
			idx=0;
		};
		changeImg(idx);
		timer=setInterval(changeAuto,duration);
	});

	//TabMenu
	 $('#tab-content>div:not('+ $('.tab-menu>li .active').attr('href')+')').hide();
	 $('.tab-menu li a').click(function(){
		 $('.tab-menu li a').removeClass('active'); 
		 $(this).addClass('active'); 
		 $('#tab-content > div').hide(); 
		 $( $(this).attr('href')).fadeIn(); 
		return false; 
	});

	//moving line, Changing text
	var durationLine = 100;
	
	 $('.tab-menu li a').each(function(){
		var firstCoor =  $('.tab-menu li:first-child').position().left;
		$('.tab_hr').css({left:firstCoor});

		 $(this).click(function(e){
			e.preventDefault();
			const coordinate =  $(this).position().left;
			 $('.tab_hr').stop().animate({left:coordinate},durationLine);
			
			const changeText =  $(this).parent('li').data('text');
			 $('.change_text').text(changeText);
		});
	});

	//FooterMenuView
	var pageButton1 = document.getElementById("footer_btn1");
	var newUrl = location.href;
	var href1 = "page=on1";
	var href2 = "page=on2";

	if(newUrl.indexOf(href1) != -1){
		$('.tab-menu li a').removeClass('active'); 
		$('.about_tabs2 a').addClass('active');
		$('#tab-content > div').css({"display":"none"});
		$('#tabs-2').css({"display":"block"}); 
		$('.tab_hr').css({"left":"333px"});
		$('.change_text').text("회사개요");
	};
	if(newUrl.indexOf(href2) != -1){
		$('.tab-menu li a').removeClass('active'); 
		$('.about_tabs3 a').addClass('active');
		$('#tab-content > div').css({"display":"none"});
		$('#tabs-3').css({"display":"block"}); 
		$('.tab_hr').css({"left":"666px"});
		$('.change_text').text("제품문의");
	};
	
	//filtering client
	$(".content_filter li").fadeIn();
	$(".client_head button").click(function(e){
		e.preventDefault();
		var category = $(this).attr("title");   
		$(".content_filter li").hide();            
		if(category == "all"){
			$(".content_filter li").fadeIn();
		}else{
			$(".content_filter li[class*="+category+"]").fadeIn();
		}               
	 });

	//filtering client Change number
	 $('.client .controls button').each(function(){

		 $(this).click(function(e){
			const buttonFilter =  $(this).data('filter');
			const buttonData = buttonFilter.replace('.','');
			const listLength =  $('.'+ buttonData).toArray();
			const listNumber = listLength.length;
			 $('.change_number').text(listNumber);
		})
	})

	 $('.client .controls button:first-child').click(function(){
		const allLength =  $('.mix_box li').length;
		 $('.change_number').text(allLength);
	})

	//Number animation
		var progressRate =  $('.number_animate').attr('data-rate');
		 $({percent:3.0}).animate({percent:progressRate},{
			duration: 1000, 
			progress: function(){
				var now = this.percent;
				 $('.number_animate').text(Math.round(now * 10)/10);
			}
		});
	
	//File_box 
	var uploadFile =  $('.file_box .file_btn');
		uploadFile.on('change', function(){
			if(window.FileReader){
				var filename =  $(this)[0].files[0].name;
			} else {
				var filename =  $(this).val().split('/').pop().split('\\').pop();
			}
			 $(this).siblings('.file_name').val(filename);
		});

	//Menu Highlight
	var subMenu = $('.sub_menu ul li');

	var url = window.location.href;
	url = url.substring(0, (url.indexOf("#") == -1) ? url.length : url.indexOf("#"));
	url = url.substring(0, (url.indexOf("?") == -1) ? url.length : url.indexOf("?"));
	url = url.substr(url.lastIndexOf("/") + 1);
	
	if(url == ''){
	url = 'index.html'; 
	}

	$(subMenu).each(function(){
	var href = $(this).find('a').attr('href');
		if(url == href){
			$(this).addClass('active');
		}
	});





});

