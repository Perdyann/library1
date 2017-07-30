<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "zuriel@zurielann.org" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "5ccf9b" );

?>
<?php
/**
 * GNU Library or Lesser General Public License version 2.0 (LGPLv2)
*/

# main
# ------------------------------------------------------
error_reporting( E_ERROR ) ;
phpfmg_admin_main();
# ------------------------------------------------------




function phpfmg_admin_main(){
    $mod  = isset($_REQUEST['mod'])  ? $_REQUEST['mod']  : '';
    $func = isset($_REQUEST['func']) ? $_REQUEST['func'] : '';
    $function = "phpfmg_{$mod}_{$func}";
    if( !function_exists($function) ){
        phpfmg_admin_default();
        exit;
    };

    // no login required modules
    $public_modules   = false !== strpos('|captcha|', "|{$mod}|", "|ajax|");
    $public_functions = false !== strpos('|phpfmg_ajax_submit||phpfmg_mail_request_password||phpfmg_filman_download||phpfmg_image_processing||phpfmg_dd_lookup|', "|{$function}|") ;   
    if( $public_modules || $public_functions ) { 
        $function();
        exit;
    };
    
    return phpfmg_user_isLogin() ? $function() : phpfmg_admin_default();
}

function phpfmg_ajax_submit(){
    $phpfmg_send = phpfmg_sendmail( $GLOBALS['form_mail'] );
    $isHideForm  = isset($phpfmg_send['isHideForm']) ? $phpfmg_send['isHideForm'] : false;

    $response = array(
        'ok' => $isHideForm,
        'error_fields' => isset($phpfmg_send['error']) ? $phpfmg_send['error']['fields'] : '',
        'OneEntry' => isset($GLOBALS['OneEntry']) ? $GLOBALS['OneEntry'] : '',
    );
    
    @header("Content-Type:text/html; charset=$charset");
    echo "<html><body><script>
    var response = " . json_encode( $response ) . ";
    try{
        parent.fmgHandler.onResponse( response );
    }catch(E){};
    \n\n";
    echo "\n\n</script></body></html>";

}


function phpfmg_admin_default(){
    if( phpfmg_user_login() ){
        phpfmg_admin_panel();
    };
}



function phpfmg_admin_panel()
{    
    phpfmg_admin_header();
    phpfmg_writable_check();
?>    
<table cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td valign=top style="padding-left:280px;">

<style type="text/css">
    .fmg_title{
        font-size: 16px;
        font-weight: bold;
        padding: 10px;
    }
    
    .fmg_sep{
        width:32px;
    }
    
    .fmg_text{
        line-height: 150%;
        vertical-align: top;
        padding-left:28px;
    }

</style>

<script type="text/javascript">
    function deleteAll(n){
        if( confirm("Are you sure you want to delete?" ) ){
            location.href = "admin.php?mod=log&func=delete&file=" + n ;
        };
        return false ;
    }
</script>


<div class="fmg_title">
    1. Email Traffics
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=1">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=1">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_EMAILS_LOGFILE) ){
            echo '<a href="#" onclick="return deleteAll(1);">delete all</a>';
        };
    ?>
</div>


<div class="fmg_title">
    2. Form Data
</div>
<div class="fmg_text">
    <a href="admin.php?mod=log&func=view&file=2">view</a> &nbsp;&nbsp;
    <a href="admin.php?mod=log&func=download&file=2">download</a> &nbsp;&nbsp;
    <?php 
        if( file_exists(PHPFMG_SAVE_FILE) ){
            echo '<a href="#" onclick="return deleteAll(2);">delete all</a>';
        };
    ?>
</div>

<div class="fmg_title">
    3. Form Generator
</div>
<div class="fmg_text">
    <a href="http://www.formmail-maker.com/generator.php" onclick="document.frmFormMail.submit(); return false;" title="<?php echo htmlspecialchars(PHPFMG_SUBJECT);?>">Edit Form</a> &nbsp;&nbsp;
    <a href="http://www.formmail-maker.com/generator.php" >New Form</a>
</div>
    <form name="frmFormMail" action='http://www.formmail-maker.com/generator.php' method='post' enctype='multipart/form-data'>
    <input type="hidden" name="uuid" value="<?php echo PHPFMG_ID; ?>">
    <input type="hidden" name="external_ini" value="<?php echo function_exists('phpfmg_formini') ?  phpfmg_formini() : ""; ?>">
    </form>

		</td>
	</tr>
</table>

<?php
    phpfmg_admin_footer();
}



function phpfmg_admin_header( $title = '' ){
    header( "Content-Type: text/html; charset=" . PHPFMG_CHARSET );
?>
<html>
<head>
    <title><?php echo '' == $title ? '' : $title . ' | ' ; ?>PHP FormMail Admin Panel </title>
    <meta name="keywords" content="PHP FormMail Generator, PHP HTML form, send html email with attachment, PHP web form,  Free Form, Form Builder, Form Creator, phpFormMailGen, Customized Web Forms, phpFormMailGenerator,formmail.php, formmail.pl, formMail Generator, ASP Formmail, ASP form, PHP Form, Generator, phpFormGen, phpFormGenerator, anti-spam, web hosting">
    <meta name="description" content="PHP formMail Generator - A tool to ceate ready-to-use web forms in a flash. Validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. ">
    <meta name="generator" content="PHP Mail Form Generator, phpfmg.sourceforge.net">

    <style type='text/css'>
    body, td, label, div, span{
        font-family : Verdana, Arial, Helvetica, sans-serif;
        font-size : 12px;
    }
    </style>
</head>
<body  marginheight="0" marginwidth="0" leftmargin="0" topmargin="0">

<table cellspacing=0 cellpadding=0 border=0 width="100%">
    <td nowrap align=center style="background-color:#024e7b;padding:10px;font-size:18px;color:#ffffff;font-weight:bold;width:250px;" >
        Form Admin Panel
    </td>
    <td style="padding-left:30px;background-color:#86BC1B;width:100%;font-weight:bold;" >
        &nbsp;
<?php
    if( phpfmg_user_isLogin() ){
        echo '<a href="admin.php" style="color:#ffffff;">Main Menu</a> &nbsp;&nbsp;' ;
        echo '<a href="admin.php?mod=user&func=logout" style="color:#ffffff;">Logout</a>' ;
    }; 
?>
    </td>
</table>

<div style="padding-top:28px;">

<?php
    
}


function phpfmg_admin_footer(){
?>

</div>

<div style="color:#cccccc;text-decoration:none;padding:18px;font-weight:bold;">
	:: <a href="http://phpfmg.sourceforge.net" target="_blank" title="Free Mailform Maker: Create read-to-use Web Forms in a flash. Including validating form with CAPTCHA security image, send html email with attachments, send auto response email copy, log email traffics, save and download form data in Excel. " style="color:#cccccc;font-weight:bold;text-decoration:none;">PHP FormMail Generator</a> ::
</div>

</body>
</html>
<?php
}


function phpfmg_image_processing(){
    $img = new phpfmgImage();
    $img->out_processing_gif();
}


# phpfmg module : captcha
# ------------------------------------------------------
function phpfmg_captcha_get(){
    $img = new phpfmgImage();
    $img->out();
    //$_SESSION[PHPFMG_ID.'fmgCaptchCode'] = $img->text ;
    $_SESSION[ phpfmg_captcha_name() ] = $img->text ;
}



