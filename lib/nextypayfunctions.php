<?php

class Nextypayfunctions{
  public static $instance;

/**
 * @param  object  $registry  Registry Object
 */

  public static function get_instance($registry) {
    if (is_null(static::$instance)) {
      static::$instance = new static($registry);
    }

    return static::$instance;
  }

  public function getQRText($toWallet, $uoid, $ntyAmount)
  {
      return '{"walletaddress":"'.$toWallet.'","uoid":"'.$uoid.'","amount":"'.$ntyAmount.'"}';
  }

  public function getQRHex($QRtext){
      return "0x".$this->strToHex($QRtext);
  }

  public function getQRUrl($QRtext) {
      $QRTextEncode = urlencode($QRText);
      $QRUrl = 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl='.$QRTextEncode.'&choe=UTF-8';
      return $QRUrl;
  }

  public function strToHex($string){

  	$hex = '';
  	for ($i = 0; $i < strlen($string); $i++){
  		$ord = ord($string[$i]);
  		$hexCode = dechex($ord);
  		$hex .= substr('0' . $hexCode, -2);
  	}
  	return strToLower($hex);

  }

  public function hexToStr($hex){

      $string = '';
      for ($i = 0; $i < strlen($hex) - 1; $i += 2){
          $string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
      }
      return $string;

  }

  public function key_filter($key){
    $delete_list=array('"','“','″','”',' ','{','}');
    return str_replace($delete_list, '',$key);
  }
}
?>
