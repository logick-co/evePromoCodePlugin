<?php

/**
 * PluginPromoCampaign form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage form
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginPromoCampaignForm extends BasePromoCampaignForm
{
  public function setup()
  {
    parent::setup();

    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('pc_campaigns');

    $this->widgetSchema['card_price_id']->setOption('query', Doctrine::getTable('Price')->createQuery('p')
      ->innerJoin('p.Products pp')
      ->orderBy('pt.name'));
  }
  
  public function configure()
  {
    parent::configure();
    
    $this->validatorSchema['name']->setOption('required', true);
    $this->validatorSchema['expiration']->setOption('required', true);
    $this->validatorSchema['card_type_id']->setOption('required', true);
    $this->validatorSchema['card_price_id']->setOption('required', true);
  }
}