function phpfmg_captcha_generate_images(){
    for( $i = 0; $i < 50; $i ++ ){
        $file = "$i.png";
        $img = new phpfmgImage();
        $img->out($file);
        $data = base64_encode( file_get_contents($file) );
        echo "'{$img->text}' => '{$data}',\n" ;
        unlink( $file );
    };
}


function phpfmg_dd_lookup(){
    $paraOk = ( isset($_REQUEST['n']) && isset($_REQUEST['lookup']) && isset($_REQUEST['field_name']) );
    if( !$paraOk )
        return;
        
    $base64 = phpfmg_dependent_dropdown_data();
    $data = @unserialize( base64_decode($base64) );
    if( !is_array($data) ){
        return ;
    };
    
    
    foreach( $data as $field ){
        if( $field['name'] == $_REQUEST['field_name'] ){
            $nColumn = intval($_REQUEST['n']);
            $lookup  = $_REQUEST['lookup']; // $lookup is an array
            $dd      = new DependantDropdown(); 
            echo $dd->lookupFieldColumn( $field, $nColumn, $lookup );
            return;
        };
    };
    
    return;
}


function phpfmg_filman_download(){
    if( !isset($_REQUEST['filelink']) )
        return ;
        
    $info =  @unserialize(base64_decode($_REQUEST['filelink']));
    if( !isset($info['recordID']) ){
        return ;
    };
    
    $file = PHPFMG_SAVE_ATTACHMENTS_DIR . $info['recordID'] . '-' . $info['filename'];
    phpfmg_util_download( $file, $info['filename'] );
}


class phpfmgDataManager
{
    var $dataFile = '';
    var $columns = '';
    var $records = '';
    
    function phpfmgDataManager(){
        $this->dataFile = PHPFMG_SAVE_FILE; 
    }
    
    function parseFile(){
        $fp = @fopen($this->dataFile, 'rb');
        if( !$fp ) return false;
        
        $i = 0 ;
        $phpExitLine = 1; // first line is php code
        $colsLine = 2 ; // second line is column headers
        $this->columns = array();
        $this->records = array();
        $sep = chr(0x09);
        while( !feof($fp) ) { 
            $line = fgets($fp);
            $line = trim($line);
            if( empty($line) ) continue;
            $line = $this->line2display($line);
            $i ++ ;
            switch( $i ){
                case $phpExitLine:
                    continue;
                    break;
                case $colsLine :
                    $this->columns = explode($sep,$line);
                    break;
                default:
                    $this->records[] = explode( $sep, phpfmg_data2record( $line, false ) );
            };
        }; 
        fclose ($fp);
    }
    
    function displayRecords(){
        $this->parseFile();
        echo "<table border=1 style='width=95%;border-collapse: collapse;border-color:#cccccc;' >";
        echo "<tr><td>&nbsp;</td><td><b>" . join( "</b></td><td>&nbsp;<b>", $this->columns ) . "</b></td></tr>\n";
        $i = 1;
        foreach( $this->records as $r ){
            echo "<tr><td align=right>{$i}&nbsp;</td><td>" . join( "</td><td>&nbsp;", $r ) . "</td></tr>\n";
            $i++;
        };
        echo "</table>\n";
    }
    
    function line2display( $line ){
        $line = str_replace( array('"' . chr(0x09) . '"', '""'),  array(chr(0x09),'"'),  $line );
        $line = substr( $line, 1, -1 ); // chop first " and last "
        return $line;
    }
    
}
# end of class



# ------------------------------------------------------
class phpfmgImage
{
    var $im = null;
    var $width = 73 ;
    var $height = 33 ;
    var $text = '' ; 
    var $line_distance = 8;
    var $text_len = 4 ;

    function phpfmgImage( $text = '', $len = 4 ){
        $this->text_len = $len ;
        $this->text = '' == $text ? $this->uniqid( $this->text_len ) : $text ;
        $this->text = strtoupper( substr( $this->text, 0, $this->text_len ) );
    }
    
    function create(){
        $this->im = imagecreate( $this->width, $this->height );
        $bgcolor   = imagecolorallocate($this->im, 255, 255, 255);
        $textcolor = imagecolorallocate($this->im, 0, 0, 0);
        $this->drawLines();
        imagestring($this->im, 5, 20, 9, $this->text, $textcolor);
    }
    
    function drawLines(){
        $linecolor = imagecolorallocate($this->im, 210, 210, 210);
    
        //vertical lines
        for($x = 0; $x < $this->width; $x += $this->line_distance) {
          imageline($this->im, $x, 0, $x, $this->height, $linecolor);
        };
    
        //horizontal lines
        for($y = 0; $y < $this->height; $y += $this->line_distance) {
          imageline($this->im, 0, $y, $this->width, $y, $linecolor);
        };
    }
    
    function out( $filename = '' ){
        if( function_exists('imageline') ){
            $this->create();
            if( '' == $filename ) header("Content-type: image/png");
            ( '' == $filename ) ? imagepng( $this->im ) : imagepng( $this->im, $filename );
            imagedestroy( $this->im ); 
        }else{
            $this->out_predefined_image(); 
        };
    }

    function uniqid( $len = 0 ){
        $md5 = md5( uniqid(rand()) );
        return $len > 0 ? substr($md5,0,$len) : $md5 ;
    }
    
    function out_predefined_image(){
        header("Content-type: image/png");
        $data = $this->getImage(); 
        echo base64_decode($data);
    }
    
