$(window).scroll(function(){
  var scrollTop = $(window).scrollTop() ;

  // console.log(scrollTop);
  if(scrollTop >= 30){
    $(".main--nav").addClass('sticky');
  }else{
    $(".main--nav").removeClass('sticky');
  }
});
