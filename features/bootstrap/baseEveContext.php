<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\TestCase;

/**
 * Defines application features from the specific context.
 */
class baseEveContext extends TestCase implements Context
{
  const CAMPAIGN_SERVICE = 'campaign_service';
  
  /**
   * Initializes context.
   *
   * Every scenario gets its own context instance.
   * You can also pass arbitrary arguments to the
   * context constructor through behat.yml.
   */
  public function __construct()
  {
    require_once(dirname(__FILE__).'/../../../behat4eve2Plugin/vendor/autoload.php');
    require_once(dirname(__FILE__).'/../../../../config/ProjectConfiguration.class.php');

    $configuration = ProjectConfiguration::getApplicationConfiguration('default', 'dev', true);
    sfContext::createInstance($configuration);
  }

  private function getContext()
  {
    return sfContext::getInstance();
  }
  
  private function getCampaignService()
  {
    return $this->getContext()->getContainer()->get(EvePromoCodeContext::CAMPAIGN_SERVICE);
  }

}