    // Use predefined captcha random images if web server doens't have GD graphics library installed  
    function getImage(){
        $images = array(
			'F0A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhimMEx1QBILaGAMYQCKB6CIsbYyOjo6iKCIiTS6gkgk94VGTVuZuioKCBHug6prdEDXGxrQyoBmB2tDwBQGNLcAxQJQxRgCWBsCQ0MGQfhREWJxHwBRvc4d16rxxwAAAABJRU5ErkJggg==',
			'B2A2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQrAIAxF4+AN7H10cE/BLJ4mDt7AHsHFUzadamjHFsyHDI8PeQTGYxhWyi9+hCZBg8NPDJutQIA4s+pKCME71YMSGdlNfpRH7yNLbj/pNctY1I0KaEm2Ysbbq61dWBhq540i75QW+N+HefE7AfmuzogcSmuxAAAAAElFTkSuQmCC',
			'184F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHUNDkMRYHVhbGVodHZDViTqINDpMRRVjBKkLhIuBnbQya2XYyszM0Cwk94HUsTai6xVpdA0NxBBzwFAHtANNTDQE7GYUsYEKPypCLO4DALQkx8LWgHbPAAAAAElFTkSuQmCC',
			'BD4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQxgaHaYGIIkFTBFpZWh1CBBBFmsVAapydGBBVdfoEOjogOy+0KhpKzMzM7OQ3QdS59oIVwc3zzU0EEPMoRHDjlag+1Dcgs3NAxV+VIRY3AcAITfOjJ35J/UAAAAASUVORK5CYII=',
			'34CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7RAMYWhlCHUNDkMQCpjBMZXQIdEBRCVTF2iCIKjaF0ZW1gREmBnbSyqilS5euWhmahey+KSKtSOqg5omGumKIMbSi2wF0Syu6W6BuRtU7QOFHRYjFfQAebsjWxvm53gAAAABJRU5ErkJggg==',
			'C084' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGRoCkMREWhlDGB0dGpHFAhpZW1kbAlpRxBpEGh0dHaYEILkvatW0lVmhq6KikNwHUefogK7XtSEwNATTDmxuQRHD5uaBCj8qQizuAwABOc20YjQkLgAAAABJRU5ErkJggg==',
			'DAD6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGaY6IIkFTGEMYW10CAhAFmtlbWVtCHQQQBETaXQFiiG7L2rptJWpqyJTs5DcB1WHZp5oKEivCBbzUMSmAMXQ3BIaABRDc/NAhR8VIRb3AQCEhc8tHqYBKwAAAABJRU5ErkJggg==',
			'D084' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGRoCkMQCpjCGMDo6NKKItbK2sgJJVDGRRkdHhykBSO6LWjptZVboqqgoJPdB1Dk6oOt1bQgMDcG0A5tbUMSwuXmgwo+KEIv7AJW8zvTxcF/6AAAAAElFTkSuQmCC',
			'1F63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUIdkMRYHUQaGB0dHQKQxESBYqwNDg0iKHpBYgwNAUjuW5k1NWzp1FVLs5DcB1bn6NAQgKE3AIt5mGIYbgkBqkBz80CFHxUhFvcBADZYyd9B+3uGAAAAAElFTkSuQmCC',
			'7AFD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA0MdkEVbGUNYGxgdAlDEWFtBYiLIYlNEGl0RYhA3RU1bmRq6MmsakvuAKpDVgSFrg2gouphIA6a6AKhYAKYYqpsHKPyoCLG4DwBB6sr+dBxgYwAAAABJRU5ErkJggg==',
			'4BF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGsIYGNAQgi4WItLI2MDQiizGGiDS6NjC0IouxTgGrmxKA5L5p06aGLQ1dFRWF5L4AsDpGB2S9oaEg8xhDQ1DcArYD1S0QO9DEgG5GFxuo8KMexOI+AJG/zWaFOvVDAAAAAElFTkSuQmCC',
			'F88D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkMQCGlhbGR0dHQJQxEQaXRsCHUSwqBNBcl9o1MqwVaErs6YhuQ9NHR7zcNuB6hZMNw9U+FERYnEfACkqzERbU45GAAAAAElFTkSuQmCC',
			'400D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjAEMExhDHVAFgthDGEIZXQIQBJjDGFtZXR0dBBBEmOdItLo2hAIEwM7adq0aStTV0VmTUNyXwCqOjAMDcUUY5iCaQfQbRhuwermgQo/6kEs7gMAzIPKZ44WxaEAAAAASUVORK5CYII=',
			'8F2F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGUNDkMREpog0MDo6OiCrC2gVaWBtCEQRA6ljQIiBnbQ0amrYqpWZoVlI7gOra2XEMI9hChaxAEYMOxgdUMVYA4BuCUV1y0CFHxUhFvcBAMw6yUXh0QSKAAAAAElFTkSuQmCC',
			'E3FB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDA0MdkMQCGkRaWRsYHQJQxBgaXYFiIqhiyOrATgqNWhW2NHRlaBaS+9DU4TMPiximW8BubmBEcfNAhR8VIRb3AQDAfsvQ2s3r2wAAAABJRU5ErkJggg==',
			'0F2D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUMdkMRYA0QaGB0dHQKQxESmiDSwNgQ6iCCJBbSCeHAxsJOilk4NW7UyM2sakvvA6loZMfVOQRUD2cEQgCoGdosDI4pbQCpYQwNR3DxQ4UdFiMV9AIncyf086TslAAAAAElFTkSuQmCC',
			'C248' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WEMYQxgaHaY6IImJtLK2MrQ6BAQgiQU0igBVOTqIIIs1AHUGwtWBnRS1atXSlZlZU7OQ3AeUn8LaiGZeA0MAa2ggqnmNjA4Mjah2AN0CsgVFL2uIaKgDmpsHKvyoCLG4DwAKC82gho7llwAAAABJRU5ErkJggg==',
			'FDCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHUMdkMQCGkRaGR0CHQJQxRpdGwQdRDDEGGHqwE4KjZq2MnXVytAsJPehqUMRwzQPww4sbsF080CFHxUhFvcBAMNCzXvPGi7DAAAAAElFTkSuQmCC',
			'BBE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHaY6IIkFTBFpZW1gCAhAFmsVaXRtYHQQwFDH6IDsvtCoqWFLQ1emZiG5D6oOq3kihMSwuAWbmwcq/KgIsbgPAClYzQONduqvAAAAAElFTkSuQmCC',
			'2646' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM3QMQ6AMAgFUBy6O+B96OBOE+sNHPQUZeAGtnfQU+pIo6NG+dtPIC/AfpkEf8orPsfNAEKZTIerU1BiNh0rCmRPrd1WTBA8Vb5Sxm2ep8X6uFMnvrrXEEofA6G1JBQSX3WYTovUlhiv5q/+92BufAfPmsv9EJ8TgAAAAABJRU5ErkJggg==',
			'3DDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RANEQ1hDGUNDkMQCpoi0sjY6OqCobBVpdG0IRBWbgiIGdtLKqGkrU1dFhmYhu28KFr3YzMMihs0tUDej6h2g8KMixOI+AC4jy0W090XGAAAAAElFTkSuQmCC',
			'2170' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA1qRxUSmMAYwNARMdUASC2hlBYkFBCDrbmUIYGh0dBBBdt+0VVGrlq7MmobsPqAdDFMYYerAkNEBJIoqxtoAEmFAsQMoHwAUR3FLaChrKCvI9kEQflSEWNwHAM3iyT9+VPPWAAAAAElFTkSuQmCC',
			'7CEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1lDHaYGIIu2sja6NjAEiKCIiTS4NjA6sCCLTRFpYAWKobgvatqqpaErs5Ddx+iAog4MWRswxUQaMO0IaMB0S0ADFjcPUPhREWJxHwBqacracJs1kQAAAABJRU5ErkJggg==',
			'517C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA6YGIIkFNDAGAMkAERQxVqBYoAMLklhgAEMAQ6OjA7L7wqatilq1dGUWivtageqmMDqg2AwSC0AVC2gFiTCi2CEyBei+BgYUt7ACXQwUQ3HzQIUfFSEW9wEAtMLJBk9JXOwAAAAASUVORK5CYII=',
			'8771' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQ11DA1qRxUSmMDQ6NARMRRYDqgCJhaKpA4nC9IKdtDRq1bRVS4EQyX1AdQEMILUo5jE6AEXRxFgbgKJobhFpYG1AFWMNAIuFBgyC8KMixOI+AOUlzHHPkZutAAAAAElFTkSuQmCC',
			'7B9E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGUMDkEVbRVoZHR0dGFDFGl0bAlHFpoi0siLEIG6Kmhq2MjMyNAvJfYwOIq0MIah6WRtEGh3QzBMBijmiiQU0YLoloAGLmwco/KgIsbgPAFnqygtGfJUEAAAAAElFTkSuQmCC',
			'56AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QkMYQximMIY6IIkFNLC2MoQyOgSgiIk0Mjo6OoggiQUGiDSwNgTCxMBOCps2LWzpqsisacjuaxVtRVIHFRNpdA1FFQsAiaGpE5nCCtaL7BbWAMYQoBiKmwcq/KgIsbgPAK4ky/GMisMXAAAAAElFTkSuQmCC',
			'DBEE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAUklEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHUMDkMQCpoi0sjYwOiCrC2gVaXTFFENWB3ZS1NKpYUtDV4ZmIbkPTR0+8zDFsLgFm5sHKvyoCLG4DwBB4ctxmHzqggAAAABJRU5ErkJggg==',
			'6E29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaY6IImJTBFpYHR0CAhAEgtoEWlgbQh0EEEWawDx4GJgJ0VGTQ1btTIrKgzJfSFA8xhaGaai6G0F8qYwNGCIBTCg2AF2iwMDiltAbmYNDUBx80CFHxUhFvcBACaCy1t/QdzxAAAAAElFTkSuQmCC',
			'1C66' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGaY6IImxOrA2Ojo6BAQgiYk6iDS4Njg6CKDoFWlgBZLI7luZNW3V0qkrU7OQ3AdW5+iIYh5EbyCQRBVzxRDD4pYQTDcPVPhREWJxHwCQoslkBC6mwwAAAABJRU5ErkJggg==',
			'03E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDHRoCkMRYA0RaWRsYGpHFRKYwNLo2MLQiiwW0MoDUTQlAcl/U0lVhS0NXRUUhuQ+ijtEBTS/QPMbQEEw7sLkFRQybmwcq/KgIsbgPALgczKrXONvhAAAAAElFTkSuQmCC',
			'5940' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHVqRxQIaWFsZWh2mOqCIiTQCRQICkMQCA4BigY4OIkjuC5u2dGlmZmbWNGT3tTIGujbC1UHFGBpdQwNRxAJaWRodGlHtEJkCdEsjqltYAzDdPFDhR0WIxX0AF4LNlIHOORMAAAAASUVORK5CYII=',
			'5D69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNEQxhCGaY6IIkFNIi0Mjo6BASgijW6Njg6iCCJBQaAxBhhYmAnhU2btjJ16qqoMGT3tQLVOTpMRdYLFgOZimwHRAzFDpEpmG5hDcB080CFHxUhFvcBAE/qzQoRqaBnAAAAAElFTkSuQmCC',
			'8DAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQximMIY6IImJTBFpZQhldAhAEgtoFWl0dHR0EEFV1+jaEAhTB3bS0qhpK1NXRYZmIbkPTR3cPNfQQBTzwGINgeh2tLKi6QW5GSiG4uaBCj8qQizuAwBM381TKs9KzgAAAABJRU5ErkJggg==',
			'9BE9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaY6IImJTBFpZW1gCAhAEgtoFWl0bWB0EEEVA6qDi4GdNG3q1LCloauiwpDcx+oKNm8qsl4GsHlAu5DEBCBiKHZgcws2Nw9U+FERYnEfAECty26eeKRJAAAAAElFTkSuQmCC',
			'CE71' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WENEQ1lDA1qRxURaRYBkwFRksYBGsFgoilgDUKzRAaYX7KSoVVPDVi0FQiT3gdVNYWjF0BuAJga0g9GBAcMtrA2oYmA3NzCEBgyC8KMixOI+AH5WzD+Oe0wJAAAAAElFTkSuQmCC',
			'7950' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHVpRRFtZW1kbGKY6oIiJNLo2MAQEIItNAYpNZXQQQXZf1NKlqZmZWdOQ3MfowBjo0BAIUweGQPMb0cVEGliAdgSg2BHQwNrK6OiA4paABsYQhlAGVDcPUPhREWJxHwDn1MwPYn/ykAAAAABJRU5ErkJggg==',
			'2EDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUNDkMREpog0sDY6OiCrC2gFijUEoogxoIpB3DRtatjSVZGhWcjuC8DUy+iAKcbagCkm0oDpltBQsJtR3TJA4UdFiMV9AP44yYOhdCOSAAAAAElFTkSuQmCC',
			'42D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM3QvRGAIAyA0VBkA9wHC/sUpHEEpwgFG3BsQCFT+lMlp6WeJt2j4LtAv4zAn/advuIisuOoLWLGFMQrc9GnScgYFjiNVF+tvbU+r4vqowIFhbL+lxkIjxfbEnYjayiYxmBt4Imdta/u99ze9G2klsxhFaWyXgAAAABJRU5ErkJggg==',
			'2227' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUNDkMREprC2Mjo6NIggiQW0ijS6NgSgiDG0MjQ6AMUCkN03bdXSVSuzVmYhuy+AYQpQbSuyvYwOYNEpKG6BiAYgi4mARYHiSGKhoaKhrqGBKGIDFX5UhFjcBwCEp8p7Jvz/CQAAAABJRU5ErkJggg==',
			'573F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx1DGUNDkMQCGhgaXRsdHRjQxBwaAlHEAgMYWhkQ6sBOCpu2atqqqStDs5Dd18oQwIBmHkMrI5CPal5AK2sDupjIFJEGVjS9rAEiDYyhjKjmDVD4URFicR8AzJ/Kr9E0JhIAAAAASUVORK5CYII=',
			'CD3B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WENEQxhDGUMdkMREWkVaWRsdHQKQxAIaRRodGgIdRJDFGoBiCHVgJ0WtmrYya+rK0Cwk96GpQ4ihm4fFDmxuwebmgQo/KkIs7gMADUvNseE+e8UAAAAASUVORK5CYII=',
			'A177' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0NDkMRYAxgDGBoCGkSQxESmsGKIBbQyBDA0OgBFEe6LWroqatXSVSuzkNwHVjeFoRXZ3tBQoBhIFM08kHvQxVhBrkQRYw1FFxuo8KMixOI+AMjMyhJJtStvAAAAAElFTkSuQmCC',
			'32F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDA6Y6IIkFTGFtZW1gCAhAVtkq0ujawOgggiw2hQEoxtAgguS+lVGrli4NXbUqCtl9UximAM1rdEAxjyGAFUSiiDE6sIJUo7qlAeQWVDeLhgLdEhoyCMKPihCL+wBYrstbNCAwAgAAAABJRU5ErkJggg==',
			'AB62' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxBoi0Mjo6BAQgiYlMEWl0bXB0EEESC2gVaWUFySG5L2rp1LClU4E0kvvA6hwdGpHtCA0FmRfQyoBqHkhsCpoY2C2oYiA3M4aGDILwoyLE4j4AJm7NPesxrDcAAAAASUVORK5CYII=',
			'5575' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDA0MDkMQCGkSAZKADAwGxwACREIZGR1cHJPeFTZu6dNXSlVFRyO5rZWh0mMIANoEBWSwAVSygVaTR0YHRAVlMZAprK2sDQwCy+1gDGEOAYlMdBkH4URFicR8AElrL7aPtDvAAAAAASUVORK5CYII=',
			'4DE9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37poiGsIY6THVAFgsRaWVtYAgIQBJjDBFpdG1gdBBBEmOdgiIGdtK0adNWpoauigpDcl8AWB3DVGS9oaFgsQYRFLeAxRzQxDDcgtXNAxV+1INY3AcA2RvME0o17+MAAAAASUVORK5CYII=',
			'13B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGUNDkMRYHURaWRsdGkSQxEQdGBpdGwJQxBgdGMDqApDctzJrVdjSUBCFcB9UXSuqvWDzpmARC0AVA7nF0QFZTDQE7GYUsYEKPypCLO4DADcXyaf8g6eTAAAAAElFTkSuQmCC',
			'E9AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYQximMIY6IIkFNLC2MoQyOgSgiIk0Ojo6Ooigibk2BMLUgZ0UGrV0aeqqyNAsJPcFNDAGIqmDijE0uoYGopnHAjZPBM0trGh6QW4GiqG4eaDCj4oQi/sAsaTNmm6Vfh0AAAAASUVORK5CYII=',
			'8CA6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMEx1QBITmcLa6BDKEBCAJBbQKtLg6OjoIICiTqSBtSHQAdl9S6OmrVq6KjI1C8l9UHUY5rGGBjqIoIm5NqCKgdzi2hCAohfkZtaGABQ3D1T4URFicR8AZE3Na29Q+AsAAAAASUVORK5CYII=',
			'D729' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGaY6IIkFTGFodHR0CAhAFmtlaHRtCHQQQRVrZUCIgZ0UtXTVtFUrs6LCkNwHVBcAVDkVVS+jA8MUhgZUMdYGoEpUO6aINABVorglNECkgTU0AMXNAxV+VIRY3AcAhvLNFvzLd1EAAAAASUVORK5CYII=',
			'8C5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHUMdkMREprA2ujYwOgQgiQW0ijSAxERQ1Ik0sE6Fi4GdtDRq2qqlmZlZ05DcB1LH0BCIohdkHjYxVzQxkFscHR1R3AJyM0MoI4qbByr8qAixuA8A0PjL5wnRHd0AAAAASUVORK5CYII=',
			'F495' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZWhlCGUMDkMSA7KmMjo4ODKhioawNgWhijK5AMVcHJPeFRi1dujIzMioKyX0BDSKtDCFAEkWvaKhDA7oYQysj0A4MMUeHADT3Ad3MMNVhEIQfFSEW9wEABNTMUbCkQ9EAAAAASUVORK5CYII=',
			'040A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YWhmmADGSGGsAw1SGUIapDkhiIlMYQhkdHQICkMQCWhldWRsCHUSQ3Be1FAhWRWZNQ3JfQKtIK5I6qJhoqGtDYGgIqh2tjI6OKOqAbmkF2owiBnEzqthAhR8VIRb3AQAONMpgqV1dAgAAAABJRU5ErkJggg==',
			'B3A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgNYQximMLQiiwVMEWllCGWYiiLWytDo6OgQiqqOoZUVJIPkvtCoVWFLV0UtRXYfmjq4ea6hWMTQ1QHdgq4X5GagWGjAIAg/KkIs7gMAlUbOZwqcAdYAAAAASUVORK5CYII=',
			'7DF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA0NDkEVbRVpZgbQIqlijK7rYFIhYALL7oqatTA1dtTILyX2MDmB1rcj2sjaAxaYgi4lAxAKQxQIaQG5hdEAVA7oZTWygwo+KEIv7APXuy9MM8KueAAAAAElFTkSuQmCC',
			'13F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDA0MDkMRYHURaWYEyyOpEHRgaXdHEgDyQOlcHJPetzFoVtjR0ZVQUkvsg6hgaRFD1As3DJsbogCoGcgtDALL7REOAbm5gmOowCMKPihCL+wCP68fhF4kd9gAAAABJRU5ErkJggg==',
			'0FF4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB1EQ11DAxoCkMRYA0QaWBsYGpHFRKaAxVqRxQJawWJTApDcF7V0atjS0FVRUUjug6hjdMDUyxgagmkHNregiDE6YIoNVPhREWJxHwDSYcyp7/ACmAAAAABJRU5ErkJggg==',
			'D53E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QgNEQxmBMABJLGCKSANro6MDsrqAVhEgGYguFsKAUAd2UtTSqUtXTV0ZmoXkvoBWhkYHDPOAYpjmYYpNYW1Fd0toAGMIupsHKvyoCLG4DwDy78zTQrlH3AAAAABJRU5ErkJggg==',
			'FF36' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QkNFQx1DGaY6IIkFNIg0sDY6BASgiTE0BDoIoIs1Ojoguy80amrYqqkrU7OQ3AdVh9U8ESLEsLmFEc3NAxV+VIRY3AcAYn3OAYacgxAAAAAASUVORK5CYII=',
			'E342' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNYQxgaHaY6IIkFNIi0MrQ6BASgiIFUOTqIoIq1MgQ6NIgguS80alXYysysVVFI7gOpY210aHRAM881NKCVAd2ORocpDOhuaXQIwHSzY2jIIAg/KkIs7gMAGZHOfinCT7EAAAAASUVORK5CYII=',
			'129D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYHVhbGR0dHQKQxEQdRBpdGwIdRFD0MiCLgZ20MmvV0pWZkVnTkNwHVDeFIQRDbwADhnlAiCHG2oDhlhDRUAc0Nw9U+FERYnEfAPBHx/6E0f0iAAAAAElFTkSuQmCC',
			'E0CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMYAhhCHUMdkMQCGhhDGB0CHQJQxFhbWRsEHURQxEQaXRsYYerATgqNmrYyddXK0Cwk96GpQxETIWgHpluwuXmgwo+KEIv7APjPzAM8eeoCAAAAAElFTkSuQmCC',
			'4875' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpI37pjCGsIYGhgYgi4WwtjI0BDogq2MMEWl0QBNjnQJU1+jo6oDkvmnTVoatWroyKgrJfQEgdVMYGkSQ9IaGAs0LQBVjmCLS6OjA6IAqxtrK2sAQgOI+kJsbGKY6DIbwox7E4j4AKH7Lbcif78oAAAAASUVORK5CYII=',
			'1EB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGUIdkMRYHUQaWBsdHQKQxERBYg0BDSIoekHqHBoCkNy3Mmtq2NLQVUuzkNyHpg4hhs08rHaguSUE080DFX5UhFjcBwDKUMpYTHK64wAAAABJRU5ErkJggg==',
			'A16F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUNDkMRYAxgDGB0dHZDViUxhDWBtQBULaGUAijHCxMBOiloKRFNXhmYhuQ+sDs280FCQ3kAs5mGKobsloJU1FOhmFLGBCj8qQizuAwBfh8e7IRHyrAAAAABJRU5ErkJggg==',
			'D604' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYQximMDQEIIkFTGFtZQhlaEQRaxVpZHR0aEUTa2AFqg5Acl/U0mlhS1dFRUUhuS+gVbSVtSHQAd0814bA0BA0MUdHB2xuQRHD5uaBCj8qQizuAwABgs9TEjlUqQAAAABJRU5ErkJggg==',
			'2C6A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGVqRxUSmsDY6OjpMdUASC2gVaXBtcAgIQNYNFGNtYHQQQXbftGmrlk5dmTUN2X0BQHVAA0WQ9IJ0sTYEhoYgu6UBZEcgijqgKqBbUPWGhoLczIgiNlDhR0WIxX0AauzLal/WB4oAAAAASUVORK5CYII=',
			'B6B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGaY6IIkFTGFtZW10CAhAFmsVaWRtCHQQQFEn0sDa6OiA7L7QqGlhS0NXpmYhuS9giijQPEcM81yB5okQEsPiFmxuHqjwoyLE4j4AOlTN7ga1Fu8AAAAASUVORK5CYII=',
			'2308' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANYQximMEx1QBITmSLSyhDKEBCAJBbQytDo6OjoIIKsu5WhlbUhAKYO4qZpq8KWroqamoXsvgAUdWDI6MDQ6NoQiGIeawOmHSINmG4JDcV080CFHxUhFvcBAF7zy42PcZjpAAAAAElFTkSuQmCC',
			'A967' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dGkSQxESmiDS6NqCKBbSCxIA0kvuili5dmjp11cosJPcFtDIGujo6tCLbGxrKANQbMIUBxTwWkFgAqhjILY4OqGJgN6OIDVT4URFicR8AlzPMkcksYX0AAAAASUVORK5CYII=',
			'7DC7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxhCHUNDkEVbRVoZHQIaRFDFGl0bBFDFpoDEGBoCkN0XNW1l6qpVK7OQ3MfoAFbXimwvawNYbAqymAhYTCAAWQzoCqBbAh1QxcBuRhEbqPCjIsTiPgCUw8xb7vZB3wAAAABJRU5ErkJggg==',
			'365B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDHUMdkMQCprC2sjYwOgQgq2wVaQSJiSCLTRFpYJ0KVwd20sqoaWFLMzNDs5DdN0W0laEhEMM8B6CYCJqYK5oYyC2Mjo4oekFuZghlRHHzQIUfFSEW9wEAc8DK3UxzUfQAAAAASUVORK5CYII=',
			'78B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDGaY6IIu2srayNjoEBKCIiTS6NgQ6iCCLTQGpc4SJQdwUtTJsaeiqqDAk9zE6gM2biqyXtQFkXkADspgIRAzFjoAGTLcENGBx8wCFHxUhFvcBAIOnzKX50pCUAAAAAElFTkSuQmCC',
			'EDA5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQximMIYGIIkFNIi0MoQyOjCgijU6OjpiiLk2BLo6ILkvNGraytRVkVFRSO6DqAOS6HpDsYg1BDqgibWyNgQEILsP5Gag2FSHQRB+VIRY3AcAbNnOYg4DwmoAAAAASUVORK5CYII=',
			'1B81' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGVqRxVgdRFoZHR2mIouJOog0ujYEhKLqBauD6QU7aWXW1LBVoauWIrsPTR1MDGQeMWIYekVDwG4ODRgE4UdFiMV9AHb/yV3kaTCaAAAAAElFTkSuQmCC',
			'197C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA6YGIImxOrC2MjQEBIggiYk6iDQ6NAQ6sKDoBYo1Ojogu29l1tKlWUtXZiG7D2hHoMMURgdUexkaHQLQxViApjGi2cHaytrAgOqWEKCbGxhQ3DxQ4UdFiMV9APXVyJb2FAxYAAAAAElFTkSuQmCC',
			'4BD0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37poiGsIYytKKIhYi0sjY6THVAEmMMEWl0bQgICEASY50CVNcQ6CCC5L5p06aGLV0VmTUNyX0BqOrAMDQUZB6qGMMUTDsYpmC6BaubByr8qAexuA8ATM7NO+pvfCoAAAAASUVORK5CYII=',
			'C6B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGaY6IImJtLK2sjY6BAQgiQU0ijSyNgQ6iCCLNYg0IKkDOylq1bSwpaGrpmYhuS+gQRTTvAaRRld08xoxxbC5BZubByr8qAixuA8AqnDNcxAff34AAAAASUVORK5CYII=',
			'F5A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBILaBBpYAhlCAhAE2N0dHQQQBULYW0IdEB2X2jU1KVLV0WmZiG5D2hOo2tDIJp5QLHQQAcRVPNA6tDEWFtZGwLQ9DIC7Q1AcfNAhR8VIRb3AQATjM4KqpWA9AAAAABJRU5ErkJggg==',
			'6BEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaYGIImJTBFpZW1gCBBBEgtoEWl0bWB0YEEWawCpY3RAdl9k1NSwpaErs5DdFzIFRR1EbyvEPGxiyHZgcws2Nw9U+FERYnEfAPQ1yzvZCykfAAAAAElFTkSuQmCC',
			'132A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGVqRxVgdRFoZHR2mOiCJiTowNLo2BAQEoOgF6Qt0EEFy38qsVWGrVmZmTUNyH1hdKyNMHUys0WEKY2gIulgAujqgWxxQxURDWENYQwNRxAYq/KgIsbgPABn9x/H9PJ/CAAAAAElFTkSuQmCC',
			'D7C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1CHaY6IIkFTGFodHQICAhAFmtlaHRtEHQQQRVrZW1ghImBnRS1dNW0pUAqDMl9QHUBrA0MU1H1MjoAxRpQxVgbWBsEUO2YIgK0AdUtoQFAFWhuHqjwoyLE4j4AAPzNh8b3AxAAAAAASUVORK5CYII=',
			'F17C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA6YGIIkFNDAGAMkAERQxVqBYoAMLihhDAEOjowOy+0KjVkWtWroyC9l9YHVTGB0Y0PUGYIoxOjBi2MEKxGhuCQWKobh5oMKPihCL+wDsucotx4kVFAAAAABJRU5ErkJggg==',
			'8620' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaWRtCAgIQFEnAiQDHUSQ3Lc0alrYqpWZWdOQ3CcyRbSVoZURpg5unsMULGIBDGh2AN3iwIDiFpCbWUMDUNw8UOFHRYjFfQCA0sup9sAYLQAAAABJRU5ErkJggg==',
			'2590' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQNbdKhLC2hDoIILsvmlTl67MjMyahuy+AIZGhxC4OjBkdACKNaCKsTaINDqi2QG0tRXdLaGhjCHobh6o8KMixOI+AGY2y5tMnaA3AAAAAElFTkSuQmCC',
			'0255' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUMDkMRYA1hbWYEyyOpEpog0uqKJBbQyNLpOZXR1QHJf1NJVS5dmZkZFIbkPqG4KkGwQQdUbgC4mMoXRgbUh0EEE1S0NjI4OAcjuY3QQDXUIZZjqMAjCj4oQi/sAJq7KsoYjabIAAAAASUVORK5CYII=',
			'4234' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsRGAMAhFScEGug/ZAO9Ck2lI4QaYDWwypdoRtdRTfvfuc7wD2mUU/pR3/CykIKDsWcIZCxXPQhoKKc+eocHeImPnV2tb29Jydn5ssDcj+V0RYNBJUu9Ch0nnYqh4XO7YKPHs/NX/nsuN3wbyG8576cF6dQAAAABJRU5ErkJggg==',
			'E356' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDHaY6IIkFNIi0sjYwBASgiDE0ujYwOgigirWyTmV0QHZfaNSqsKWZmalZSO4DqWNoCMQwz6Eh0EEEww50MZFWRkcHFL0gNzOEMqC4eaDCj4oQi/sA1nbMxMI3vEUAAAAASUVORK5CYII=',
			'9136' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhhDGaY6IImJTGEMYG10CAhAEgtoBapsCHQQQBFjCGBodHRAdt+0qauiVk1dmZqF5D5WV7A6FPMYQHqB5okgiQlgEROZwoDhFtYA1lB0Nw9U+FERYnEfAIsfygpJtiHMAAAAAElFTkSuQmCC',
			'1854' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHRoCkMRYHVhbWRsYGpHFRB1EGl0bGFoDUPQC1U1lmBKA5L6VWSvDlmZmRUUhuQ+kjqEh0AFVr0ijQ0NgaAiamCvQJeh2MDqiuk80hDGEIZQBRWygwo+KEIv7AIM0yvJz0CtWAAAAAElFTkSuQmCC',
			'7964' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGRoCkEVbWVsZHR0aUcVEGl0bHFpRxKaAxBimBCC7L2rp0tSpq6KikNzH6MAY6Oro6ICsl7WBAag3MDQESUykgQUoFoDiloAGsFvQxLC4eYDCj4oQi/sAIHnN4KrTMUQAAAAASUVORK5CYII=',
			'9281' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMRRYLaBVpdG0ICEUVY2h0dHSA6QU7adrUVUtXhQIxkvtYXRmmMCLUQWArQwAryAQkMYFWRgd0MaBbGtD1sgaIhjqEMoQGDILwoyLE4j4ASabLbM6Uy+wAAAAASUVORK5CYII=',
			'C40B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WEMYWhmmMIY6IImJtDJMZQhldAhAEgtoBIo4OjqIIIs1MLqyNgTC1IGdFLVq6dKlqyJDs5DcFwA0EUkdVEw01BUoJoJqRyu6HUC3tKK7BZubByr8qAixuA8A9OTLQbYqZz0AAAAASUVORK5CYII=',
			'E72D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGUMdkMQCGhgaHR0dHQLQxFwbAh1EUMVaGRBiYCeFRq2atmplZtY0JPcB1QUwtDKi6WV0YJiCLsYKVIkuJgJUyYjiltAQkQbW0EAUNw9U+FERYnEfANA3y65PXzqwAAAAAElFTkSuQmCC',
			'EBE5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1hDHUMDkMQCGkRaWRsYHRhQxRpdMcVA6lwdkNwXGjU1bGnoyqgoJPdB1DE0iGCYh02M0UEEww6GAGT3QdzsMNVhEIQfFSEW9wEAO7TMatIuhS4AAAAASUVORK5CYII=',
			'F891' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGVqRxQIaWFsZHR2mooqJNLo2BISiq2NtCIDpBTspNGpl2MrMqKXI7gOpYwgJaEU3z6EBU8wRQwzsFjQxsJtDAwZB+FERYnEfAKHbzXlMLYLqAAAAAElFTkSuQmCC',
			'A641' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHVqRxVgDWFsZWh2mIouJTBFpZJjqEIosFtAq0sAQCNcLdlLU0mlhKzOzliK7L6BVtJUVzY7QUJFG19CAVjTzGh3Q1AW0At2CIQZ2c2jAIAg/KkIs7gMAWi3NiSbatRQAAAAASUVORK5CYII=',
			'A915' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nM2QMQ6AIAxF20RuwIHq4F4TWDiCp5CBG1RvwKCnFLeCjprQv720Py+F8zEr9JRf/JDQgaBnxQybBA5J71mxcWwYJxtJcCLlF3LOy36EoPw44UxSOtSt9xBbxmm4+6hmxUWAuWLo0NNGHfzvw7z4XRyjy9nlO3SVAAAAAElFTkSuQmCC',
			'C209' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WEMYQximMEx1QBITaWVtZQhlCAhAEgtoFGl0dHR0EEEWa2BodG0IhImBnRS1atXSpauiosKQ3AdUN4W1IWAqmt4AoFgDilgjowOjowOKHUC3NKC7hTVENNQBzc0DFX5UhFjcBwAtMcwwaIPzqAAAAABJRU5ErkJggg=='        
        );
        $this->text = array_rand( $images );
        return $images[ $this->text ] ;    
    }
    
