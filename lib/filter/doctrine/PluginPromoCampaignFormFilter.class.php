<?php

/**
 * PluginPromoCampaign form.
 *
 * @package    ##PROJECT_NAME##
 * @subpackage filter
 * @author     ##AUTHOR_NAME##
 * @version    SVN: $Id: sfDoctrineFormFilterPluginTemplate.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
abstract class PluginPromoCampaignFormFilter extends BasePromoCampaignFormFilter
{
  public function setup()
  {
    parent::setup();
    
    $this->widgetSchema->getFormFormatter()->setTranslationCatalogue('pc_campaigns');
  }
}
