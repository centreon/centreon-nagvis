<?php

if (!isset($oreon)) {
  exit();
}

function nagvisInstall($dir) {
  if (is_dir($dir)) {
    return true;
  }
  return false;
}

require_once 'HTML/QuickForm.php';
require_once 'HTML/QuickForm/Renderer/ArraySmarty.php';

$path = './modules/centreon-nagvis/';

$attrsTextLong = array("size" => "50");

$form = new HTML_QuickForm('Form', 'post', '?p=' . $p);
$form->addElement('header', 'title', _('Centreon Nagvis configuration'));
$form->addElement('header', 'information', _('Nagvis information'));
$form->addElement('text', 'centreon_nagvis_uri', _('Nagvis URI'), $attrsTextLong);
$form->addElement('text', 'centreon_nagvis_path', _('Nagvis Path'), $attrsTextLong);

$form->addRule('centreon_nagvis_uri', _('Compulsory field'), 'required');
$form->addRule('centreon_nagvis_path', _('Compulsory field'), 'required');
$form->registerRule('exist', 'callback', 'nagvisInstall');

$form->addRule('centreon_nagvis_path', _('Directory does not exist'), 'exist');
$form->setRequiredNote("<font style='color: red;'>*</font>" . _(" Required fields"));

$form->addElement('submit', 'submitC', _("Save"));
$form->addElement('reset', 'reset', _("Reset"));

if ($form->validate()) {
  $values = $form->getSubmitValues();
  $queryInsert = 'UPDATE `options` SET `value` = "%s" WHERE `key` = "%s"';
  $pearDB->query(sprintf($queryInsert, $pearDB->escape($values['centreon_nagvis_uri']),  'centreon_nagvis_uri'));
  $pearDB->query(sprintf($queryInsert, $pearDB->escape($values['centreon_nagvis_path']), 'centreon_nagvis_path'));
}

/*
 * Get options
 */
if (!isset($values)) {
  $values = array();
  $query = 'SELECT `key`, `value` FROM `options` WHERE `key` IN ("centreon_nagvis_uri", "centreon_nagvis_path")';
  $res = $pearDB->query($query);
  if (!PEAR::isError($res)) {
    while ($row = $res->fetchRow()) {
      $values[$row['key']] = $row['value'];
    }
  }
}
$form->setDefaults($values);

/*
 *
 * Smarty template Init
 *
 */
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);
$tpl->assign('p', $p);

$renderer = new HTML_QuickForm_Renderer_ArraySmarty($tpl, true);
$renderer->setRequiredTemplate('{$label}&nbsp;<font color="red" size="1">*</font>');
$renderer->setErrorTemplate('<font color="red">{$error}</font><br />{$html}');
$form->accept($renderer);
$tpl->assign('form', $renderer->toArray());
$tpl->display("nagvis-config.ihtml");

?>