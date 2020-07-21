<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

function clean_email($string){
  $string = str_replace('animal_choice', 'animal choice', $string);
  $string = str_replace('dobuk', 'DOB', $string);
  $string = str_replace('animal_answer', 'Animal answer', $string);
  return $string;
}


          require_once('class.phpmailer.php');


          if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            function post_captcha($user_response) {
                $fields_string = '';
                $fields = array(
                    'secret' => '6LeQ2EwUAAAAADCo6r-ItKqtvHjMUAkEcVVUZsIL',
                    'response' => $user_response
                );
                foreach($fields as $key=>$value)
                $fields_string .= $key . '=' . $value . '&';
                $fields_string = rtrim($fields_string, '&');

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
                curl_setopt($ch, CURLOPT_POST, count($fields));
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, True);

                $result = curl_exec($ch);
                curl_close($ch);

                return json_decode($result, true);
            }

            // Call the function post_captcha
            $res = post_captcha($_POST['g-recaptcha-response']);

            if ($res['success']) {
                $temp_row = array();
                $post = $_POST;
                $email_usr = $_POST['email'];
                $name = $_POST['name'];
                foreach($post as $key => $value){
                  $row = file_get_contents('row-template.html');
                  $row = str_replace('{{key}}', ucfirst(clean_email($key)), $row);
                  $row = str_replace('{{value}}', $value, $row);
                  array_push($temp_row, $row);
                }

                $content = implode('', $temp_row);
                $message = file_get_contents('email.html');
                $message = str_replace('{{row-template}}', $content, $message);

                $email = new PHPMailer();
                $email->CharSet = 'UTF-8';
                $email->IsHTML(true);
                $email->From      = $email_usr;
                $email->FromName  = $name;
                $email->Subject   = 'Portfolio enquiry from '.$name;
                $email->Body      = $message;
                $email->AddAddress( 'barry.tickle12@gmail.com' );

                $name = explode(' ', $name);
                $name = $name[0];
                $message2 = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/includes/form/email-return.html');

                $message2 = str_replace('{{fname}}', $name, $message2);

                $mail = new PHPMailer();
                $mail->CharSet = 'UTF-8';
                $mail->IsHTML(true);
                $mail->From      = 'noreply@barrytickle.com';
                $mail->FromName  = 'Barry Tickle';
                $mail->Subject   = $name. ', thank you for getting in touch with me';
                $mail->Body      = $message2;
                $mail->AddAddress($email_usr);
                $mail->send();
                if($email->send()){
                die('<script>document.location.href="/thank-you/";</script>');
                // http_response_code(200);
                }else{
                http_response_code(404);
                }
            }
          }


          ?>