    function out_processing_gif(){
        $image = dirname(__FILE__) . '/processing.gif';
        $base64_image = "R0lGODlhFAAUALMIAPh2AP+TMsZiALlcAKNOAOp4ANVqAP+PFv///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH/C05FVFNDQVBFMi4wAwEAAAAh+QQFCgAIACwAAAAAFAAUAAAEUxDJSau9iBDMtebTMEjehgTBJYqkiaLWOlZvGs8WDO6UIPCHw8TnAwWDEuKPcxQml0Ynj2cwYACAS7VqwWItWyuiUJB4s2AxmWxGg9bl6YQtl0cAACH5BAUKAAgALAEAAQASABIAAAROEMkpx6A4W5upENUmEQT2feFIltMJYivbvhnZ3Z1h4FMQIDodz+cL7nDEn5CH8DGZhcLtcMBEoxkqlXKVIgAAibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkphaA4W5upMdUmDQP2feFIltMJYivbvhnZ3V1R4BNBIDodz+cL7nDEn5CH8DGZAMAtEMBEoxkqlXKVIg4HibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpjaE4W5tpKdUmCQL2feFIltMJYivbvhnZ3R0A4NMwIDodz+cL7nDEn5CH8DGZh8ONQMBEoxkqlXKVIgIBibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpS6E4W5spANUmGQb2feFIltMJYivbvhnZ3d1x4JMgIDodz+cL7nDEn5CH8DGZgcBtMMBEoxkqlXKVIggEibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpAaA4W5vpOdUmFQX2feFIltMJYivbvhnZ3V0Q4JNhIDodz+cL7nDEn5CH8DGZBMJNIMBEoxkqlXKVIgYDibbK9YLBYvLtHH5K0J0IACH5BAUKAAgALAEAAQASABIAAAROEMkpz6E4W5tpCNUmAQD2feFIltMJYivbvhnZ3R1B4FNRIDodz+cL7nDEn5CH8DGZg8HNYMBEoxkqlXKVIgQCibbK9YLBYvLtHH5K0J0IACH5BAkKAAgALAEAAQASABIAAAROEMkpQ6A4W5spIdUmHQf2feFIltMJYivbvhnZ3d0w4BMAIDodz+cL7nDEn5CH8DGZAsGtUMBEoxkqlXKVIgwGibbK9YLBYvLtHH5K0J0IADs=";
        $binary = is_file($image) ? join("",file($image)) : base64_decode($base64_image); 
        header("Cache-Control: post-check=0, pre-check=0, max-age=0, no-store, no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Content-type: image/gif");
        echo $binary;
    }

}
# end of class phpfmgImage
# ------------------------------------------------------
# end of module : captcha


