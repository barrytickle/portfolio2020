<?php
  require $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
  \Stripe\Stripe::setApiKey('sk_live_qRJtiM78PdbXQtdDQ1yhEPGp');
  print_r($_POST);
  $token = $_POST['stripeToken'];
  $email = $_POST['email'];
  $name = $_POST['name'];

  $customer = \Stripe\Customer::create([
      'description' => $name,
      'email' => $email
  ]);

  $customer_id = $customer['id'];

  $card = \Stripe\Customer::createSource(
    $customer_id,
    ['source' => $token]
  );
    die('<script>document.location.href="/thank-you"');
  ?>
