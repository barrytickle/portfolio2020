$(document).ready(function(){
  if($(".case-study--blocks").length > 0){
    $(".case-study--blocks div").each(function(){
      var logo = $(this).data('logo');
      var color = $(this).data('background');
      $(this).attr('style', 'background-color:'+color)
      $(this).append('<img src="/images/case-study/logos/'+logo+'">');
    });
  }
});