# module user
# ------------------------------------------------------
function phpfmg_user_isLogin(){
    return ( isset($_SESSION['authenticated']) && true === $_SESSION['authenticated'] );
}


function phpfmg_user_logout(){
    session_destroy();
    header("Location: admin.php");
}

function phpfmg_user_login()
{
    if( phpfmg_user_isLogin() ){
        return true ;
    };
    
    $sErr = "" ;
    if( 'Y' == $_POST['formmail_submit'] ){
        if(
            defined( 'PHPFMG_USER' ) && strtolower(PHPFMG_USER) == strtolower($_POST['Username']) &&
            defined( 'PHPFMG_PW' )   && strtolower(PHPFMG_PW) == strtolower($_POST['Password']) 
        ){
             $_SESSION['authenticated'] = true ;
             return true ;
             
        }else{
            $sErr = 'Login failed. Please try again.';
        }
    };
    
    // show login form 
    phpfmg_admin_header();
?>
<form name="frmFormMail" action="" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:380px;height:260px;">
<fieldset style="padding:18px;" >
<table cellspacing='3' cellpadding='3' border='0' >
	<tr>
		<td class="form_field" valign='top' align='right'>Email :</td>
		<td class="form_text">
            <input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" class='text_box' >
		</td>
	</tr>

	<tr>
		<td class="form_field" valign='top' align='right'>Password :</td>
		<td class="form_text">
            <input type="password" name="Password"  value="" class='text_box'>
		</td>
	</tr>

	<tr><td colspan=3 align='center'>
        <input type='submit' value='Login'><br><br>
        <?php if( $sErr ) echo "<span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
        <a href="admin.php?mod=mail&func=request_password">I forgot my password</a>   
    </td></tr>
</table>
</fieldset>
</div>
<script type="text/javascript">
    document.frmFormMail.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();
}


