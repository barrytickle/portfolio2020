<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.php' ?>
<header class="largeContainer contentHeader">
  <h1>My photography</h1>
  <div class="smallContainer">
    <p>Photography has always been a hobby of mine, capturing the best parts of the world helps capture memories and create another form of art outside of the web.</p> <h2>Already have a site?</h2><p>If you already have a website, please don't hesitate to <a href="/contact-me/">get in touch</a> with me, as I also offer services where I will come photograph your business for your existing site. This can be an addon if you require a new site, This is dependant on how far you are.</p>
    <p>If you are interested in this service, simply fill out the form below or <a href="/contact-me/">get in touch</a>.</p>
    <?php include $_SERVER['DOCUMENT_ROOT'].'/includes/contact-form.php';?>
  </div>
</header>
<main class="largeContainer">
  <?php
      $height = array('','grid-item--height2', 'grid-item--height3');
      $width = array('','grid-item--width2', 'grid-item--width3');
   ?>
  <?php
    $insta = file_get_contents('http://social.physio123.co.uk/insta/?handle=barrytickle&filter=photography');
    $insta = json_decode($insta);
  ?>
    <?php
      foreach($insta as $in){
     ?>
     <a class="grid-item instagram" href="<?php echo $in->image;?>" data-title="<?php echo $in->caption; ?>" data-lightbox="photography">
       <img src="<?php echo $in->image;?>">
     </a>
   <?php } ?>

    <?php
        function generate_photos($path){
          $folder = scandir($_SERVER['DOCUMENT_ROOT'].'/images/photography/'.$path);
          array_shift($folder);
          array_shift($folder);
          // print_r($folder);

            foreach($folder as $in){
              $image = "/images/photography/$path/$in";

              echo '<a class="grid-item '.$path.'" href="'.$image.'" data-lightbox="photography"><img src="'.$image.'"></a>';
            }
        }
     ?>

     <?php
        generate_photos('buildings');
        generate_photos('scenery');
      ?>

</main>
<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
