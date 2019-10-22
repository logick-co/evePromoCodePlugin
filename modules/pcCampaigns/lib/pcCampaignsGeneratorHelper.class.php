<?php

/**
 * osApplication module helper.
 *
 * @package    e-venement
 * @subpackage osApplication
 * @author     Baptiste SIMON <baptiste.simon AT e-glop.net>
 * @version    SVN: $Id: helper.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class pcCampaignsGeneratorHelper extends BasePcCampaignsGeneratorHelper
{
  public function linkToExport($object, $params)
{
  return '<li class="sf_admin_action_export">'.link_to(
    '<span class="ui-icon ui-icon-arrowstop-1-s"></span>'.__($params['label'], array(), 'sf_admin'), 
    'pcCampaigns/export?id=' . $object->id, 
    $params['params'])
  .'</li>';
}
}