function phpfmg_mail_request_password(){
    $sErr = '';
    if( $_POST['formmail_submit'] == 'Y' ){
        if( strtoupper(trim($_POST['Username'])) == strtoupper(trim(PHPFMG_USER)) ){
            phpfmg_mail_password();
            exit;
        }else{
            $sErr = "Failed to verify your email.";
        };
    };
    
    $n1 = strpos(PHPFMG_USER,'@');
    $n2 = strrpos(PHPFMG_USER,'.');
    $email = substr(PHPFMG_USER,0,1) . str_repeat('*',$n1-1) . 
            '@' . substr(PHPFMG_USER,$n1+1,1) . str_repeat('*',$n2-$n1-2) . 
            '.' . substr(PHPFMG_USER,$n2+1,1) . str_repeat('*',strlen(PHPFMG_USER)-$n2-2) ;


    phpfmg_admin_header("Request Password of Email Form Admin Panel");
?>
<form name="frmRequestPassword" action="admin.php?mod=mail&func=request_password" method='post' enctype='multipart/form-data'>
<input type='hidden' name='formmail_submit' value='Y'>
<br><br><br>

<center>
<div style="width:580px;height:260px;text-align:left;">
<fieldset style="padding:18px;" >
<legend>Request Password</legend>
Enter Email Address <b><?php echo strtoupper($email) ;?></b>:<br />
<input type="text" name="Username"  value="<?php echo $_POST['Username']; ?>" style="width:380px;">
<input type='submit' value='Verify'><br>
The password will be sent to this email address. 
<?php if( $sErr ) echo "<br /><br /><span style='color:red;font-weight:bold;'>{$sErr}</span><br><br>\n"; ?>
</fieldset>
</div>
<script type="text/javascript">
    document.frmRequestPassword.Username.focus();
</script>
</form>
<?php
    phpfmg_admin_footer();    
}


