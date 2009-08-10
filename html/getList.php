<?
/**
 * getList - Get list from host, host- and servicegroups via API
 * 
 * @author	Michael Luebben	<michael_luebben@web.de>
 */
 
/**
 * securePostVar -  Cleanup variable from tags etc.
 * 
 * @param string $var
 * @author Michael Luebben	<michael_luebben@web.de>
 */
function secureVar($var) {
	$tmpVar=strip_tags($var);
	$tmpVar=htmlentities($tmpVar);
	$tmpVar=addslashes($tmpVar);
	return $tmpVar;
}	

// Array contains state codes for host state
$hostStateCode = array ('0' => 'UP',
						'1' => 'DOWN');

// Load required files
require_once('icinga-api/IcingaApi.php');
require_once('icinga-api/parser/Ido2dbParser.php');

// Instance ido2db parser
$ido2dbCfg = new Ido2dbParser();

// Array contians connections data for the IDO
$idoConfig = array (
					'type'			=> $ido2dbCfg->getConfigParameter('db_servertype'),
					'host'			=> $ido2dbCfg->getConfigParameter('db_host'),
					'database'		=> $ido2dbCfg->getConfigParameter('db_name'),
					'user'			=> $ido2dbCfg->getConfigParameter('db_user'),
					'password'		=> $ido2dbCfg->getConfigParameter('db_pass'),
					'table_prefix'	=> $ido2dbCfg->getConfigParameter('db_prefix'),
					'persistent'	=> true
					);

// Instance api object from class
$api = IcingaApi::getConnection(IcingaApi::CONNECTION_IDO, $idoConfig);

// Get type
$secureType=secureVar($_GET['type']);

// Seach host
if ($secureType == "host") {
	$secureHost=secureVar($_POST['host']);
	$apiRes = $api->createSearch()
		->setSearchTarget(IcingaApi::TARGET_HOST)
		->setResultColumns(array('HOST_NAME', 'HOST_CURRENT_STATE'))
		->setSearchFilter(HOST_NAME, '%'.$secureHost.'%', IcingaApi::MATCH_LIKE)
		->fetch();

	echo '<ul>';
		foreach($apiRes as $apiHandle){
			echo '<li><b>'.$apiHandle->host_name.'</b><span class="informal" id="listHOST'.$hostStateCode[$apiHandle->host_current_state].'"> ('.$hostStateCode[$apiHandle->host_current_state].')</span></li>';
		}
	echo '</ul>';
// Seach hostgroup
} elseif ($secureType == "hostgroup") {
	$secureHostgroup=secureVar($_POST['hostgroup']);
	$apiRes = $api->createSearch()
		->setSearchTarget(IcingaApi::TARGET_HOSTGROUP)
		->setResultColumns(array('HOSTGROUP_NAME','HOSTGROUP_ALIAS'))
		->setSearchFilter(HOSTGROUP_ALIAS, '%'.$secureHostgroup.'%', IcingaApi::MATCH_LIKE)
		->fetch();

	echo '<ul>';
		foreach($apiRes as $apiHandle){
			echo '<li><span class="informal" id="searchSelected"><b>'.$apiHandle->hostgroup_alias.'</b> (</span>'.$apiHandle->hostgroup_name.'<span class="informal" id="searchSelected">)</span></li>';
		}
	echo '</ul>';
// Seach servicegroup
} elseif ($secureType == "servicegroup") {
	$secureServicegroup=secureVar($_POST['servicegroup']);
	$apiRes = $api->createSearch()
		->setSearchTarget(IcingaApi::TARGET_SERVICEGROUP)
		->setResultColumns(array('SERVICEGROUP_NAME','SERVICEGROUP_ALIAS'))
		->setSearchFilter(SERVICEGROUP_ALIAS, '%'.$secureServicegroup.'%', IcingaApi::MATCH_LIKE)
		->fetch();

	echo '<ul>';
		foreach($apiRes as $apiHandle){
			echo '<li><span class="informal" id="searchSelected"><b>'.$apiHandle->servicegroup_alias.'</b> (</span>'.$apiHandle->servicegroup_name.'<span class="informal" id="searchSelected">)</span></li>';
		}
	echo '</ul>';
} else {
	echo '<ul>';
		echo '<li>Undefined type!</li>';
	echo '</ul>';
}
?>