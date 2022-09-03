jQuery(document).ready(function($){
/*	$('.footnote-ref').qtip({
		// prerender: true,
		// content: 'This is an active list element',
		content:'<a href="http://baidu.com">测试</a>注脚1 <a href="#fnref-1" class="footnote-backref">↩</a>',
		// position: {
	    //     my: 'top center',  // Position my top left...
	    //     at: 'bottom center', // at the bottom right of...
	    //     viewport: $(window)
	    // },
	    style: {
		    classes: 'qtip-bootstrap'
	    },
	    hide: {
            fixed: true,
            delay: 400,
            event: 'unfocus blur mouseleave'
		},
		show: {
			event: 'focus mouseenter'
		}
	});*/

	let num = $(".footnotes li[id^='fn-']").length;
	if(!(num>0)){
		return
	}

	for (let index = 0; index < num; index++) {
		// console.log($(".footnotes ol li[id=fn-"+(index+1)+"]"));

		let textFoot = $(".footnotes ol li[id=fn-"+(index+1)+"]").html();
		let showText = textFoot.substring(0,textFoot.lastIndexOf("<a ")-1);
		// console.log(showText);
		let tip = "sup[id=fnref-"+(index+1)+"]";
		//替换为方括号
		if($.qtip.isSquareBrackets){
			$(tip+" a").text("["+$(tip+" a").html()+"]");
		}

		$(tip).qtip({
			content:showText,
			position: {
			    my: 'top center',  // Position my top left...
			    at: 'bottom center', // at the bottom right of...
			    viewport: $(window)
			},
			style: {
				classes: 'qtip-bootstrap'
			},
			hide: {
				fixed: true,
				delay: 400,
				event: 'unfocus blur mouseleave'
			},
			show: {
				event: 'focus mouseenter'
			}
		});
	}

});
