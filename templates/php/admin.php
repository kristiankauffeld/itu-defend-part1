<?php
$bannerAdsPath = './ads.dat';
require './ads.inc.php';
///////////////////////////////////////
// Don't Edit Anything Below This Line!
///////////////////////////////////////
if (!isset($_REQUEST['action'])) {
    $_REQUEST['action'] = '';
}
if ($_REQUEST['action'] == 'auth') {
    auth();
}
if ((@$_COOKIE['user'] != $bannerAds['user']) || (@$_COOKIE['pass'] != $bannerAds['pass'])) {
    login();
    exit;
}

switch ($_REQUEST['action']) {
    case 'config':
        config();
        break;
    case 'list':
        view();
        break;
    case 'edit':
        edit();
        break;
    case 'add':
        add();
        break;
    case 'codegen':
        codegen();
        break;
    case 'fileupload':
	fileupload();
	break;
    case 'logout':
        logout();
        break;
    default:
        menu();
        break;
}
function dateselect($name, $date)
{
    global $bannerAdsTime;
    $month = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    if($date == '99999999') {
        $date = $bannerAdsTime;
    }
    $m = date('n', $date);
    $d = date('j', $date);
    $y = date('Y', $date);
    $output = '<select name="' .$name. '_month">';
    for ($i = 0; $i < 12; $i++) {
        $j = $i + 1;
        $s = '';
        if ($j == $m) {
            $s = 'selected="selected"';
        }
        $output .= "<option $s value=\"$j\">$month[$i]</option>";
    }
    $output .= '</select> <select name="' .$name. '_day">';
    for ($i = 1; $i < 32; $i++) {
        $s = '';
        if ($i == $d) {
            $s = 'selected="selected"';
        }
        $output .= "<option $s value=\"$i\">$i</option>";
    }
    $output .= '</select> <select name="' .$name. '_year">';
    for ($i = 2001; $i < 2021; $i++) {
        $s = '';
        if ($i == $y) {
            $s = 'selected="selected"';
        }
        $output .= "<option $s value=\"$i\">$i</option>";
    }
    $output .= '</select>';
    return $output;
}
function adform($data, $mode) {
        if ($data[ PHPADS_ADELEMENT_ENABLED ] == 1) {
            $isen = 'checked="checked"';
        } else {
            $isen = '';
        }
        $expires = dateselect('ad_expires', $data[ PHPADS_ADELEMENT_ENDDATE ]);
        if ($data[ PHPADS_ADELEMENT_ENDDATE ] == '99999999') {
            $noexpires = 'checked="checked"';
        } else {
            $noexpires = '';
        }
        $starts = dateselect('ad_starts', $data[ PHPADS_ADELEMENT_STARTDATE ]);

        echo '<script src="ckeditor/ckeditor.js"></script>';
        echo 'You can edit any of the following properties for Ad ' .$data[PHPADS_ADELEMENT_ID]. ':';
	echo '<form method="post" action="admin.php">';
	echo '<input type="hidden" name="action" value="'.$mode.'" />';
	echo '<table width="550" border="1" cellspacing="0" cellpadding="1">';
	if ($mode=='edit') {
	    echo '<input type="hidden" name="id" value="' .$data[PHPADS_ADELEMENT_ID]. '" />';
	} elseif ($mode=='add') {
	    echo '<tr><td><b>Custom Ad ID:</b><br /><span class="smalltext">Letters and numbers only<br />Leave blank if no custom ID</span></td><td><input type="text" name="ad_custom_id" size="30" /></td></tr>';
	}
	echo '<tr><td><b>Ad Name:</b></td><td><input type="text" name="ad_name" value="' .$data[ PHPADS_ADELEMENT_NAME ]. '" size="30" /></td></tr>';
	echo '<tr><td><b>Is Enabled?</b></td><td><input type="checkbox" ' .$isen. ' name="ad_en" value="1" /> Ad is Enabled</td></tr>';
        echo '<tr><th>Ad Type:</th><td><select name="ad_type" id="ad_type"><option value="'.PHPADS_ADTYPE_IMAGE.'"'.($data[PHPADS_ADELEMENT_ADTYPE]==PHPADS_ADTYPE_IMAGE?' selected="selected"':'').'>Image</option><option value="'.PHPADS_ADTYPE_OTHER.'"'.($data[PHPADS_ADELEMENT_ADTYPE]==PHPADS_ADTYPE_OTHER?' selected="selected"':'').'>Other</option></select></td><tr>';
        echo '<tr class="otherrow"><th>Other ad format:</th><td><textarea class="ckeditor" name="otherinfo" wrap="virtual" cols="50" rows="10">' .$data[ PHPADS_ADELEMENT_OTHERCONTENT ]. '</textarea></td></tr>';
        echo '<tr class="imagerow"><td><b>Link URL:</b></td><td><input type="text" name="ad_link" value="' .$data[ PHPADS_ADELEMENT_LINK_URI ]. '" size="30" /></td></tr>';
        echo '<tr class="imagerow"><td><b>Image URL:</b></td><td><input type="text" name="ad_image" value="' .$data[ PHPADS_ADELEMENT_IMAGE_URI ]. '" size="30" /></td></tr>';
        echo '<tr><td><b>Image Width:</b></td><td><input type="text" name="ad_width" value="' .$data[ PHPADS_ADELEMENT_WIDTH ]. '" size="4" /></td></tr>';
        echo '<tr><td><b>Image Height:</b></td><td><input type="text" name="ad_height" value="' .$data[ PHPADS_ADELEMENT_HEIGHT ]. '" size="4" /></td></tr>';
	echo '<tr><td><b>Weight:</b></td><td><input type="text" name="ad_weight" value="' .$data[ PHPADS_ADELEMENT_WEIGHTING ]. '" size="4" /></td></tr>';
	if ($mode=='edit') {
	    echo '<tr><td><b>Impressions:</b> ' .$data[ PHPADS_ADELEMENT_IMPRESSIONS ]. '&nbsp;<b>C/T:</b> ' .$data[ PHPADS_ADELEMENT_CLICKTHRUS ]. '</td><td><input type="checkbox" name="ad_reset" value="1" /> Reset to Zero</td></tr>';
	}
	echo '<tr><td><b>Impressions Remaining:</b><br /><span class="smalltext">Set to <b>-1</b> for unlimited</span></td><td><input type="text" name="ad_remain" value="' .$data[ PHPADS_ADELEMENT_REMAINING ]. '" size="4" /></td></tr>';
        echo '<tr><th>Starts</th><td>'.$starts.'</td></tr>';
        echo '<tr><td><b>Expires:</b></td><td>' .$expires. ' <input type="checkbox" name="ad_noexpires" ' .$noexpires. ' value="1" /> Never Expires</td></tr>';
        echo '</table>';
        echo '<br /><div align="center"><input type="submit" name="save" value="Save" /> <input type="submit" name="cancel" value="Cancel" />';
	if ($mode=='edit') {
	    echo '<br /><br /><input type="checkbox" name="confirm_delete" value="1" /> Check to Confirm Delete<br /><input type="submit" name="delete" value="Delete This Ad" /><br /><br /><br /><span class="smalltext">Ad Preview:</span><br />';
	    if ($data[PHPADS_ADELEMENT_ADTYPE]==PHPADS_ADTYPE_OTHER) {
		echo '<div width="' .$data[ PHPADS_ADELEMENT_WIDTH ]. '" height="' .$data[ PHPADS_ADELEMENT_HEIGHT ]. '">' .$data[ PHPADS_ADELEMENT_OTHERCONTENT ]. '</div>';
	    } elseif ($data[PHPADS_ADELEMENT_ADTYPE]==PHPADS_ADTYPE_IMAGE) {
		echo '<a href="' .$data[ PHPADS_ADELEMENT_LINK_URI ]. '" target="_blank"><img src="' .$data[ PHPADS_ADELEMENT_IMAGE_URI ]. '" alt="' .$data[ PHPADS_ADELEMENT_NAME ]. '" width="' .$data[ PHPADS_ADELEMENT_WIDTH ]. '" height="' .$data[ PHPADS_ADELEMENT_HEIGHT ]. '" border="0" /></a>';
	    }
	}
	echo '</div></form>';
        echo '<script>$(".' .($data[PHPADS_ADELEMENT_ADTYPE]==PHPADS_ADTYPE_OTHER?'image':'other'). 'row").hide();$("#ad_type").change(function(){ $("."+($(this).val()=='.PHPADS_ADTYPE_OTHER.'?"image":"other")+"row").hide();$("."+($(this).val()=='.PHPADS_ADTYPE_OTHER.'?"other":"image")+"row").show(); }); CKEDITOR.instances.editor1.setData( "' .str_replace('"', '\\"', $data[ PHPADS_ADELEMENT_OTHERCONTENT ]). '" );</script>';
}
function head($title)
{
    echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" /><title>PHPads - admin - ' .$title. '</title><link href="jquery-ui-1.11.4.custom/jquery-ui.css" rel="stylesheet"><style type="text/css">body, td{font-family:arial;font-size:10px;color:#000000;background-color:#D8E7D3;}b{font-weight:bold;}h1{font-size:12px;}.smalltext{font-size:10px;}.error{color:#ff0000;}</style><script src="jquery-ui-1.11.4.custom/external/jquery/jquery.js"></script><script src="jquery-ui-1.11.4.custom/jquery-ui.js"></script></head><body><div align="center"><img src="phpads-main.png" alt="PHPads">
<br><b>' .$title. '</b></div><br /><br /><table align="center" width="550" border="0" cellspacing="2" cellpadding="2"><tr><td align="left">';
}
function foot()
{
    echo '</td></tr></table><br /><br /><div align="center"><hr width="550"><span class="smalltext"><a href="admin.php?action=config">Configuration</a> | <a href="admin.php?action=list">List Ads</a> | <a href="admin.php?action=add">Add Ad</a> | <a href="admin.php?action=fileupload">File upload</a> | <a href="admin.php?action=codegen">Code Generator</a> | <a href="admin.php?action=logout">Logout</a><p><a href="http://blondish.net/">PHPads</a></span></div>';
    echo '<script>$("input[type=submit]").button();</script>';
    echo '</body></html>';
}
function auth()
{
    global $bannerAds;
    if (($_POST['user'] != $bannerAds['user']) || (md5($_POST['pass']) != $bannerAds['pass'])) {
        login('Invalid Login or Password');
        exit;
    }
    setcookie('user', $_POST['user']);
    setcookie('pass', md5($_POST['pass']));
    menu();
}
function login($msg = '')
{
    head('Authorization Required');
    if ($msg != '') {
        echo "<span class=\"error\"><b>$msg</b></span><p>";
    }
    echo 'You must login to access the control panel. Please provide your login name and password below. NOTE: Cookies must be enabled to use this control panel.<form method="post" action="admin.php"><input type="hidden" name="action" value="auth" /><b>Login Name:</b> <input type="text" name="user" /><br /><b>Password:</b> <input type="password" name="pass" /><br /><input type="submit" value="Login" /></form>';
    foot();
}
function menu()
{
    head('Admin Menu');
    echo '<br><br><center><a href="admin.php?action=config">Setup</a><br><a href="admin.php?action=list">Edit Ads/ Ad Stats</a><br><a href="admin.php?action=add">Add Ad</a><br><a href="admin.php?action=fileupload">Upload file</a><br /><a href="admin.php?action=codegen">Code Generator</a><br><a href="admin.php?action=logout">Logout</a></center>';
    foot();
    exit;
}
function config()
{
    global $bannerAds;
    if (isset($_POST['save'])) {
        $bannerAds['user'] = trim($_POST['newlogin']);
        if ($_POST['newpass'] != '**********') {
            $bannerAds['pass'] = md5(trim($_POST['newpass']));
        }
        $bannerAds['click_url'] 	= trim($_POST['click_url']);
        $bannerAds['js_url'] 		= trim($_POST['js_url']);
        $bannerAds['target'] 		= trim($_POST['target']);
        $bannerAds['border'] 		= (int)$_POST['border'];
	$bannerAds['default_display'] 	= trim($_POST['default_display']);
	$bannerAds['timezone'] 		= trim($_POST['timezone']);
	$bannerAds['timeformat'] 	= trim($_POST['timeformat']);
	$bannerAds['blockip'] 		= trim($_POST['blockip']);
        writeads();
        menu();
    } else if (isset($_POST['cancel'])) {
        menu();
    } else {
        head('Setup');
        echo 'Edit or Check stats. <hr width="550" /><form method="post" action="admin.php"><input type="hidden" name="action" value="config" /><li><b>Login and Password</b></li><br /><br />To change your login name and password, fill them in below; <br><br>Login Name: <input type="text" name="newlogin" value="' .$bannerAds['user']. '" /><br>Password: <input type="password" name="newpass" value="**********" /><hr width="550" /><li><b>Click URL</b></li><br /><br>This is the URL to the click.php script. <br><br>Click URL: <input type="text" name="click_url" value="' .$bannerAds['click_url']. '" /><hr width="550" /><li><b>JavaScript URL</b></li><br /><br>This is the URL to the js.php script. <br><br>JavaScript URL: <input type="text" name="js_url" value="' .$bannerAds['js_url']. '" /><hr width="550" /><li><b>Target</b></li><br><br>This lets you specify the target attribute of all links. To open ads in a new window set the target value to &quot;_blank&quot; (without the quotes). If you do not want or need a target, leave this field empty.<br><br>Target: <input type="text" name="target" value="' .$bannerAds['target']. '" /><hr width="550" /><li><b>Border</b></li><p>This sets the amount of border, in pixels, around an ad. Set this to 0 for no border.<br><br>Border: <select name="border">';
        for ($i = 0; $i <= 5; $i++) {
            if ($bannerAds['border'] == $i) {
                $s = ' selected="selected"';
            } else {
                $s = '';
            }
            echo "<option value=\"$i\"$s>$i</option>";
        }
        echo '</select>';
//	echo '<br /><br /><input type="submit" name="save" value="Save" /> <input type="submit" name="cancel" value="Cancel" />';
	echo '<hr width="550" /><li><b>Default content to display</b></li><br /><br>This is what is displayed if no Ads in the system match the requested settings (can be used to display adverts from Google Ads or another Ad partner if all options are expired). <br><br>Default content: <textarea name="default_display" wrap="virtual" cols="65" rows="6">' .$bannerAds['default_display']. '</textarea>';
	echo '<hr width="550" /><li><b>Timezone for start/end dates</b></li><br /><br>This controls which timezone the ad server is running in, to control exactly when adverts should start/stop being displayed.</br><br>Timezone: <select name="timezone">';
	$timezones=timezone_identifiers_list();
	for ($i = 0;  $i < count($timezones); $i++) {
	    echo '<option value="' .$timezones[$i]. '"' .($bannerAds['timezone']==$timezones[$i]?' selected="selected"':''). '>' .$timezones[$i]. '</option>';
	}
	echo '</select><hr width="550" /><li><b>Time format</b></li><br><br>This controls only the display of times inputed - it has no functional effect beyond readability.<br><br>Time format: <input type="text" name="timeformat" value="' .$bannerAds['timeformat']. '" />';
	echo '<hr width="550" /><li><b>Blocked IP Address:</b></li><br><br>If you want to exclude a particular IP (your office maybe) from counting towards the impressions and click through counts, enter it here (or leave blank). <br><br>IP Address: <input type="text" name="blockip" value="' .$bannerAds['blockip']. '" />';
	echo '<hr width="550" /><br /><br /><input type="submit" name="save" value="Save" /> <input type="submit" name="cancel" value="Cancel" />';
        foot();
    }
}
function view()
{
    global $ads,$bannerAds;
    head('Ads');
    echo '<center><table border="1" bordercolor="#000000" cellspacing="0" cellpadding="1"><tr><th>Type</th><td nowrap="nowrap"><span class="smalltext"><b>Name (ID):</b></span></td><td><span class="smalltext"><b>Link URL:</b></span></td><td><span class="smalltext"><b>Image URL:</b></span></td><td><span class="smalltext"><b>Active:</b></span></td><td><span class="smalltext"><b>Weight:</b></span></td><td><span class="smalltext"><b>Starts:</b></span></td><td><span class="smalltext"><b>Ends:</b></span></td><td><span class="smalltext"><b>Left:</b></span></td><td><span class="smalltext"><b>Impressions:</b></span></td><td><span class="smalltext"><b>C/T:</b></span></td></tr>';
    foreach ($ads as $ad) {
        $data = explode('||', $ad);
        $enabled = $data[ PHPADS_ADELEMENT_ENABLED ] ? 'Yes' : '<span class="error">No</span>';
        if($data[ PHPADS_ADELEMENT_ENDDATE ] == '99999999') {
            $expires = 'Never';
        } else {
            $expires = date($bannerAds['timeformat'], $data[ PHPADS_ADELEMENT_ENDDATE ]);
        }
	if(!$data[ PHPADS_ADELEMENT_STARTDATE ]) {
            $starts = 'Always';
	} else {
	    $starts = date($bannerAds['timeformat'], $data[ PHPADS_ADELEMENT_STARTDATE ]);
	}
        if ($data[ PHPADS_ADELEMENT_REMAINING ] == -1) {
            $remaining = 'Unlimited';
        } else {
            $remaining = $data[ PHPADS_ADELEMENT_REMAINING ];
        }
        if (strlen($data[ PHPADS_ADELEMENT_LINK_URI ]) > 25) {
            $linkUrl = substr($data[ PHPADS_ADELEMENT_LINK_URI ], 0, 15). '...' .substr($data[ PHPADS_ADELEMENT_LINK_URI ], -7);
        } else {
            $linkUrl = $data[ PHPADS_ADELEMENT_LINK_URI ];
        }
        if (strlen($data[ PHPADS_ADELEMENT_IMAGE_URI ]) > 25) {
            $imageUrl = substr($data[ PHPADS_ADELEMENT_IMAGE_URI ], 0, 15). '...' .substr($data[ PHPADS_ADELEMENT_IMAGE_URI ], -7);
        } else {
            $imageUrl = $data[ PHPADS_ADELEMENT_IMAGE_URI ];
        }
        echo "<tr><td>" .($data[ PHPADS_ADELEMENT_ADTYPE ]==0?'Image':'Other'). "</td><td nowrap=\"nowrap\"><span class=\"smalltext\"><a href=\"admin.php?action=edit&id=".urlencode($data[ PHPADS_ADELEMENT_ID ])."\" title=\"Edit Ad\">" .$data[ PHPADS_ADELEMENT_NAME ]. " (".$data[ PHPADS_ADELEMENT_ID ].")</a></span></td><td nowrap=\"nowrap\"><span class=\"smalltext\"><a href=\"" .$data[ PHPADS_ADELEMENT_LINK_URI ]. "\">$linkUrl</a></span></td><td nowrap=\"nowrap\"><span class=\"smalltext\"><a href=\"" .$data[ PHPADS_ADELEMENT_IMAGE_URI ]. "\">$imageUrl</a></span></td><td><span class=\"smalltext\">$enabled</span></td><td><span class=\"smalltext\">" .$data[ PHPADS_ADELEMENT_WEIGHTING ]. "</span></td><td><span class=\"smalltext\">$starts</span></td><td><span class=\"smalltext\">$expires</span></td><td><span class=\"smalltext\">$remaining</span></td><td><span class=\"smalltext\">" .$data[ PHPADS_ADELEMENT_IMPRESSIONS ]. "</span></td><td><span class=\"smalltext\">" .$data[ PHPADS_ADELEMENT_CLICKTHRUS ]. "</span></td></tr>";
    }
    echo '</table></center>';
    foot();
}
function edit()
{
    global $ads;
    if (!isset($_REQUEST['id'])) {
        die('No Ad ID was Specified');
    }
    if (isset($_POST['save'])) {
        for ($i = 0; $i < count($ads); $i++) {
            if(ereg('^' .$_POST['id']. '\|\|', $ads[$i])) {
                $data = explode('||', $ads[$i]);
                if (isset($_POST['ad_en']) && $_POST['ad_en'] == 1) {
                    $data[ PHPADS_ADELEMENT_ENABLED ] = 1;
                } else {
                    $data[ PHPADS_ADELEMENT_ENABLED ] = 0;
                }
                if (isset($_POST['ad_reset']) && $_POST['ad_reset'] == 1) {
                    $data[ PHPADS_ADELEMENT_IMPRESSIONS ] = 0;
                    $data[ PHPADS_ADELEMENT_CLICKTHRUS ] = 0;
                }
                if (isset($_POST['ad_noexpires']) && $_POST['ad_noexpires'] == 1) {
                    $data[ PHPADS_ADELEMENT_ENDDATE ] = '99999999';
                } else {
                    $data[ PHPADS_ADELEMENT_ENDDATE ] = mktime(0, 0, 0, $_POST['ad_expires_month'], $_POST['ad_expires_day'], $_POST['ad_expires_year']);
                }
                $data[ PHPADS_ADELEMENT_WEIGHTING ] = $_POST['ad_weight'];
                $data[ PHPADS_ADELEMENT_REMAINING ] = $_POST['ad_remain'];
                $data[ PHPADS_ADELEMENT_WIDTH ] = $_POST['ad_width'];
                $data[ PHPADS_ADELEMENT_HEIGHT ] = $_POST['ad_height'];
                $data[ PHPADS_ADELEMENT_LINK_URI ] = $_POST['ad_link'];
                $data[ PHPADS_ADELEMENT_IMAGE_URI ] = $_POST['ad_image'];
                $data[ PHPADS_ADELEMENT_NAME ] = $_POST['ad_name'];
		$data[ PHPADS_ADELEMENT_STARTDATE ] = mktime(0, 0, 0, (int)$_POST['ad_starts_month'], (int)$_POST['ad_starts_day'], (int)$_POST['ad_starts_year']); 
		$data[ PHPADS_ADELEMENT_ADTYPE ] = (int)$_POST['ad_type'];

		$data[PHPADS_ADELEMENT_OTHERCONTENT] = str_replace("\n","", $_POST['otherinfo']);
		$ads[$i] = join('||', $data);

                break;
            }
        }
        writeads();
        view();
    } else if (isset($_POST['delete'])) {
        if (!isset($_POST['confirm_delete']) || $_POST['confirm_delete'] != 1) {
            die('You did not confirm the delete. <a href="javascript:window.history.go(-1);">[Back]</a>');
        }
	$nads = array();
        foreach ($ads as $ad) {
            if(!ereg('^' .$_POST['id']. '\|\|', $ad)) {
                $nads[] = $ad;
            }
        }
        $ads = $nads;
        writeads();
        menu();
    } else if (isset($_POST['cancel'])) {
        menu();
    } else {
        foreach ($ads as $ad) {
            if(ereg('^' .$_GET['id']. '\|\|', $ad)) {
                $data = explode('||', $ad);
                break;
            }
        }
        if (!isset($data)) {
            die('Ad ID ' .$_GET['id']. ' was not found');
        }

        head('Edit Ad');

	adform($data, 'edit');

        foot();
    }
}
function add()
{
    global $bannerAds, $ads;
    if (isset($_POST['save'])) {
        $data = array(11);
        if ($_POST['ad_custom_id'] != '') {
            $data[ PHPADS_ADELEMENT_ID ] = $_POST['ad_custom_id'];
        } else {
            $data[ PHPADS_ADELEMENT_ID ] = $bannerAds['next_autoindex'];
            $bannerAds['next_autoindex']++;
        }
        if (isset($_POST['ad_en']) && $_POST['ad_en'] == 1) {
            $data[ PHPADS_ADELEMENT_ENABLED ] = 1;
        } else {
            $data[ PHPADS_ADELEMENT_ENABLED ] = 0;
        }
        $data[ PHPADS_ADELEMENT_WEIGHTING ] = $_POST['ad_weight'];
        if (isset($_POST['ad_noexpires']) && $_POST['ad_noexpires'] == 1) {
            $data[ PHPADS_ADELEMENT_ENDDATE ] = '99999999';
        } else {
            $data[ PHPADS_ADELEMENT_ENDDATE ] = mktime(0, 0, 0, $_POST['ad_expires_month'], $_POST['ad_expires_day'], $_POST['ad_expires_year']);
        }
        $data[ PHPADS_ADELEMENT_REMAINING ] = (int)$_POST['ad_remain'];
        $data[ PHPADS_ADELEMENT_IMPRESSIONS ] = 0;
        $data[ PHPADS_ADELEMENT_CLICKTHRUS ] = 0;
        $data[ PHPADS_ADELEMENT_WIDTH ] = (int)$_POST['ad_width'];
        $data[ PHPADS_ADELEMENT_HEIGHT ] = (int)$_POST['ad_height'];
        $data[ PHPADS_ADELEMENT_LINK_URI ] = $_POST['ad_link'];
        $data[ PHPADS_ADELEMENT_IMAGE_URI ] = $_POST['ad_image'];
        $data[ PHPADS_ADELEMENT_NAME ] = stripslashes($_POST['ad_name']);
	$data[ PHPADS_ADELEMENT_STARTDATE ] = mktime(0, 0, 0, (int)$_POST['ad_starts_month'], (int)$_POST['ad_starts_day'], (int)$_POST['ad_starts_year']);
        $data[ PHPADS_ADELEMENT_ADTYPE ] = (int)$_POST['ad_type'];
        if ($data[PHPADS_ADELEMENT_ADTYPE]==PHPADS_ADTYPE_OTHER) {
            $data2 = fopen('uploads/'.$data[PHPADS_ADELEMENT_ID]."_".$data[ PHPADS_ADELEMENT_NAME ].'.inc.txt', 'w');
            flock($data2, 2);
            fputs($data2, $_POST['otherinfo']);
            flock($data2, 3);
            fclose($data2);
        }

        $ads[] = join('||', $data);
        writeads();
        menu();
    } else if (isset($_POST['cancel'])) {
        menu();
    } else {

	$data = array( null, null, null, null, null, null, null, null, null, null, null, null, null, null );
        $data[ PHPADS_ADELEMENT_ENDDATE ] = 99999999;
	$data[ PHPADS_ADELEMENT_STARTDATE ] = time();
        $data[ PHPADS_ADELEMENT_WIDTH ] = 468;
        $data[ PHPADS_ADELEMENT_HEIGHT ] = 60;
        $data[ PHPADS_ADELEMENT_LINK_URI ] = 'http://';
        $data[ PHPADS_ADELEMENT_IMAGE_URI ] = 'http://';
	$data[ PHPADS_ADELEMENT_REMAINING ] = -1;
	$data[ PHPADS_ADELEMENT_ENABLED ] = 1;
	$data[ PHPADS_ADELEMENT_NAME ] = "New Ad";
	$data[ PHPADS_ADELEMENT_WEIGHTING ] = 1;
	$data[ PHPADS_ADELEMENT_ADTYPE ] = PHPADS_ADTYPE_IMAGE;

        head('Add Ad');
	adform($data, 'add');
        foot();
    }
}
function codegen()
{
global $bannerAds;
    if (isset($_POST['invocation_type']) && $_POST['invocation_type'] == 'php') {
        if (isset($_POST['nextstep']) && $_POST['nextstep'] == 2) {
            if (isset($_POST['numtypes'])) {
                $numtypes = $_POST['numtypes'];
            } else {
                $numtypes = 1;
            }
            head('Code Generator: Step 2');
            echo "<form method=\"post\" action=\"admin.php\"><input type=\"hidden\" name=\"action\" value=\"codegen\" /><input type=\"hidden\" name=\"invocation_type\" value=\"php\" /><input type=\"hidden\" name=\"nextstep\" value=\"3\" /><input type=\"hidden\" name=\"numtypes\" value=\"$numtypes\">Absolute Path to ads.php: <input type=\"text\" name=\"path\" value=\"" .dirname(__FILE__). "/ads.php\" size=\"35\" /><br /><br /><table>";
            for ($i = 1; $i <= $numtypes; $i++) {
                echo "<tr><td><b>Ad Type $i</b></td></tr><tr><td>Specific Ad ID (if applicable): <input type=\"text\" name=\"adID_$i\" size=\"4\" /> skip next row if not blank</td></tr><tr><td># of Ads: <input type=\"text\" name=\"numads_$i\" size=\"4\" /> Width (if applicable): <input type=\"text\" name=\"width_$i\" size=\"4\" /> Height (width required): <input type=\"text\" name=\"height_$i\" size=\"4\" /></td></tr>";
            }
            echo '</table><div align="center"><input type="submit" value="Generate Code" /> <input type="reset" value="Reset" /></form>';
            foot();
            exit;
        } else if (isset($_POST['nextstep']) && $_POST['nextstep'] == 3) {
            head('Code Generator: Step 3');
            echo 'Copy and Paste the code below to a PHP file where you\'d like the ads displayed:<br /><br /><font face="Verdana, Helvetica" size="-1"><pre>';
            echo "&lt;?php include '" .$_POST['path']. "'; ?&gt;\n";
            for ($i = 1; $i <= $_POST['numtypes']; $i++) {
                if ($_POST["adID_$i"] != '') {
                    echo "\n/* Code to display ad ID " .$_POST["adID_$i"]. " */\n";
                    echo "&lt;?php \$buttons$i = new bannerAds ('" .$_POST["adID_$i"]. "'); ?&gt;\n";
                    echo "&lt;?php echo \$buttons" .$i. "->ad[ PHPADS_ADELEMENT_ID ]; ?&gt;\n";
                } else {
                    echo "\n/* Code to display " .$_POST["numads_$i"]. " ad(s)";
                    if (($_POST["width_$i"] != '') && ($_POST["height_$i"] != '')) {
                        echo " with width of " .$_POST["width_$i"]. " and height of " .$_POST["height_$i"];
                    }
                    echo " */\n&lt;?php \$buttons$i = new bannerAds (null, " .$_POST["numads_$i"];
                    if (($_POST["width_$i"] != '') && ($_POST["height_$i"] != '')) {
                        echo ", " .$_POST["width_$i"]. ", " .$_POST["height_$i"];
                    }
                    echo "); ?&gt;\n";
                    for ($j = 0; $j < $_POST["numads_$i"]; $j++) {
                        echo "&lt;?php echo \$buttons" .$i. "->ad[$j]; ?&gt;\n";
                    }
                }
            }
        }
        echo '</pre></font>';
        foot();
        exit;
    } else if (isset($_POST['invocation_type']) && $_POST['invocation_type'] == 'js') {
        head('Code Generator: Step 2');
        echo 'Copy and Paste the code below to a HTML file where you\'d like the ad displayed:<br /><br /><font face="Verdana, Helvetica" size="-1"><pre>';
        if (isset($_POST['ad_id']) && $_POST['ad_id'] != '') {
            echo '&lt;script language=&quot;javascript&quot; type=&quot;text/javascript&quot; src=&quot;' .$bannerAds['js_url']. '?id=' .$_POST['ad_id']. '&quot;&gt;&lt;/script&gt;';
        } else if (isset($_POST['ad_width']) && isset($_POST['ad_height']) && $_POST['ad_width'] != '' && $_POST['ad_height'] != '') {
            echo '&lt;script language=&quot;javascript&quot; type=&quot;text/javascript&quot; src=&quot;' .$bannerAds['js_url']. '?width=' .$_POST['ad_width']. '&height=' .$_POST['ad_height']. '&quot;&gt;&lt;/script&gt;';
        } else {
            echo '&lt;script language=&quot;javascript&quot; type=&quot;text/javascript&quot; src=&quot;' .$bannerAds['js_url']. '&quot;&gt;&lt;/script&gt;';
        }
        echo '</pre></font>';
        foot();
        exit;
    } else {
        head('Code Generator: Step 1');
        echo 'The code generator will create the necessary PHP or JavaScript code for you to copy and paste wherever you\'d like ads displayed. You are able to display multiple ads on the same page, even different types of ads (a type of ad can be a group of ads of different width and height, just 1 unique ad, or all ads in the database) when PHP invocation is used to display ads. Should you use JavaScript invocation to display ads, only one ad can be displayed per page. Therefore, it is to your advantage to use PHP to display ads.<hr width="550" /><li><b>PHP Invocation</b></li><form method="post" action="admin.php"><input type="hidden" name="action" value="codegen" /><input type="hidden" name="invocation_type" value="php" /><input type="hidden" name="nextstep" value="2" />How many different types of ads would you like to display? <input type="text" name="numtypes" value="1" size="4" maxlength="2" /> <input type="submit" value="Go!" /></form><hr width="550" /><li><b>JavaScript Invocation</b></li><form method="post" action="admin.php"><input type="hidden" name="action" value="codegen" /><input type="hidden" name="invocation_type" value="js" />Display ad ID <input type="text" name="ad_id" size="4" /> OR<br />Display one ad with width of <input type="text" name="ad_width" size="4" /> and height of <input type="text" name="ad_height" size="4" /> OR<br />Display all ads in the database by leaving the above fields blank. <input type="submit" value="Go!" /></form>';
        foot();
    }
}
function fileupload()
{
    if (isset($_POST['submit'])) {
	head('File upload...');
	$target_dir = "uploads/";
	$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

	// Allow image uploads
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check !== false) {
	    $uploadOk = 1;
	} else {
	    echo "File is not an image.<br />";
	    $uploadOk = 0;
	}
	// Check if file already exists
	if (file_exists($target_file)) {
	    echo "Sorry, a file already exists with that name.<br />";
	    $uploadOk = 0;
	}
	// Allow certain file formats
	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
	    echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.<br />";
	    $uploadOk = 0;
	}
	if (!$uploadOk) {
	    echo "Your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        echo "<strong>The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.</strong><br /><br />The URL for this image is:<br />http://".$_SERVER['SERVER_NAME']."/uploads/". basename( $_FILES["fileToUpload"]["name"]);
	    } else {
	        echo "Sorry, there was an error uploading your file.";
	    }
	}
	foot();
    } else {
        head('File upload');
        echo '<form method="post" enctype="multipart/form-data">Select image to upload:<input type="file" name="fileToUpload" id="fileToUpload"><br /><input type="submit" value="Upload Image" name="submit"></form>';
        foot();
    }
}

function logout()
{
    setcookie('user', '');
    setcookie('pass', '');
    head('Logged Out');
    echo 'You are now logged out of the control panel. Click <a href="admin.php">here</a> to login again.';
    foot();
}
?>
