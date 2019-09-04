<div class="sf_admin_list ui-grid-table ui-widget ui-corner-all ui-helper-reset ui-helper-clearfix">
  <table>
    <caption class="fg-toolbar ui-widget-header ui-corner-top">
      <h2><span class="ui-icon ui-icon-triangle-1-s"></span><?php echo __('Codes list') ?></h2>
    </caption>
    <thead class="ui-widget-header">
      <tr>
        <th class="sf_admin_date sf_admin_list_th_code ui-state-default ui-th-column">Code</th>
        <th class="sf_admin_text sf_admin_list_th_account ui-state-default ui-th-column">Account</th>
        <th class="sf_admin_text sf_admin_list_th_activation ui-state-default ui-th-column">Activation</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ( $form->getObject()->PromoCodes as $i => $code ): $odd = fmod(++$i, 2) ? 'odd' : '' ?>
      <tr class="sf_admin_row ui-widget-content <?php echo $odd ?>">
        <td class="sf_admin_date sf_admin_list_td_code"><?php echo $code->code ?></td>
        <td class="sf_admin_date sf_admin_list_td_account"><?php echo $code->Account ?></td>
        <td class="sf_admin_date sf_admin_list_td_activation"><?php echo $code->activation_date ?></td>
      </tr>
      <?php endforeach ?>
    </tbody>
  </table>
</div>
