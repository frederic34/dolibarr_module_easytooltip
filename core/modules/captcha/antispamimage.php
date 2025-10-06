<?php
/*
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 * or see http://www.gnu.org/
 */

/**
 *		\file       htdocs/core/antispamimage.php
 *		\brief      Return antispam image
 */
define('NOLOGIN', 1);

if (!defined('NOREQUIREUSER')) {
	define('NOREQUIREUSER', 1);
}
if (!defined('NOREQUIREDB')) {
	define('NOREQUIREDB', 1);
}
if (!defined('NOREQUIRETRAN')) {
	define('NOREQUIRETRAN', 1);
}
if (!defined('NOREQUIREMENU')) {
	define('NOREQUIREMENU', 1);
}
if (!defined('NOREQUIRESOC')) {
	define('NOREQUIRESOC', 1);
}
if (!defined('NOTOKENRENEWAL')) {
	define('NOTOKENRENEWAL', 1);
}

require_once '../../../config.php';
/*
 * View
 */
header("Content-type: image/png");
session_start();

$val = '';
$val2 = '';

$alphabet = 'aAbBCDeEFgGhHJKLmMnNpPqQRsStTuVwWXYZz2345679';

mt_srand((float) microtime() * 1000000);

// Police et taille des caractères du code
// ---------------------------------------
$font_ttf = dol_buildpath('/easytooltip/img/LinuxLibertine.ttf', 0);
// Initialisation de l'image du captcha
// ------------------------------------
$image_captcha = imagecreatetruecolor(80, 32);
if (empty($image_captcha)) {
	dol_print_error('', "Problem with GD creation");
	exit;
}
// Définition des couleurs du captcha en RVB
// -----------------------------------------
$color_bg = imagecolorallocate($image_captcha, 255, 255, 255);
$color_font[0] = imagecolorallocate($image_captcha, 0, 0, 0);  // black
$color_font[1] = imagecolorallocate($image_captcha, 255, 0, 0); // red
$color_font[2] = imagecolorallocate($image_captcha, 0, 0, 255);  // blue
$color_font[3] = imagecolorallocate($image_captcha, 128, 128, 255); // violet
$color_font[4] = imagecolorallocate($image_captcha, 0, 128, 0); // green
$color_font[5] = imagecolorallocate($image_captcha, 128, 128, 128); // grey
$color_font[6] = imagecolorallocate($image_captcha, 255, 0, 255); // magenta
$color_font[7] = imagecolorallocate($image_captcha, 255, 128, 0); //orange
// Remplissage avec la couleur de fond
// -----------------------------------
imagefill($image_captcha, 0, 0, $color_bg);

// Creation du code et écriture au fur et à mesure dans le captcha
// ---------------------------------------------------------------
$string = '';
for ($i = 0; $i < 5; $i++) {
	$temp = $alphabet[mt_rand(0, strlen($alphabet) - 1)];
	$inclinaison = mt_rand(0, 30) * mt_rand(-1, 1);
	imagettftext($image_captcha, 14, $inclinaison, 6 + ($i * 14), 22, $color_font[mt_rand(0, 7)], $font_ttf, $temp);
	$string .= $temp;
}

// Création de l'image et envoi au navigateur
// ------------------------------------------
imagepng($image_captcha);

// Suppression des ressources de l'image
// -------------------------------------
imagedestroy($image_captcha);

$_SESSION['dol_antispam_value'] = $string;