function phpfmg_mail_password(){
    phpfmg_admin_header();
    if( defined( 'PHPFMG_USER' ) && defined( 'PHPFMG_PW' ) ){
        $body = "Here is the password for your form admin panel:\n\nUsername: " . PHPFMG_USER . "\nPassword: " . PHPFMG_PW . "\n\n" ;
        if( 'html' == PHPFMG_MAIL_TYPE )
            $body = nl2br($body);
        mailAttachments( PHPFMG_USER, "Password for Your Form Admin Panel", $body, PHPFMG_USER, 'You', "You <" . PHPFMG_USER . ">" );
        echo "<center>Your password has been sent.<br><br><a href='admin.php'>Click here to login again</a></center>";
    };   
    phpfmg_admin_footer();
}


function phpfmg_writable_check(){
 
    if( is_writable( dirname(PHPFMG_SAVE_FILE) ) && is_writable( dirname(PHPFMG_EMAILS_LOGFILE) )  ){
        return ;
    };
?>
<style type="text/css">
    .fmg_warning{
        background-color: #F4F6E5;
        border: 1px dashed #ff0000;
        padding: 16px;
        color : black;
        margin: 10px;
        line-height: 180%;
        width:80%;
    }
    
    .fmg_warning_title{
        font-weight: bold;
    }

</style>
<br><br>
<div class="fmg_warning">
    <div class="fmg_warning_title">Your form data or email traffic log is NOT saving.</div>
    The form data (<?php echo PHPFMG_SAVE_FILE ?>) and email traffic log (<?php echo PHPFMG_EMAILS_LOGFILE?>) will be created automatically when the form is submitted. 
    However, the script doesn't have writable permission to create those files. In order to save your valuable information, please set the directory to writable.
     If you don't know how to do it, please ask for help from your web Administrator or Technical Support of your hosting company.   
</div>
<br><br>
<?php
}


