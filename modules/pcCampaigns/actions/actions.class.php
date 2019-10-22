<?php

require_once dirname(__FILE__).'/../lib/pcCampaignsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pcCampaignsGeneratorHelper.class.php';

class pcCampaignsActions extends autoPcCampaignsActions
{
  
  public function executeExport(sfWebRequest $request)
  {
    $this->getContext()->getConfiguration()->loadHelpers('I18N');
    
    $campaign_id = intval($request->getParameter('id'));
    $codes = Doctrine::getTable('PromoCode')->createQuery('c')
      ->innerJoin('c.PromoCampaign ca')
      ->leftJoin('c.Account a')
      ->select('ca.name, c.code, c.activation_date, a.email as contact')
      ->andWhere('c.campaign_id = ?', $campaign_id)
      ->fetchArray()
    ;
    
    $this->codes = [];
    $campaign_name = sprintf('%s-%s.csv', __('promo-codes-campaign', [], 'pc_campaigns'), strtolower($codes[0]['PromoCampaign']['name']));
    
    foreach ($codes as $key => $code)
    {
      $this->codes[$key] = [
        'campaign' => $code['PromoCampaign']['name'],
        'code' => $code['code'],
        'contact' => $code['contact'],
        'activation' => $code['activation_date']
      ];
    }
    
    $this->format = false;
    
    $this->getResponse()->setHttpHeader('Content-Disposition', 'attachment; filename="' . $campaign_name . '"');
    
    if ( !$request->hasParameter('debug') )
    {
      sfConfig::set('sf_web_debug', false);
    }
    else
    {
      $this->getResponse()->sendHttpHeaders();
      $this->setLayout('layout');
    }
  }
  
  public function executeCreate(sfWebRequest $request)
  {
    parent::executeCreate($request);
    
    if ( $this->form->isValid() )
    {
      $this->promo_campaign->PromoCodes = $this->getCampaignService()->generateCodes($this->promo_campaign, 6, $this->promo_campaign->codes_count);
      $this->promo_campaign->save();
      
      $this->redirect(array('sf_route' => 'pc_campaigns_show', 'sf_subject' => $this->promo_campaign));  
    }
  }
  
  protected function processForm(sfWebRequest $request, sfForm $form)
  {
    $form->bind($request->getParameter($form->getName()), $request->getFiles($form->getName()));
    if ($form->isValid())
    {
      $notice = $form->getObject()->isNew() ? 'The item was created successfully.' : 'The item was updated successfully.';

      $this->promo_campaign = $form->save();

      $this->dispatcher->notify(new sfEvent($this, 'admin.save_object', array('object' => $this->promo_campaign)));

      if ($request->hasParameter('_save_and_add'))
      {
        $this->getUser()->setFlash('notice', $notice.' You can add another one below.');

        $this->redirect('@pc_campaigns_new');
      }
      else
      {
        $this->getUser()->setFlash('notice', $notice);
      }
    }
    else
    {
      $this->getUser()->setFlash('error', 'The item has not been saved due to some errors.', false);
      $this->setTemplate('new');
    }
  }
  
  private function getCampaignService()
  {
    return $this->getContext()->getContainer()->get('campaign_service');
  }
}
