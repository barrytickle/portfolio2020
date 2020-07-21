<?php

function clean_text($text){
$text = str_replace('.php', '', $text);
$text = str_replace('-', ' ', $text);
$text = str_replace('index', 'Home', $text);
$text = str_replace(' ot', ' OT', $text);
$text = str_replace('manchester', 'Manchester', $text);
$text = str_replace('street', 'Street,', $text);
$text = str_replace('sale', 'Sale', $text);
$text = str_replace('john', 'John', $text);
$text = str_replace('road', 'Road,', $text);
$text = str_replace('dcd', 'DCD', $text);
$text = str_replace('Asltip', 'ASLTIP', $text);
$text = str_replace('Rcslt', 'RCSLT', $text);
$text = str_replace('Hcpc', 'HCPC', $text);
$text = str_replace('mcelroy', 'McElroy', $text);
$text = str_replace('osullivan', "O'Sullivan", $text);
$text = str_replace('5 12', '5-12', $text);
$text = str_replace('0 12', '0-12', $text);
$text = str_replace('0 2', '0-2', $text);
$text = str_replace('13 18', '13-18', $text);
$text = str_replace('3 5', '3-5', $text);
$text = str_replace('Faq', 'FAQ', $text);
return $text;
}

function generate_title(){
  $crumbs = explode("/",$_SERVER["REQUEST_URI"]);
   array_pop($crumbs);
   array_shift($crumbs);

   $crumbs = array_reverse($crumbs);
   function cleaner($crumb){
     $crumb = str_replace('.php', '_', $crumb);
     $crumb = str_replace('', ' ', $crumb);
     $crumb = str_replace('-', ' ', $crumb);
     return $crumb;
   }

   if(empty($crumbs)){
     echo 'Barry Tickle | Freelance web designer & developer in Newton-Le-Willows, Merseyside';
   }else{
     foreach($crumbs as $crumb){
         echo clean_text(ucfirst(cleaner($crumb)) . ' | ');
     }
     echo 'Barry Tickle | Freelance web designer & developer in Newton-Le-Willows, Merseyside';
   }

}



?>