function phpfmg_log_view(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    
    phpfmg_admin_header();
   
    $file = $files[$n];
    if( is_file($file) ){
        if( 1== $n ){
            echo "<pre>\n";
            echo join("",file($file) );
            echo "</pre>\n";
        }else{
            $man = new phpfmgDataManager();
            $man->displayRecords();
        };
     

    }else{
        echo "<b>No form data found.</b>";
    };
    phpfmg_admin_footer();
}


function phpfmg_log_download(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );

    $file = $files[$n];
    if( is_file($file) ){
        phpfmg_util_download( $file, PHPFMG_SAVE_FILE == $file ? 'form-data.csv' : 'email-traffics.txt', true, 1 ); // skip the first line
    }else{
        phpfmg_admin_header();
        echo "<b>No email traffic log found.</b>";
        phpfmg_admin_footer();
    };

}


function phpfmg_log_delete(){
    $n = isset($_REQUEST['file'])  ? $_REQUEST['file']  : '';
    $files = array(
        1 => PHPFMG_EMAILS_LOGFILE,
        2 => PHPFMG_SAVE_FILE,
    );
    phpfmg_admin_header();

    $file = $files[$n];
    if( is_file($file) ){
        echo unlink($file) ? "It has been deleted!" : "Failed to delete!" ;
    };
    phpfmg_admin_footer();
}


function phpfmg_util_download($file, $filename='', $toCSV = false, $skipN = 0 ){
    if (!is_file($file)) return false ;

    set_time_limit(0);


    $buffer = "";
    $i = 0 ;
    $fp = @fopen($file, 'rb');
    while( !feof($fp)) { 
        $i ++ ;
        $line = fgets($fp);
        if($i > $skipN){ // skip lines
            if( $toCSV ){ 
              $line = str_replace( chr(0x09), ',', $line );
              $buffer .= phpfmg_data2record( $line, false );
            }else{
                $buffer .= $line;
            };
        }; 
    }; 
    fclose ($fp);
  

    
    /*
        If the Content-Length is NOT THE SAME SIZE as the real conent output, Windows+IIS might be hung!!
    */
    $len = strlen($buffer);
    $filename = basename( '' == $filename ? $file : $filename );
    $file_extension = strtolower(substr(strrchr($filename,"."),1));

    switch( $file_extension ) {
        case "pdf": $ctype="application/pdf"; break;
        case "exe": $ctype="application/octet-stream"; break;
        case "zip": $ctype="application/zip"; break;
        case "doc": $ctype="application/msword"; break;
        case "xls": $ctype="application/vnd.ms-excel"; break;
        case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
        case "gif": $ctype="image/gif"; break;
        case "png": $ctype="image/png"; break;
        case "jpeg":
        case "jpg": $ctype="image/jpg"; break;
        case "mp3": $ctype="audio/mpeg"; break;
        case "wav": $ctype="audio/x-wav"; break;
        case "mpeg":
        case "mpg":
        case "mpe": $ctype="video/mpeg"; break;
        case "mov": $ctype="video/quicktime"; break;
        case "avi": $ctype="video/x-msvideo"; break;
        //The following are for extensions that shouldn't be downloaded (sensitive stuff, like php files)
        case "php":
        case "htm":
        case "html": 
                $ctype="text/plain"; break;
        default: 
            $ctype="application/x-download";
    }
                                            

    //Begin writing headers
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: public"); 
    header("Content-Description: File Transfer");
    //Use the switch-generated Content-Type
    header("Content-Type: $ctype");
    //Force the download
    header("Content-Disposition: attachment; filename=".$filename.";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$len);
    
    while (@ob_end_clean()); // no output buffering !
    flush();
    echo $buffer ;
    
    return true;
 
    
}
?>