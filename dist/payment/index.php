<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/header.php' ?>
<main class="largeContainer contactContainer">
  <article class="columns flex-justify--space_between two-col">
    <section class="col">
      <h1>
        <span>Sign up</span>
        Sign up for hosting
      </h1>
      <form class="flex flex-column contact-form" method="post" action="/payment/stripe.php" id="payment-details" enctype="multipart/form-data">
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
        <div class="form-group flex flex-column">
          <div id="card-element" class="MyCardElement" style="border:1px solid #d2d2d2; border-radius:5px; padding:10px 20px;">
             <!-- Elements will create input elements here -->
           </div>

           <!-- We'll put the error messages in this element -->
           <div id="card-errors" role="alert"></div>
        </div>
        <input type="hidden" name="url-sent" value="<?php echo "http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";?>">
        <input type="hidden" name="ip" value="<?php echo $_SERVER['REMOTE_ADDR']; ?>">
        <div class="form-group">
          <button type="submit" class="btn btn-strong" id="order-button">Sign up</button>
        </div>
      </form>
    </section>
    <aside class="col">
      <h3>Where am I based?</h3>
      <p>You can find me in <b>Newton-Le-Willows, Merseyside</b>. Are you from this area? If so feel free to mention this in the form if meeting up is more preferable to yourself.</p>
      <h3>Want to contact me other ways?</h3>
      <p>
        Sure go ahead, i'm also available through these other methods
      </p>
      <p>
        <b>Call:</b> 07707946303<br>
        <b>Email:</b> <a href="mailto:barry.tickle12@gmail.com">barry.tickle12@gmail.com</a>
      </p>
    </aside>
  </article>
</main>
<script src="https://js.stripe.com/v3/"></script>

<?php include $_SERVER['DOCUMENT_ROOT'].'/includes/footer.php' ?>
<script>
  var stripe = Stripe('pk_live_gg48nxUmdlJLPpgA621rlOTk');
  // Create a Stripe client.
  // https://cartalyst.com/manual/stripe-laravel/4.0
  // var stripe = Stripe('pk_live_aCNH7LN4p1gMUb7g5uRBoZh5');

  // Create an instance of Elements.
  var elements = stripe.elements();

  // Custom styling can be passed to options when creating an Element.
  // (Note that this demo uses a wider set of styles than the guide below.)
  var style = {
    base: {
      color: '#32325d',
      fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
      fontSmoothing: 'antialiased',
      fontSize: '16px',
      '::placeholder': {
        color: '#aab7c4'
      }
    },
    invalid: {
      color: '#fa755a',
      iconColor: '#fa755a'
    }
  };

  // Create an instance of the card Element.
  var card = elements.create('card', {style: style});

  // Add an instance of the card Element into the `card-element` <div>.
  card.mount('#card-element');

  // Handle real-time validation errors from the card Element.
  card.addEventListener('change', function(event) {
    var displayError = document.getElementById('card-errors');
    if (event.error) {
      displayError.textContent = event.error.message;
    } else {
      displayError.textContent = '';
    }
  });



  function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = document.getElementById('payment-details');
    var hiddenInput = document.createElement('input');
    hiddenInput.setAttribute('type', 'hidden');
    hiddenInput.setAttribute('name', 'stripeToken');
    hiddenInput.setAttribute('value', token.id);
    form.appendChild(hiddenInput);
    // Submit the form
    form.submit();

  }
</script>


<script>
$("#order-button").click(function(e){
  e.preventDefault();
  stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the user if there was an error.
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
    } else {
      // Send the token to your server.
      stripeTokenHandler(result.token);
    }
  });
});
</script>
