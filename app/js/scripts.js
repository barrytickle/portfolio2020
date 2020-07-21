$(document).ready(function(){
  $("#hamburger, .menu-bg").click(function(){
    if($(this).hasClass('menu-bg')){
      if(!$(this).parent().hasClass('active')){
        $(this).parent().toggleClass('active');
        $("html, body").toggleClass('no-move');
      }
    }else{
      $(this).parent().toggleClass('active');
      $("html, body").toggleClass('no-move');
      $(".menu > nav").toggleClass('menu-active');

      function animate(){
        $("nav a").each(function(i){
          setTimeout(function(){
            $("nav a").eq(i).toggleClass('fadeInLeft');
          }, 100 * (i + 1));
        });
      }
      setTimeout(animate, 0);
    }
  });


  $("a").on('click', function(event) {

      // Make sure this.hash has a value before overriding default behavior
      if (this.hash !== "") {
        // Prevent default anchor click behavior
        event.preventDefault();

        // Store hash
        var hash = this.hash;

        // Using jQuery's animate() method to add smooth page scroll
        // The optional number (800) specifies the number of milliseconds it takes to scroll to the specified area
        $('html, body').animate({
          scrollTop: $(hash).offset().top
        }, 800, function(){

          // Add hash (#) to URL when done scrolling (default click behavior)
          window.location.hash = hash;
        });
      } // End if
    });

    if($(".isotope-grid").length > 0){
        var $grid = $('.isotope-grid').isotope({
          // options
          itemSelector: '.portfolio-group',
          layoutMode: 'fitRows'
        });
      }

    $('.isotope-list').on( 'click', 'span', function() {
      $(".isotope-list .active").removeClass('active');
      $(this).addClass('active');
        var filterValue = $(this).attr('data-filter');
        $grid.isotope({ filter: filterValue });
      });


      if (document.cookie.replace(/(?:(?:^|.*;\s*)gt_accepted\s*\=\s*([^;]*).*$)|^.*$/, "$1") != "yes") {
        $('.cookie-wrapper').fadeIn(500);
      };
      $('#cookie_accept_btn').click(function() {
          document.cookie = "gt_accepted=yes; expires=Mon, 24 Jun 2022 15:00:00 GMT; path=/";
          $('.cookie-wrapper').fadeOut(350);
        });


      if($("#case-study").length > 0){
        $("#case-study").html($("h1").text());
      }


      var articles = $(".blog-container .blog-post");
      // Loop through all the spans
      // for (var i = 0; i < articles.length; i += 2) {
      //
      //     // Create dynamic div
      //     var $div = $("<div/>", {
      //         class: 'blog-row flex flex-justify--space_between flex-row'
      //     });
      //
      //     // Wrap all the spans inside the div
      //     var $row = $(".blog-row").length+1;
      //     // console.log($(".blog-row").length);
      //     if($row % 3 == 0){
      //       articles.slice(i, i + 1).wrapAll($div);
      //       // articles.slice(i, i + 2).wrapAll($div);
      //     }else{
      //       articles.slice(i, i + 2).wrapAll($div);
      //     }
      // }

      $(".social-group .icon-image").hover(function(){
        var src = $(this).attr('src');
        if(src.indexOf('-white') !== -1){
          src = src.replace('-white','-blue');
        }else{
          src = src.replace('-blue', '-white');
        }
        $(this).attr('src', src);
      });


});
