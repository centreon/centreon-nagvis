<?php

if (!isset($centreon)) {
  exit();
}

$path = dirname(__FILE__);
$roUser = 'centreon_nagvis';

/* Get nagvis path */
$query = 'SELECT `key`, `value` FROM `options` WHERE `key` IN ("centreon_nagvis_path", "centreon_nagvis_uri")';
$res = $pearDB->query($query);
if (PEAR::isError($res)) {
  echo '<div class="error">Error when getting information</div>';
  exit();
}

$nagvis_path = null;
$nagvis_uri = null;
while ($row = $res->fetchRow()) {
  if ($row['key'] == 'centreon_nagvis_path') {
    $nagvis_path = $row['value'];
  } elseif ($row['key'] == 'centreon_nagvis_uri') {
    $nagvis_uri = $row['value'];
  }
}

if (is_null($nagvis_path) || is_null($nagvis_uri)) {
  echo '<div class="error">Error when getting information</div>';
  exit();
}

if (substr($nagvis_path, -1) != '/') {
  $nagvis_path = $nagvis_path . '/';
}

function debug($msg) {
  $fh = fopen(DEBUGFILE, 'a');
  fwrite($fh, utf8_encode(microtime_float().' '.$msg."\n"));
  fclose($fh);
}

$userCentreon = $roUser;

/* Fix bad usage */
$_SERVER['SCRIPT_FILENAME'] = $nagvis_path . 'frontend/nagvis-js/index.php';
$tmpdir = getcwd();
chdir($nagvis_path . 'frontend/nagvis-js/');

require_once $nagvis_path . 'server/core/defines/global.php';
require_once $nagvis_path . 'server/core/defines/matches.php';
require_once $nagvis_path . 'server/core/functions/autoload.php';
require_once $nagvis_path . 'server/core/classes/CoreExceptions.php';
require_once $nagvis_path . 'server/core/functions/nagvisErrorHandler.php';
require_once $nagvis_path . 'server/core/functions/core.php';

/*
 * Init Global
 */
$core = GlobalCore::getInstance();
$listMap = $core->getAvailableMaps();

/* 
 * Create nagvis session 
 */
$AUTH = new CoreAuthHandler();
if ($AUTH->checkUserExists($userCentreon)) {
  $credential = array('user' => $userCentreon);
  $AUTH->setTrustUsername(true);
  $AUTH->setLogoutPossible(true);
  $AUTH->passCredentials($credential);
  $_SESSION['logonModule'] = cfg('global', 'logonmodule');
  $_SESSION['authModule'] = $AUTH->getModule();
  $_SESSION['authCredentials'] = $credential;
  $_SESSION['authTrusted'] = $AUTH->authedTrusted();
  $_SESSION['authLogoutPossible'] = $AUTH->logoutSupported();
}

chdir($tmpdir);
sort($listMap);

/* 
 * Reads map name from URL, if no parameter specified, default to first map of the list
 * WARNING: the "official" param name is "map", but we keep on reading "station" for compatibility reasons 
 * over first versions of this module. Some client are using URL with station, we cannot break compatibility
 */
$currentMap = '';
if (isset($_GET["map"])) {
  $currentMap = $_GET["map"];
  $mapurl = $nagvis_uri . '?mod=Map&context_menu=0&hover_menu=1&header_menu=0&show=' . $_GET["map"];
} else {
  if (isset($_GET["station"])) {
    $currentMap = $_GET["station"];
    $mapurl = $nagvis_uri . '?mod=Map&context_menu=0&hover_menu=1&header_menu=0&show=' . $_GET["station"];
  } else {
    if (isset($listMap[0])) {
      $mapurl = $nagvis_uri . '?mod=Map&context_menu=0&hover_menu=1&header_menu=0&show=' . $listMap[0];
    }
  }
}

/*
 * Init Smarty
 */
$tpl = new Smarty();
$tpl = initSmartyTpl($path, $tpl);

$tpl->assign('listmap', $listMap);
$tpl->assign('nagvis_uri', $nagvis_uri);
$tpl->assign('mapurl', $mapurl);
$tpl->assign('currentMap', $currentMap);

$tpl->display('nagvis.ihtml');

?>
