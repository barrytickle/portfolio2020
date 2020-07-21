<?php
    $url = $_SERVER['REQUEST_URI'];
    if (strpos($url, 'case-study') !== false) {
 ?>
    <h2 class="centered"><span>Get in touch</span>What happens next?</h2>
    <p>If you liked the look of <span id="case-study"></span> feel free to get in touch and find out what type of project I can develop for you, or simply leave your feedback. The end goal of all my projects are to provide you with a service which performs as well as it looks. While providing small startup companies with a strong chance of increasing their online presence.</p>
<?php } ?>

<form class="flex flex-column contact-form" method="post" action="/includes/form/send.php" enctype="multipart/form-data">
  <div class="columns flex-justify--space_between two-col">
    <div class="form-group flex flex-column col">
      <label for="name">Full name</label>
      <input type="text" name="name" id="name" placeholder="e.g. John Doe">
    </div>
    <div class="form-group flex flex-column col">
      <label for="email">Email address</label>
      <input type="email" name="email" id="email" placeholder="e.g. johndoe@aol.co.uk">
    </div>
  </div>
  <div class="columns flex-justify--space_between two-col">
    <div class="form-group flex flex-column col">
      <label for="phone">Phone number (optional)</label>
      <input type="text" name="phone-number" id="phone" placeholder="e.g. 01925 123456">
    </div>
    <div class="form-group flex flex-column col">
      <label for="website">Your website (optional)</label>
      <input type="text" name="website" id="website" placeholder="e.g. www.barrytickle.com">
    </div>
  </div>
  <div class="form-group flex flex-column">
    <label for="comments">Your message</label>
    <textarea rows="3" name="comments" id="comments"></textarea>
  </div>
  <input type="hidden" name="url-sent" value="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>">
  <input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
  <div class="g-recaptcha" data-sitekey="6LeQ2EwUAAAAADCXUQYS-vwm9FzjDW57OdsQKPCj"></div>
  <div class="form-group">
    <button type="submit" class="btn btn-strong">Send form</button>
  </div>
</form>
