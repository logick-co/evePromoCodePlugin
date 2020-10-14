<?php 

class evePromoCodePluginConfiguration extends sfPluginConfiguration
{
  public function initialize()
  {
    $this->initializeSubmenus();

    $this->initializeAutoload();

    return parent::initialize();
  }
  
  public function initializeSubmenus()
  {
    $this->configuration->appendMenus(array(
      'setup_extra' => array(
        'Promotional campaigns' => array(
          'url'   => array(
            'app' => 'tck',
            'route' => 'promo_campaigns/index'
          ),
          'credentials' => array('pr-card-promo-edit'),
          'i18n'  => 'pc_campaigns',
        ),
      ),
      'ticketting' => array()
    ));
  }
}
