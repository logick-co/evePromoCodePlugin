<?php

class pcPromocodesActions extends apiActions
{
  /**
   * @return ApiEntityService
   */
  public function getMyService()
  {
      return $this->getService('campaign_service');
  }
  
  public function getApiMemberCardService()
  {
    return $this->getService('api_cart_pass_service');
  }
  
  public function getCartService()
  {
    return $this->getService('api_carts_service');
  }
  
  public function checkIsIdentified($transaction)
  {
    return $transaction->contact_id !== null;
  }
  
  public function executeActivate(sfWebRequest $request)
  {
    $cart_id = $request->getParameter('id', 0);
    
    $transaction = $this->getCartService()->getExpectedTransaction($cart_id);
    
    if ( !$this->checkIsIdentified($transaction) ) {
        throw new liApiAuthException('You need to be identified, please login', 30006);
    }
    
    $code = $request->getParameter('promocode', '');
    
    $valid_code = $this->getMyService()->checkCodeValidity($code);
    
    if ( $valid_code )
    {
      try {
        $this->getMyService()->addMemberCard($transaction, $valid_code->PromoCampaign);
        
        $activated_code = $this->getMyService()->activateCode($valid_code, $transaction->Contact);
      } catch(\Exception $e) {
        throw new liApiException($e->getMessage());
      }
      
      $result = $this->createJsonResponse([
        "code" => '200',
        'message' => 'Activation Succeeded',
        'errors' => []
      ], ApiHttpStatus::SUCCESS);
    }
    else
    {
      $result = $this->createJsonResponse([
        "code" => '400',
        'message' => 'Activation Failed',
        'errors' => ['Invalid code']
      ], ApiHttpStatus::BAD_REQUEST);
    }
    
    return $result;
  }
}
