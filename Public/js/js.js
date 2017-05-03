$(function() {
	 $("#checkAll").click(function() {
    $("input[class='table-checkable']").prop("checked", this.checked);
  });
  
  $("input[class='table-checkable']").click(function() {
    var $subs = $("input[class='table-checkable']");
    $("#checkAll").prop("checked" , $subs.length == $subs.filter(":checked").length ? true :false);
  });
  
   $(".hide_left").click(function() {
	   if($(".sidebar").hasClass("act")){
 $(".sidebar").css("left","-200px");
 $(".sidebar").removeClass("act");
 $(this).find("span").addClass("glyphicon-th");
 $(this).find("span").removeClass("glyphicon-align-justify");
 
  $(".main").css("margin-left","50px");
  $(".navbar-fixed-top").css("margin-left","50px");
	   }
	   else{
		   $(this).find("span").removeClass("glyphicon-th");
  $(this).find("span").addClass("glyphicon-align-justify");
		    $(".sidebar").css("left","0px");
 $(".sidebar").addClass("act");
  $(".main").css("margin-left","250px");
  $(".navbar-fixed-top").css("margin-left","250px");
		   }
        });   
        });
