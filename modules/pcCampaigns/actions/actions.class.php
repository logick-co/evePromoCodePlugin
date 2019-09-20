<?php

require_once dirname(__FILE__).'/../lib/pcCampaignsGeneratorConfiguration.class.php';
require_once dirname(__FILE__).'/../lib/pcCampaignsGeneratorHelper.class.php';

class pcCampaignsActions extends autoPcCampaignsActions
{
  
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
