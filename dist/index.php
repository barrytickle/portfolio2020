<?php include 'includes/header.php' ?>
<header class="largeContainer columns two-col home-header">
  <div class="col">
    <h1>Hello, I'm Barry</h1>
    <p>
      I help clients to <b>achieve</b> their business objectives without spending large amounts of money by providing facilities like: Shared Web Hosting, Personal Bespoke Websites, Blogs, Ecommerce, Dashboard designs and SSL Certificates (in association with world's most trusted SSL providers).
    </p>
    <div class="flex flex-row">
      <div >
        <a href="/contact-me/" class="btn btn-outline">Let's get started</a>
      </div>
      <div>
        <a href="#learn-more" class="btn btn-outline">Learn more</a>
      </div>
    </div>
  </div>
  <div class="col">
    <img src="/images/portrait/me-pattern.png">
  </div>
</header>

<main>

  <section class="section-services largeContainer" id="learn-more">
    <h2 class="centered">
      <span>What do I do?</span>
      I do a lot of things, here are the
      services I can provide for you
    </h2>
    <div class="flex flex-row blob-icons">
        <div class="flex flex-column flex-align-center">
          <img src="/images/icons/blobs/seo.svg" alt="SVG icon" title="SVG icon">
          <h3>SEO Marketer</h3>
          <p>I help your website perform well, ensuring you rank high for the right search criteria in Google.</p>

        </div>
        <div class="flex flex-column flex-align-center">
          <img src="/images/icons/blobs/web-design.svg" alt="SVG icon" title="SVG icon">
          <h3>Web designer</h3>
          <p>I design then develop bespoke websites which your visitors will love becoming purely focused on conversion.</p>
        </div>
        <div class="flex flex-column flex-align-center">
          <img src="/images/icons/blobs/hosting.svg" alt="SVG icon" title="SVG icon">
          <h3>Hosting provider</h3>
          <p>I help you every step of the way, giving you the option to have your domain and hosting managed for a small sum each month.</p>
        </div>
    </div>
  </section>
  <div class="largeContainer mobileHidden" style="padding:0;">
    <img src="/images/background/seperator.png" style="width:100%;">
  </div>
  <section class="smallContainer case--study-blocks">
    <h2 class="centered">
      <span>Let's showcase</span>
      My builds and designs
    </h2>
    <div class="case-study--block_group">
      <div class="block">
        <a class="case-block blockHover" style="background-image:linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('/images/background/case-study/slt-for-kids.png');" href="/portfolio/case-study/slt-for-kids/">
          <h3>01 SLT for Kids</h3>
        </a>
      </div>
      <div class="block">
        <div class="row">
          <a class="case-block blockHover" style="background-image:linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('/images/background/case-study/oakley-and-friends.jpg');" href="/portfolio/case-study/oakley-and-friends/">
            <h3>02 Oakley and friends</h3>
          </a>
        </div>
        <div class="row">
          <a class="case-block blockHover" style="background-image:linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('http://evertonmatchshirt.com/wp-content/uploads/2019/05/59393541_551872112002506_4035969257538846720_n-1.jpg');" href="/portfolio/case-study/everton-match-shirt/">
            <h3>03 Everton Match Shirt</h3>
          </a>
          <a class="case-block blockHover" style="background-image:linear-gradient(rgba(0,0,0,0.3), rgba(0,0,0,0.3)), url('/images/case-study/txgroup-search/laptop-search-one.jpg');" href="/portfolio/case-study/txgroup-search/">
            <h3>04 Tx Group Search</h3>
          </a>
        </div>
      </div>
    </div>
    <div class="centered small-group">
      <p>My projects show what I can achieve as an individual, a project should be developed with the user in mind. As a web designer user interaction is one of my main aims when visiting a website, making a website look good should only be the first steps of developing a system for users. Explore my thought process throughout each of my projects, as this is a reflection of what I can provide for you as a service.</p>
      <a class="btn" href="/portfolio/">View all projects</a>
    </div>

  </section>
  <section class="largeContainer photography">
    <div class="two-col columns">
      <div class="col">
        <h2 class="centered">
          <span>Hobbies and interests</span>
          My photography hobbies
        </h2>
        <p>
          Photography has always been a huge interest of mine, I like to travel quite a lot and capturing memories of amazing scenery feels like more than just a hobby to me. I like being able to capture pictures of buildings at interesting angles which can capture the most detail. I love being able to find the history in scenery and monuments. Checkout the photography I have captured overtime, I would also be more than happy to even provide this as a service if you are local.
        </p>
        <a href="/photography/" class="btn">View my photography</a>
      </div>
      <div class="col">
          <?php
            $insta = json_decode(file_get_contents('https://social.physio123.co.uk/insta/?handle=barrytickle&filter=photography'));
            // echo '<pre>';print_r($insta);echo '</pre>';
            $insta = array_slice($insta, 0, 4);
          ?>
        <div class="photography-collage">
          <div class="row">
            <a class="photo blockHover" target="_blank" style="background-image:url('<?php echo $insta[0]->image; ?>');" href="<?php echo $insta[0]->url; ?>"></a>
            <a class="photo blockHover" target="_blank" style="background-image:url('<?php echo $insta[1]->image; ?>');" href="<?php echo $insta[1]->url; ?>"></a>
          </div>
          <div class="row">
            <a class="photo blockHover" target="_blank" style="background-image:url('<?php echo $insta[2]->image; ?>');" href="<?php echo $insta[2]->url; ?>"></a>
            <a class="photo blockHover" target="_blank" style="background-image:url('<?php echo $insta[3]->image; ?>');" href="<?php echo $insta[3]->url; ?>"></a>
          </div>
        </div>
      </div>
    </div>
  </section>
</main>
<?php include 'includes/footer.php' ?>
