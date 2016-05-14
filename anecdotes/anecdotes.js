$(document).ready(function() {
	
            $(".tooltip").tipsy({
		html: true,
                gravity: 'w'
	});
	$("#trigger-info").fancybox({
		'titlePosition': 'inside',
		'transitionIn': 'none',
		'transitionOut': 'none'
	});
	$(".anecdote-content.not-empty").hover(

	function(e) {
		e.stopPropagation();
  		if ($(this).hasClass("out") && $(this).parent().parent().parent().hasClass("matrix")) {
                    $(this).find(".container").show();
                    $(this).css("background-color", "white");
                    $(this).switchClass("out", "over");
                }
                else if ($(this).hasClass("over")  && $(this).parent().parent().parent().hasClass("matrix"))  {
                    $(this).find(".container").hide();
                    $(this).find(".comment").hide();
                    $(this).css("background-color", "grey");
                    $(this).switchClass("over", "out");
                }
	});

	$(".anecdote-content.not-empty").click(
	
	function(e) {
	           
		e.stopPropagation();
		if ($(this).hasClass("inactive")) {
			$(this).find(".comment").show();
			$(this).switchClass("inactive", "active");
                        //la classe inactive est rajouter après à cause de mouseout
                        //table : matrix-anecdote-detail
                        //tr : active/inactive
		} else if ($(this).hasClass("active")) {
			$(this).switchClass("active", "inactive");
			$(this).find(".comment").hide();
		}
	});

	$(".anecdote-title").click(

	function(e) {
		e.stopPropagation();
		if ($(this).hasClass("inactive")) {
			$("tbody tr, td, th").show();
			$(".comment").hide();
                        $("tbody .container").hide();
			$(".not-empty").removeClass("book-detail");
			$(".book-author").switchClass("active", "inactive");
			var anecdoteId = $(this).parent().attr("id");
			$("tbody tr").hide();
			$(this).parent().show();
			$("th").hide();
			$("th.no-border").show();
			$(".empty").hide();
			$(".not-empty").css({
				"background-color": "white"
			});
			$(".not-empty").addClass("anecdote-detail");
			$("." + anecdoteId).find(".container").show();
			$(this).switchClass("inactive", "active");
			$("table").switchClass("matrix", "detail");
			var tds = document.querySelectorAll("." + anecdoteId);
			var index = [];
			for (i = 0; i < tds.length; i++) {
				if (tds[i].className.contains("not-empty")) {
					index.push(i);
				}
			}
			var th = document.querySelectorAll(".book-author");
			for (i = 0; i < th.length; i++) {
				if (index.indexOf(i + 2) != -1) {
					$(th[i]).show();
				}
			}
			var th = document.querySelectorAll(".book-date");
			for (i = 0; i < th.length; i++) {
				if (index.indexOf(i + 2) != -1) {
					$(th[i]).show();
				}
			}
                                                $("#view-matrix").show();

		} else if ($(this).hasClass("active")) {
			$("tbody tr, td, th").show();
			$("tbody .container").hide();
                        $(".not-empty").css("background-color", "grey");
			$(this).switchClass("active", "inactive");
			$("table").switchClass("detail", "matrix");
			$(".not-empty").removeClass("anecdote-detail");
		}
	});
	$(".book-author").click(

	function(e) {
		e.stopPropagation();
		var bookId = $(this).attr("id");
		if ($(this).hasClass("inactive")) {
			$(".not-empty").removeClass("anecdote-detail");
			$("tbody tr, td, th").show();
                        $(".comment").hide();
			$("tbody .container").hide();
			//$(".not-empty").css("background-color", "grey");
			$(".anecdote-title").switchClass("active", "inactive");
			$("td:not(." + bookId + ", .anecdote-title), th:not(." + bookId + ", .no-border)").hide();
			$("." + bookId).find(".container").show();
			$("tr").has("td.empty." + bookId).hide();
			$(".not-empty").css({
				"background-color": "white"
			});
			$(".not-empty").addClass("book-detail");
			$(this).switchClass("inactive", "active");
			$("table").switchClass("matrix", "detail");
                                                $("#view-matrix").show();

		} else if ($(this).hasClass("active")) {
			$("tbody tr, td, th").show();
			$("tbody .container").hide();
			$(".not-empty").css("background-color", "grey");
			$(".not-empty").removeClass("book-detail");
			$(this).switchClass("active", "inactive");
			$("table").switchClass("detail", "matrix");
		}
	});
        //$(".book-author").dblclick(
        //    function(e) {
        //     	e.stopPropagation();
        //        var url = "../critique/"+$(this).attr('id');
        //        var win = window.open(url, '_blank');
        //        win.focus();
        //    }
        //);        $(".anecdote-content.not-empty").dblclick(
        //    function(e) {
        //     	e.stopPropagation();
        //        var url = "../critique/"+$(this).attr('class').split(' ')[0] +"#"+$(this).attr('class').split(' ')[0];
        //        var win = window.open(url, '_blank');
        //        win.focus();
        //    }
        //);
        $("#view-matrix").click(
            function (e) {
			$("tr, td, th").show();
			$(".container").hide();
			$(".not-empty").css("background-color", "grey");
			$(".anecdote-detail").removeClass("anecdote-detail");
			$(".book-detail").removeClass("book-detail");
                        $(".active").switchClass("active", "inactive");
			$("table").switchClass("detail", "matrix");
                
            }
        );
        $("#view-detail").click(
            function (e) {
			$("tr, td, th").show();
			$(".container").show();
                        $(".comment").show();
			$(".not-empty").css("background-color", "white");
			$(".anecdote-detail").removeClass("anecdote-detail");
			$(".book-detail").removeClass("book-detail");
                        $(".active").switchClass("active", "inactive");
			$("table").switchClass("matrix", "detail");
                        $("#view-matrix").show();
                
            }
        )
});