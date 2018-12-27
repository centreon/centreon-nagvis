<?php

require_once $centreon_path . "/www/class/centreonLang.class.php";
$centreonLang = new CentreonLang($centreon_path, $centreon);
$centreonLang->bindLang();
$centreonLang->bindLang('messages');

if (!isset($centreon)) {
  exit();
}

$path = dirname(__FILE__);

/* Read module options */
$query = 'SELECT `key`, `value` FROM `options` WHERE `key` IN ("centreon_nagvis_path", "centreon_nagvis_uri", "centreon_nagvis_auth", "centreon_nagvis_single_user")';
try {
    $res = $pearDB->query($query);
} catch (\PDOException $e) {
    echo '<div class="error">'._('Error when getting Centreon-NagVis module options').'</div>';
    exit();
}

$nagvis_path = null;
$nagvis_uri = null;
while ($row = $res->fetch()) {
    if ($row['key'] == 'centreon_nagvis_path') {
        $nagvis_path = $row['value'];
    } elseif ($row['key'] == 'centreon_nagvis_uri') {
        $nagvis_uri = $row['value'];
    } elseif ($row['key'] == 'centreon_nagvis_auth') {
        $centreon_nagvis_auth = $row['value'];
    } elseif ($row['key'] == 'centreon_nagvis_single_user') {
        $centreon_nagvis_single_user = $row['value'];
    }
}

if (is_null($nagvis_path) || is_null($nagvis_uri)) {
  echo '<div class="error">'._('Error when getting NagVis path').'</div>';
  exit();
}

if (substr($nagvis_path, -1) != '/') {
  $nagvis_path = $nagvis_path . '/';
}

// Loading NagVis authentication type from options
$single_nagvis_user = $centreon_nagvis_auth === 'single';

/* Fix bad usage */
$_SERVER['SCRIPT_FILENAME'] = $nagvis_path . 'frontend/nagvis-js/index.php';
$tmpdir = getcwd();
chdir($nagvis_path . 'frontend/nagvis-js/');

// We now know where NagVis is located, so we can load it
require_once $nagvis_path . 'server/core/defines/global.php';
require_once $nagvis_path . 'server/core/defines/matches.php';
require_once $nagvis_path . 'server/core/functions/autoload.php';
require_once $nagvis_path . 'server/core/classes/CoreExceptions.php';
require_once $nagvis_path . 'server/core/functions/nagvisErrorHandler.php';
require_once $nagvis_path . 'server/core/functions/core.php';


/* Init NagVis */
$core = GlobalCore::getInstance();

if ($single_nagvis_user) {
    // Same user for all, read from options
    $userCentreon = $centreon_nagvis_single_user;
} else {
    // User connected in Centreon
    $userCentreon = $centreon->user->alias;
}

$error = '';
$listMap = array();

/* 
 * Create nagvis session to get list of available maps for the user
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

    if ($single_nagvis_user) {
        $listMap = $core->getAvailableMaps();
    } else {
        if ($AUTH->isAuthenticated()) {
            $AUTHORISATION = new CoreAuthorisationHandler();
            $AUTHORISATION->parsePermissions();

            $core->setAA($AUTH, $AUTHORISATION);
            $list = Array();
            $maps = $core->getAvailableMaps();
            foreach ($maps AS $key => $val) {
                if ($core->getAuthorization()->isPermitted('Map', 'view', $val)) {
                    $list[$key] = $val;
                }
            }
            if (empty($list)) {
                $error =  _("No map available");
            } else {
                $listMap = $list;
            }
        } else {
            $AUTHORISATION = null;
            $error = _("NagVis authentication failed");
        }
    }
} else {
    $error = _("No such user in NagVis: ") . $userCentreon;
}

chdir($tmpdir);
sort($listMap);

/* 
 * Reads map name from URL, if no parameter specified, default to first map of the list
 * WARNING: the "official" param name is "map", but we keep on reading "station" for compatibility reasons 
 * over first versions of this module. Some client are using URL with station, we cannot break compatibility
 */
$currentMap = '';
$mapurl = '';
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

$tpl->assign('error', $error);
$tpl->assign('listmap', $listMap);
$tpl->assign('nagvis_uri', $nagvis_uri);
$tpl->assign('mapurl', $mapurl);
$tpl->assign('currentMap', $currentMap);

$tpl->display('nagvis.ihtml');

function debug($msg) {
    $fh = fopen(DEBUGFILE, 'a');
    fwrite($fh, utf8_encode(microtime_float().' '.$msg."\n"));
    fclose($fh);
}

?>
