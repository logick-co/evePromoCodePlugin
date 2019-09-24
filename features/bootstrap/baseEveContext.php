<?php

use Behat\Behat\Context\Context;
use PHPUnit\Framework\TestCase;

/**
 * Defines application features from the specific context.
 */
class baseEveContext extends TestCase implements Context
{
  const CAMPAIGN_SERVICE = 'campaign_service';
  const CAMPAIGN_NAME = 'PROMOCODES-TEST';
  
  protected $campaigns = [];
  protected $codes = [];
  protected $error = '';
  
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

  protected function getContext()
  {
    return sfContext::getInstance();
  }
  
  protected function getCampaignService()
  {
    return $this->getContext()->getContainer()->get(EvePromoCodeContext::CAMPAIGN_SERVICE);
  }

  protected function getFixtures($key)
  {
    $fixtures = sfYaml::load(__DIR__ . '/../../config/fixtures.yml');
    
    $data = $fixtures['fixtures'];
    
    if ( array_key_exists($key, $data) )
    {
      return $data[$key];
    }

    return null;
  }
  
  protected function createCampaign($name)
  {
    $this->getCampaignService()->removeCampaign($name);

    $fixtures = $this->getFixtures('campaign');

    $campaign = new PromoCampaign();
    $campaign->name = $name;
    $campaign->expiration = date('Y-m-d H:i:s', strtotime('+1 month'));
    $campaign->card_type_id = $fixtures['card_type_id'];
    $campaign->card_price_id = $fixtures['card_price_id'];
    
    $campaign->save();
    
    $this->campaigns[] = $campaign;
    $this->codes = [];
    
    return $campaign;
  }
  
}
