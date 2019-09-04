<?php

class CampaignService
{

  public function listCampaigns()
  {
    return Doctrine::getTable('PromoCampaign')->findAll();
  }

  public function addCampaign($campaign_name)
  {
    $campaign = new PromoCampaign();
    $campaign->name = $campaign_name;
    $campaign->save();
    
    return $campaign;
  }
  
  public function removeCampaign($campaign_name)
  {
    $campaign = Doctrine::getTable('PromoCampaign')->findOneByName($campaign_name);
    
    if ($campaign) {
      $campaign->delete();
    }
  }
  
  public function generateCode($campaign, $length)
  {
    $code = new PromoCode();
    $code->code = $campaign->name . '-' . $this->getToken($length);
    $code->PromoCampaign = $campaign;
    
    return $code;
  }
  
  public function generateCodes($campaign, $length, $count)
  {
    $codes_collection = new Doctrine_Collection('PromoCode');
    $codes = [];
    
    for ($i=0; $i < $count; $i++) {
      $code = $this->generateCode($campaign, $length);
      $attempt = 0;
      
      while (array_key_exists($code->code, $codes)) {
        $code = $this->generateCode($campaign, $length);
        
        if (++$attempt > 10) {
          throw new \Exception('Too many attempts to generate unique codes');
        }
      }
      
      $codes[$code->code] = $code;
      $codes_collection->add($code);
    }
    
    return $codes_collection;
  }
  
  private function cryptoRandSecure($min, $max)
  {
      $range = $max - $min;
      if ($range < 1) return $min; // not so random...
      $log = ceil(log($range, 2));
      $bytes = (int) ($log / 8) + 1; // length in bytes
      $bits = (int) $log + 1; // length in bits
      $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
      do {
          $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
          $rnd = $rnd & $filter; // discard irrelevant bits
      } while ($rnd > $range);
      return $min + $rnd;
  }
  
  private function getToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet);

    for ($i=0; $i < $length; $i++) {
      $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max-1)];
    }

    return $token;
  }
}
