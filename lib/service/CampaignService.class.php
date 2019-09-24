<?php

class CampaignService
{
  public function activateCode(PromoCode $code, Contact $account)
  {
    $code->activation_date = date('Y-m-d H:i:s');
    $code->Account = $account;
    $code->save();
    
    return $code;
  }
  
  public function checkCodeValidity($code)
  {
    $code_exists = Doctrine::getTable('PromoCode')->createQuery('co')
      ->innerJoin('co.PromoCampaign pc')
      ->andWhere('co.code = ?', $code)
      ->andWhere('pc.expiration > now()')
      ->andWhere('pc.card_type_id IS NOT NULL')
      ->andWhere('co.activation_date IS NULL')
      ->andWhere('co.account_id IS NULL')
      ->fetchOne();
    
    return $code_exists;
  }

  public function addMemberCard(Transaction $transaction, PromoCampaign $campaign)
  {
    $card = new MemberCard;
    $card->transaction_id = $transaction->id;
    $card->member_card_type_id = $campaign->card_type_id;
    $card->contact_id = $transaction->contact_id;
    $card->active = false;
    $card->detail = $campaign->name;
    
    $bp = new BoughtProduct;
    $bp->Declination = $campaign->MemberCardType->ProductDeclination;
    $bp->Price = $campaign->Price;
    $bp->value = $campaign->Price->value;
    $bp->Transaction = $transaction;
    $bp->Transaction->contact_id = $transaction->contact_id;
    
    $card->BoughtProducts->add($bp);    
    $transaction->MemberCards->add($card);    
    $card->save();
    
    return $card;
  }

  public function listCampaigns()
  {
    return Doctrine::getTable('PromoCampaign')->findAll();
  }

  public function addCampaign($campaign_name)
  {
    $campaign = new PromoCampaign();
    $campaign->name = $campaign_name;
    $campaign->expiration = date('Y-m-d', strtotime('+1 year'));
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
