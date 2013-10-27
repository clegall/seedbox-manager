<?php

function chargerClasse($classe) { require_once($classe.'.class.php'); }
spl_autoload_register('chargerClasse');

if ( isset($_SERVER['REMOTE_USER']) || isset($_SERVER['PHP_AUTH_USER']) )
    $userName = isset($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER']:$_SERVER['PHP_AUTH_USER'];
else
    $userName = 'magicalex';//die('Le script n\'est pas prot&eacute;g&eacute; par une authentification.<br>V&eacute;rifiez la configuration de votre serveur web.');

if ( file_exists('../conf/users/'.$userName.'/config.ini') )
    $file_user_ini = '../conf/users/'.$userName.'/config.ini';
else
    $file_user_ini = '../conf/config.ini';

// initialisation des objets
$user = new Users($file_user_ini, $userName );
$serveur = new Server($file_user_ini, $userName);

$host = $_SERVER['HTTP_HOST'];
$folder_scgi = '/'.strtoupper(substr($userName,0,3)).'0';
$title_seedbox = 'Seedbox '.$userName;
$passwd = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW']:null;

// setting transdroid
$tableau_conf_trandroid = array(
    'ui_swipe_labels' => false,
    'alarm_vibrate' => false,
    'alarm_enabled' => false,
    'alarm_check_rss_feeds' => false,
    'websites' => array(),
    'ui_refresh_interval' => '60',
    'ui_hide_refresh' => false,
    'search_sort_by' => 'sort_seeders',
    'alarm_play_sound' => false,
    'ui_enable_ads' => true,
    'ui_only_show_transferring' => false,
    'search_num_results' => '25',
    'servers' => array(array(
        'port' => '443',
        'host' => $host,
        'ssl' => true,
        'type' => 'daemon_rtorrent',
        'password' => $passwd,
        'os_type' => 'type_linux',
        'folder' => $folder_scgi,
        'username' => $userName,
        'use_auth' => true,
        'name' => $title_seedbox,
        'base_ftp_url' => 'ftp://'.$userName.'@'.$host.'/torrents/',
        'download_alarm' => true,
        'new_torrent_alarm' => true,
        'ssl_accept_all' => true
        )),

    'alarm_interval' => '600000',
    'rssfeeds' => array(),
    'ui_ask_before_remove' => true
);

$conf_json_trandroid = json_encode($tableau_conf_trandroid, JSON_PRETTY_PRINT);

// setting filezilla
$conf_xml_filezilla = '<?xml version="1.0" encoding="UTF-8" standalone="yes" ?>'."\n".
'<FileZilla3>'."\n".
    '<Servers>'."\n".
        '<Server>'."\n".
            '<Host>'.$host.'</Host>'."\n".
            '<Port>'.$user->portFtp().'</Port>'."\n".
            '<Protocol>0</Protocol>'."\n".
            '<Type>0</Type>'."\n".
            '<User>'.$userName.'</User>'."\n".
            '<Pass>'.$passwd.'</Pass>'."\n".
            '<Logontype>1</Logontype>'."\n".
            '<TimezoneOffset>0</TimezoneOffset>'."\n".
            '<PasvMode>MODE_DEFAULT</PasvMode>'."\n".
            '<MaximumMultipleConnections>0</MaximumMultipleConnections>'."\n".
            '<EncodingType>UTF-8</EncodingType>'."\n".
            '<BypassProxy>0</BypassProxy>'."\n".
            '<Name>'.$title_seedbox.' ftp</Name>'."\n".
            '<Comments />'."\n".
            '<LocalDir />'."\n".
            '<RemoteDir />'."\n".
            '<SyncBrowsing>0</SyncBrowsing>'.$title_seedbox.' ftp&#x0A;'."\n".
        '</Server>'."\n".
        '<Server>'."\n".
            '<Host>'.$host.'</Host>'."\n".
            '<Port>'.$user->portSftp().'</Port>'."\n".
            '<Protocol>1</Protocol>'."\n".
            '<Type>0</Type>'."\n".
            '<User>'.$userName.'</User>'."\n".
            '<Pass>'.$passwd.'</Pass>'."\n".
            '<Logontype>1</Logontype>'."\n".
            '<TimezoneOffset>0</TimezoneOffset>'."\n".
            '<PasvMode>MODE_DEFAULT</PasvMode>'."\n".
            '<MaximumMultipleConnections>0</MaximumMultipleConnections>'."\n".
            '<EncodingType>Auto</EncodingType>'."\n".
            '<BypassProxy>0</BypassProxy>'."\n".
            '<Name>'.$title_seedbox.' sftp</Name>'."\n".
            '<Comments />'."\n".
            '<LocalDir />'."\n".
            '<RemoteDir />'."\n".
            '<SyncBrowsing>0</SyncBrowsing>'.$title_seedbox.' sftp&#x0A;'."\n".
        '</Server>'."\n".
    '</Servers>'."\n".
'</FileZilla3>';

if ( $_GET['file'] == 'transdroid' )
    $serveur->FileDownload('settings.json', $conf_json_trandroid);
elseif ( $_GET['file'] == 'filezilla' )
    $serveur->FileDownload('filezilla.xml', $conf_xml_filezilla);
else
    echo 'Le fichier demand&eacute; n\'existe pas.';