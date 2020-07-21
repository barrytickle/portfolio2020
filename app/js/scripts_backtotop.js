var pxScrolled = 200;
var duration = 500;

$(window).scroll(function() {
  if ($(this).scrollTop() > pxScrolled) {
    $('.fab-container').css({'bottom': '0px', 'transition': '.3s'});
    $(".hamburger-container").css({'bottom': '70px'});

  } else {
    $('.fab-container').css({'bottom': '-72px'});
    $(".hamburger-container").removeAttr('style');
  }
});

$('.top').click(function() {
  $('html, body').animate({scrollTop: 0}, duration);
})
