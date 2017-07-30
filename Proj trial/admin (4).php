<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "okekeperdita@yahoo.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "efb231" );

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
			'138A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGVqRxVgdRFoZHR2mOiCJiTowNLo2BAQEoOhlAKpzdBBBct/KrFVhq0JXZk1Dch+aOpgY0LzA0BBMMTR1Ihh6RUNAbmZEERuo8KMixOI+AFmtyD7wWw4cAAAAAElFTkSuQmCC',
			'545B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYWllDHUMdkMQCGhimsjYwOgSgioWCxESQxAIDGF1Zp8LVgZ0UNm3p0qWZmaFZyO5rFWkFqkYxj6FVFGhnIIp5Aa1At6CJiUxhaGV0dETRyxrA0MoQyoji5oEKPypCLO4DAM0Wyv1hVEzuAAAAAElFTkSuQmCC',
			'421E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpI37pjCGAHFoALJYCGsrQwijA7I6xhCRRkc0MdYpDI0OU+BiYCdNm7Zq6appK0OzkNwXMIUBCFH1hoYyBKCLgfkYYqwNmGKioY5AiOLmgQo/6kEs7gMAMr/JIcff8AoAAAAASUVORK5CYII=',
			'ABFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA6YGIImxBoi0sjYwBIggiYlMEWl0BapmQRILaAWpY3RAdl/U0qlhS0NXZiG7D00dGIaGQsxjQDUPhx2obgloBbq5gQHFzQMVflSEWNwHAN3jy2MMmlIVAAAAAElFTkSuQmCC',
			'A993' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUIdkMRYA1hbGR0dHQKQxESmiDS6NgQ0iCCJBbRCxAKQ3Be1dOnSzMyopVlI7gtoZQx0CIGrA8PQUIZGBwzzWBodMcQw3QI0D8PNAxV+VIRY3AcAp63NpZ2xJV0AAAAASUVORK5CYII=',
			'E9B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGUIdkMQCGlhbWRsdHQJQxEQaXUEkulijQ0MAkvtCo5YuTQ1dtTQLyX0BDYyBSOqgYgxYzGPBIobpFmxuHqjwoyLE4j4A5+DPLGJp9aIAAAAASUVORK5CYII=',
			'DF6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUMDkMQCpog0MDo6OiCrC2gVaWBtwCbGCBMDOylq6dSwpVNXhmYhuQ+sDqt5gYTFsLglNECkgQHNzQMVflSEWNwHAHSey5dxABhWAAAAAElFTkSuQmCC',
			'7A32' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMZAhhDGaY6IIu2MoawNjoEBKCIsbYyNAQ6iCCLTRFpdGh0aBBBdl/UtJVZU4EUkvsYHcDqGpHtYG0QDXVoCGhFdotIA1BdQ8AUZLEAoJgr0C3oYo6hjKEhgyD8qAixuA8A167N23meYXMAAAAASUVORK5CYII=',
			'B456' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QKxKAMAwFX0U8Au4TBD6IGDwCThGTG7RHqOGUDK4FJAzkuZ18doLtUoY/5RU/FTgpJy6YRCQyiJTMoWSBm6ovDJQCl3465ZyXZV4LP4mtw8bTvk7ZRm7rG05nFuGh52r2cIaicv7qfw/mxm8HxbTMu5byUSAAAAAASUVORK5CYII=',
			'C92C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGaYGIImJtLK2Mjo6BIggiQU0ijS6NgQ6sCCLNYg0OgDFkN0XtWrp0qyVmVnI7gtoYAx0aGV0YEDRy9DoMAVNrJGl0SGAEcUOsFscGFDcAnIza2gAipsHKvyoCLG4DwAzo8tOQdOaMwAAAABJRU5ErkJggg==',
			'ECE5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDHUMDkMQCGlgbXRsYHRhQxEQasImxNjC6OiC5LzRq2qqloSujopDcB1HHACTR9WKKgexAFQO5hSEA2X0QNztMdRgE4UdFiMV9AJD3zKZvpFMmAAAAAElFTkSuQmCC',
			'7323' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkNZQxhCGUIdkEVbRVoZHR0dAlDEGBpdGwIaRJDFpgBFgWIByO6LWhW2amXW0iwk9zE6ANWBVSL0sjYwNDpMYUAxTwQkFoAqBrSxldGBEcUtAQ2sIayhAahuHqDwoyLE4j4A6EDMBrEhbQEAAAAASUVORK5CYII=',
			'0D56' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaY6IImxBoi0sjYwBAQgiYlMEWl0BaoWQBILaAWKTWV0QHZf1NJpK1MzM1OzkNwHUufQEIhiHlTMQQTDDlQxkFsYHR1Q9ILczBDKgOLmgQo/KkIs7gMA7IjMCMsUsSkAAAAASUVORK5CYII=',
			'7864' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGRoCkEVbWVsZHR0aUcVEGl0bHFpRxKawtrICyQBk90WtDFs6dVVUFJL7GB2A6hwdHZD1sjaAzAsMDUESEwGLBaC4JaAB7BY0MSxuHqDwoyLE4j4Aya3Npqfx7GgAAAAASUVORK5CYII=',
			'5342' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkNYQxgaHaY6IIkFNIi0MrQ6BASgiIFUOTqIIIkFBjC0MgQ6NIgguS9s2qqwlZlZq6KQ3Qc0jbXRoRHZDqBYo2toQCuyW4A8kKopyGIiU0RAogHIYqwBIDc7hoYMgvCjIsTiPgAGsc1/COApHQAAAABJRU5ErkJggg==',
			'56D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGVqRxQIaWFtZGx2mOqCIiTSyNgQEBCCJBQaINLA2BDqIILkvbNq0sKWrIrOmIbuvVbQVSR1UTKTRFU0sACyGaofIFEy3sAZgunmgwo+KEIv7AGEpzRhYHZJ8AAAAAElFTkSuQmCC',
			'7984' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGRoCkEVbWVsZHR0aUcVEGl0bAlpRxKaINDo6OkwJQHZf1NKlWaGroqKQ3MfowBjoCFSIrJe1gQFoXmBoCJKYSAMLyA4UtwQ0gN2CJobFzQMUflSEWNwHAOJpza4lIlFCAAAAAElFTkSuQmCC',
			'C939' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WEMYQxhDGaY6IImJtLK2sjY6BAQgiQU0ijQ6NAQ6iCCLNQDFGh1hYmAnRa1aujRr6qqoMCT3BTQwBjo0OkxF1csANA9oAoodLCAxFDuwuQWbmwcq/KgIsbgPAAsZzbek6M+EAAAAAElFTkSuQmCC',
			'6A5B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjCGsDYwOgQgiQW0sLaCxESQxRpEGl2nwtWBnRQZNW1lamZmaBaS+0KmiDQ6NASimtcqGgoSQzGvFWgempgIUK+joyOKXtYAoHmhjChuHqjwoyLE4j4ABgXMXpDoSRUAAAAASUVORK5CYII=',
			'E603' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMIQ6IIkFNLC2MoQyOgSgiIk0Mjo6NIigijWwAskAJPeFRk0LW7oqamkWkvsCGkRbkdTBzXMFm4Aq5ohhB6ZbsLl5oMKPihCL+wAS+M3I6Y0H3QAAAABJRU5ErkJggg==',
			'9CAD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQxmmMIY6IImJTGFtdAhldAhAEgtoFWlwdHR0EEETY20IhImBnTRt6rRVS1dFZk1Dch+rK4o6CATpDUUVEwCKuaKpA7kFJIbsFpCbgeahuHmgwo+KEIv7AAtyzDd/wiZUAAAAAElFTkSuQmCC',
			'3B88' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVElEQVR4nGNYhQEaGAYTpIn7RANEQxhCGaY6IIkFTBFpZXR0CAhAVtkq0ujaEOgggiyGqg7spJVRU8NWha6amoXsPmLNwyKGzS3Y3DxQ4UdFiMV9ACzDzDm0jJrUAAAAAElFTkSuQmCC',
			'2E9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaYGIImJTBFpYHR0CBBBEgtoFWlgbQh0YEHWDRVDcd+0qWErMyOzUNwHMikErg4MGR1ApqOKsTYA7UWzQ6QB0y2hoZhuHqjwoyLE4j4AcHzJ/EC00g0AAAAASUVORK5CYII=',
			'8A1E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIYGIImJTGEMYQhhdEBWF9DK2sqIJiYyRaTRYQpcDOykpVHTVmZNWxmaheQ+NHVQ80RDMcUw1WHTyxog0ugY6oji5oEKPypCLO4DAKBByoZFv40MAAAAAElFTkSuQmCC',
			'C165' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WEMYAhhCGUMDkMREWhkDGB0dHZDVBTSyBrA2oIk1MADFGF0dkNwXBURLp66MikJyH1ido0ODCIbeAFSxRpBYoIMIilsYgG5xCEB2H2sIayhDKMNUh0EQflSEWNwHAJtLyYiomEhzAAAAAElFTkSuQmCC',
			'2A85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAd0lEQVR4nM2Quw2AMAwFnSIbmH3sIr2RcJMRmMIpsgGwQ5iST2UEJUj4dSc/+WRYb2Pwp3ziFwUENKg4hlMYAjP5PamxRusvDCoWZk7k/Zaljdpy9n5y7JGh6wbqNJlcWDQsab/hGdrZFe+nioUUZvrB/17Mg98GlWbLZAeXcVQAAAAASUVORK5CYII=',
			'3C27' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYQxlCGUNDkMQCprA2Ojo6NIggq2wVaXBtCEAVmwLiBQAhwn0ro6YBiayVWcjuA6lrBUI08ximACGamEMAQwADulscGB3Q3cwaGogiNlDhR0WIxX0A5AfLoCyC0VIAAAAASUVORK5CYII=',
			'EFF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA6Y6IIkFNIg0sDYwBARgiDE6iGCqaxBBcl9o1NSwpaGrVkUhuQ+qrhGLHa0MmGJTsIgFoLoZ7JbQkEEQflSEWNwHADEPzN1UUFHiAAAAAElFTkSuQmCC',
			'B930' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGVqRxQKmsLayNjpMdUAWaxVpdGgICAhAUQcUa3R0EEFyX2jU0qVZU1dmTUNyX8AUxkAkdVDzGIDmBaKJsWCxA9Mt2Nw8UOFHRYjFfQCP7M7i22iYqQAAAABJRU5ErkJggg==',
			'AF85' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUMDkMRYA0QaGB0dHZDViUwRaWBtCEQRC2gFq3N1QHJf1NKpYatCV0ZFIbkPos6hQQRJb2goyLwAFDGQOpAd6GJAvQEBaGIMoQxTHQZB+FERYnEfAAyOy71piks4AAAAAElFTkSuQmCC',
			'16E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHUIdkMRYHVhbWYEyAUhiog4ijaxAWgRFr0gDSCwAyX0rs6aFLQ1dtTQLyX2MDqKtSOpgehtdMc3DIobFLSGYbh6o8KMixOI+AEqpyTAoE9UmAAAAAElFTkSuQmCC',
			'A1E3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDHUIdkMRYAxgDWIEyAUhiIlNYgWJAGkksoJUBLBaA5L6opUAUumppFpL70NSBYWgoA07zMMVQ3RLQyhqK7uaBCj8qQizuAwCjMMpxT6tKeAAAAABJRU5ErkJggg==',
			'B16A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMAYwOjpMdUAWa2UNYG1wCAhAUccAFGN0EEFyX2jUqqilU1dmTUNyH1idoyNMHdQ8kN7A0BBMMVR1QL2MaHpDA1hDGUIZUcQGKvyoCLG4DwBUBsqPDtUhFgAAAABJRU5ErkJggg==',
			'E19A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGVqRxQIaGAMYHR2mOqCIsQawNgQEBKCIMQDFAh1EkNwXGrUqamVmZNY0JPeB1DGEwNUhxBoCQ0PQxBgbMNUxOjqiiIWGsIYyhDKiiA1U+FERYnEfAOlTyjXA3crDAAAAAElFTkSuQmCC',
			'FB28' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQxhCGaY6IIkFNIi0Mjo6BASgijW6NgQ6iKCpA5IwdWAnhUZNDVu1MmtqFpL7wOpaGTDMc5jCiG5eo0MAhlgrowO6XtEQ1tAAFDcPVPhREWJxHwDPAs15TVT7ywAAAABJRU5ErkJggg==',
			'6B79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6Y6IImJTBFpZWgICAhAEgtoEWl0aAh0EEEWawCqa3SEiYGdFBk1NWzV0lVRYUjuCwGZN4VhKoreVqB5AUC70MQcHRhQ7AC5hbWBAcUtYDc3MKC4eaDCj4oQi/sAL+XM1lslKLoAAAAASUVORK5CYII=',
			'877C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQ11DA6YGIImJTGFodGgICBBBEgtoBYkFOrCgqgOKOjogu29p1Kppq5auzEJ2H1BdAMMURgcGFPOA/AB0MdYGRgdGNDtEGlgbGFDcwhoAFkNx80CFHxUhFvcBAKvIy2csmG+pAAAAAElFTkSuQmCC',
			'FC0F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMIaGIIkFNLA2OoQyOjCgiIk0ODo6YoixNgTCxMBOCo2atmrpqsjQLCT3oanDK4ZpBza3gN2MIjZQ4UdFiMV9AFkZy26hCm/EAAAAAElFTkSuQmCC',
			'0FE2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB1EQ11DHaY6IImxBog0sDYwBAQgiYlMAYkxOoggiQW0gtU1iCC5L2rp1LCloUAayX1QdY0OmHpbGTDsYJjCgMUtqG4GioU6hoYMgvCjIsTiPgABUssrIcshdAAAAABJRU5ErkJggg==',
			'4758' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37poiGuoY6THVAFgthaHRtYAgIQBJjBIsxOoggibFOYWhlnQpXB3bStGmrpi3NzJqaheS+gCkMAUASxbzQUEYHhoZAFPMYprA2sGKIiTQwOjqg6AWJMYQyoLp5oMKPehCL+wDqy8vwRgOXhAAAAABJRU5ErkJggg==',
			'93CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WANYQxhCHaYGIImJTBFpZXQICBBBEgtoZWh0bRB0YEEVa2VtYHRAdt+0qavClq5amYXsPlZXFHUQCDYPVUwAix3Y3ILNzQMVflSEWNwHAFLAyp/BItVWAAAAAElFTkSuQmCC',
			'A841' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHVqRxVgDWFsZWh2mIouJTBFpdJjqEIosFtAKVBcI1wt2UtTSlWErM7OWIrsPpI4VzY7QUJFG19CAVlTzgHagqQPbgSEGdnNowCAIPypCLO4DAK4MzcJl6v9eAAAAAElFTkSuQmCC',
			'5772' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QMQ6AMAhF6dAb4H26uNOkOHgaHLhB7Q1cOKXtRlJHTeRvL5/wAtg0An/KJ35cFl6ZzuQYCRxJiGhiOaFjmUAHRee3NWt2me3eT4Ggjqa7rCHB2Pc3NEqn1TOsKFF607FIgwUuP/jfi3nwuwFmyMyVWCieHwAAAABJRU5ErkJggg==',
			'FF9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGVqRxQIaRBoYHR2mOqCJsTYEBARgiAU6iCC5LzRqatjKzMisaUjuA6ljCIGrQ4g1BIaGoNvbgKmO0dERU28oI4rYQIUfFSEW9wEA/NvMrtYr8/EAAAAASUVORK5CYII=',
			'7152' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMZAlhDHaY6IIu2MgawNjAEBKCIsQLFGB1EkMWmAPVOZWgQQXZf1KqopZlZQArhPkYHhgCGhoBGZDtA5gPFWpHdAjQHaEfAFGQxoBsCGB0dAlDFWEMZQhlDQwZB+FERYnEfAPvgya2PcZ8SAAAAAElFTkSuQmCC',
			'95B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoBYo1BDoIoIqFsDY6OiC7b9rUqUuXhq5MzUJyH6srQ6NroyOKeQytQDGgeSJIYgKtIhhiIlNYW9HdwhrAGILu5oEKPypCLO4DAHgtzGzrAVZQAAAAAElFTkSuQmCC',
			'D75F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QgNEQ11DHUNDkMQCpjA0ujYwOiCrC2jFKtbKOhUuBnZS1NJV05ZmZoZmIbkPqC6AoSEQTS9IH7oYawMrutgUkQZGR0cUsdAAkQaGUFS3DFT4URFicR8AO8nLJnz6qJAAAAAASUVORK5CYII=',
			'B651' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDHVqRxQKmsLayNjBMRRFrFWkEioWiqhNpYJ3KANMLdlJo1LSwpZlZS5HdFzBFtBVkArp5DljEXNHFgG5hdER1H8jNQJeEBgyC8KMixOI+AJAGzWj3hdVqAAAAAElFTkSuQmCC',
			'3885' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUMDkMQCprC2Mjo6OqCobBVpdG0IRBWDqHN1QHLfyqiVYatCV0ZFIbsPrM6hQQTDvAAsYoEOIhhucQhAdh/EzQxTHQZB+FERYnEfAIU7yxRoz4wKAAAAAElFTkSuQmCC',
			'62A5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2Quw3AIAxETeENyD6moDcSFMk0RgobkBFomDKUzqdMJHySi9Od9HTQHycwk37hQzYRqkmsPFuxQDKkc7zb7Jy7egLZS/Ck+Nattzb+pvhihYojbXW3AGO6e4ZQAtkri4wuaz7kJXnhgybY70O98J0fPMyA8X95XAAAAABJRU5ErkJggg==',
			'0C83' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7GB0YQxmA0AFJjDWAtdHR0dEhAElMZIpIg2tDQIMIklhAq0gDo6NDQwCS+6KWTlu1KnTV0iwk96Gpg4uxopmHzQ5sbsHm5oEKPypCLO4DAMQ3zLI+mGY6AAAAAElFTkSuQmCC',
			'B51A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQxmmMLQiiwVMEWlgCGGY6oAs1irSwBjCEBCAqi6EYQqjgwiS+0Kjpi5dNW1l1jQk9wVMYWh0QKiDmgcWCw1BtQNT3RTWVgY0sdAAxhDGUEcUsYEKPypCLO4DANpOzKo43AAZAAAAAElFTkSuQmCC',
			'1EB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDGVqRxVgdRBpYGx2mIouJgsQaAkJR9YLVwfSCnbQya2rY0tBVS5Hdh6YOIdYQQJwYml7RELCbQwMGQfhREWJxHwDSQsmdrEY6mwAAAABJRU5ErkJggg==',
			'0AA7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YAhimMIaGIImxBjCGMIQyNIggiYlMYW1ldHRAEQtoFWl0bQgAQoT7opZOW5m6KmplFpL7oOpaGVD0ioa6hgZMYUCxA6wugAHFLSCxQAdUN2OKDVT4URFicR8AlnnMzY4YKSoAAAAASUVORK5CYII=',
			'0425' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM3QsRHAIAgFUCjcwOyDhT0WNG6QLWzYQEewiFPGdHhJmdwFun9wvAPGrQr8qT/xIYGCoLDJHEPDEMjO+QriSloyVoxQUiTjy733cew5Gx+r13ml+GV3E6prNm8oMJJfLTqNbH2X2Qk3+sH/XuwH3wk1HMn26W/dawAAAABJRU5ErkJggg==',
			'3BCB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7RANEQxhCHUMdkMQCpoi0MjoEOgQgq2wVaXRtEHQQQRYDqmNtYISpAztpZdTUsKWrVoZmIbsPVR2SeYyo5mGxA5tbsLl5oMKPihCL+wAsf8tifgfGMgAAAABJRU5ErkJggg==',
			'6809' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDKEBCAJBbQItLo6OjoIIIs1sDaytoQCBMDOykyamXY0lVRUWFI7guZAlIXMBVFb6tIoyvQBHQxoBUodmBzCzY3D1T4URFicR8AOHnMUUwk6QYAAAAASUVORK5CYII=',
			'07CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB1EQx1CHUMdkMRYAxgaHR0CHQKQxESmMDS6Ngg6iCCJBbQytLICTQhAcl/U0lXTlq5aGZqF5D6gugAkdVAxRgeQmAiKHawNrGh2sAaIAFWhugWsC83NAxV+VIRY3AcAZ3DKn0cjWAgAAAAASUVORK5CYII=',
			'0F63' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGUIdkMRYA0QaGB0dHQKQxESmiDSwNjg0iCCJBbSCxIA0kvuilk4NWzp11dIsJPeB1Tk6NARg6A1AMQ9iB6oYNrcwOgBVoLl5oMKPihCL+wA2P8w99TAK1AAAAABJRU5ErkJggg==',
			'E5AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNEQxmmMIY6IIkFNIg0MIQyOgSgiTE6OjqIoIqFsDYEwsTATgqNmrp06arIrGlI7gOa0+iKUIcQC0UXE8GijrUVZAeyW0JDGEH2orh5oMKPihCL+wAxgc1D/WC+2gAAAABJRU5ErkJggg==',
			'647B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYWllDA0MdkMREpjBMZWgIdAhAEgtoYQgFiYkgizUwujI0OsLUgZ0UGbV06aqlK0OzkNwXMkWklWEKI6p5raKhDgGMqOa1MrQyOqCKAd3SytqAqhfs5gZGFDcPVPhREWJxHwBlaMtfuN7N6QAAAABJRU5ErkJggg==',
			'5591' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQxlCGVqRxQIaRBoYHR2moouxNgSEIosFBoiEAMVgesFOCps2denKzKilKO5rZWh0CAlAsQMs1oAqFtAq0uiIJiYyhbUV6BYUMdYAxhCgm0MDBkH4URFicR8AhnPMbAEWHsQAAAAASUVORK5CYII=',
			'866A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGVqRxUSmsLYyOjpMdUASC2gVaWRtcAgIQFEn0sDawOggguS+pVHTwpZOXZk1Dcl9IlNEW1kdHWHq4Oa5NgSGhmCKoaiDuAVVL8TNjChiAxV+VIRY3AcAaZzLghgacEUAAAAASUVORK5CYII=',
			'AA37' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YAhhDGUNDkMRYAxhDWBsdGkSQxESmsLYyNASgiAW0ijQ6ANUFILkvaum0lVlTV63MQnIfVF0rsr2hoaKhQJ1TGNDNawgIQBdzbXR0QBdzDGVEERuo8KMixOI+ABiezg+Hsdh2AAAAAElFTkSuQmCC',
			'B735' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QMQ6AMAhF6eAN6n3o0J0OLD0NHbxB6x3sKcWNqqMm5ScML0BegP4ogZnyix/TyoEdk2FUocQS0M7RBgUljayC0hDR+HHue29HzsZP5+ja9sM9p7foxhbtCQdWvSwFyfoxeXEMDSf434d58TsBSuTN6HahVOwAAAAASUVORK5CYII=',
			'1394' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGRoCkMRYHURaGR0dGpHFRB0YGl0bAloDUPQytLI2BEwJQHLfyqxVYSszo6KikNwHUscQEuiAprfRoSEwNARNzBHoElR1YLegiImGYLp5oMKPihCL+wBl0sraIBJykgAAAABJRU5ErkJggg==',
			'57E9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNEQ11DHaY6IIkFNDA0ujYwBARgiDE6iCCJBQYwtLIixMBOCpu2atrS0FVRYcjua2UIYG1gmIqsl6GV0QEo1oAsFgA0DSiGYofIFBGQGIpbWAOAYmhuHqjwoyLE4j4AmHPLfGhT9RQAAAAASUVORK5CYII=',
			'2F65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AMAwE7SIbmH2cgt4Upsg0btiAsAEFTElC5QhKkOLvTv/SyXA+zqCn/OIXZFBWVHGMVjKMkX1PFrJgLYOb4cjeb8vzno+UvJ+UXmQjt0WuW2lYsMom9oysurB4P9XSUMjcwf8+zIvfBTmsysrEdM0vAAAAAElFTkSuQmCC',
			'3E09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RANEQxmmMEx1QBILmCLSwBDKEBCArLJVpIHR0dFBBFkMqI61IRAmBnbSyqipYUtXRUWFIbsPrC5gqgiaeUCxBnQxRkcHFDuwuQWbmwcq/KgIsbgPAOQFyzX4M0fmAAAAAElFTkSuQmCC',
			'6D0E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WANEQximMIYGIImJTBFpZQhldEBWF9Ai0ujo6Igq1iDS6NoQCBMDOykyatrK1FWRoVlI7guZgqIOorcVuxi6Hdjcgs3NAxV+VIRY3AcA39LLKXCTJskAAAAASUVORK5CYII=',
			'06DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGUNDkMRYA1hbWRsdHZDViUwRaWRtCEQRC2gVaUASAzspaum0sKWrIkOzkNwX0CraikVvoyuaGMgOdDFsboG6GUVsoMKPihCL+wBUfsnefzEZSwAAAABJRU5ErkJggg==',
			'88CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHUNDkMREprC2MjoEOiCrC2gVaXRtEEQRA6ljbWCEiYGdtDRqZdjSVStDs5Dch6YOyTxsYph2oLsF6mYUsYEKPypCLO4DAFf2ydFxWjYDAAAAAElFTkSuQmCC',
			'8608' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQximMEx1QBITmcLayhDKEBCAJBbQKtLI6OjoIIKiTqSBtSEApg7spKVR08KWroqamoXkPpEpoq1I6uDmuTYEopgHEnPEsAPTLdjcPFDhR0WIxX0APPnMUFlfPX0AAAAASUVORK5CYII=',
			'A8A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjDWBtZQhlCAhAEhOZItLo6OjoIIAkFtDK2sraEOiA7L6opSvDlq6KTM1Cch9UHYp5oaEija6hgQ4iKOYBxRrQxUB6A1D0BrQyhgDFUNw8UOFHRYjFfQCt9c0nykfl0gAAAABJRU5ErkJggg==',
			'54DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYWllDGUMdkMQCGhimsjY6OgSgioWyNgQ6iCCJBQYwuiKJgZ0UNm3p0qWrIrOmIbuvVaQVXS9Dq2ioK5pYQCsDhjqRKUAxNLewBmC6eaDCj4oQi/sA2DvL1pCuOPgAAAAASUVORK5CYII=',
			'94DF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDGUNDkMREpjBMZW10dEBWF9DKEMraEIgmxuiKJAZ20rSpS5cuXRUZmoXkPlZXkVZ0vQytoqGuaGICrQwY6oBuaUV3C9TNqOYNUPhREWJxHwBozMnR8FM7FgAAAABJRU5ErkJggg==',
			'955B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHUMdkMREpog0sDYwOgQgiQW0QsREUMVCWKfC1YGdNG3q1KVLMzNDs5Dcx+rK0OjQEIhiHkMrRAzZPIFWkUZXNDGRKaytjI6OKHpZAxhDGEIZUdw8UOFHRYjFfQDdP8sXdTpX2AAAAABJRU5ErkJggg==',
			'B634' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGRoCkMQCprC2sjY6NKKItYo0gkhUdSINDI0OUwKQ3BcaNS1s1dRVUVFI7guYItrK0OjogG6eQ0NgaAiGWAA2t6CIYXPzQIUfFSEW9wEAGmPQNmGm4ZIAAAAASUVORK5CYII=',
			'7D13' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNFQximMIQ6IIu2irQyhDA6BKCKNTqGMDSIIItNEWl0mMLQEIDsvqhpK7OmrVqaheQ+RgcUdWDI2gARQzZPBItYQAPQLVNQ3RLQIBrCGOqA6uYBCj8qQizuAwAOfc0VmhVLtwAAAABJRU5ErkJggg==',
			'7BFA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDA1pRRFtFWlkbGKY6oIo1ujYwBAQgi00BqWN0EEF2X9TUsKWhK7OmIbkPqAJZHRiyNoDMYwwNQRITgYihqAtowNQb0AB0M5rYQIUfFSEW9wEAwizK85TFCiAAAAAASUVORK5CYII=',
			'0770' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA1qRxVgDGBodGgKmOiCJiUwBiwUEIIkBdbUyNDo6iCC5L2rpqmmrlq7MmobkPqC6AIYpjDB1UDFGB4YAVDGRKawNINEAFLeINLA2MKC4BaQLKIbi5oEKPypCLO4DAOXny5nrHBdiAAAAAElFTkSuQmCC',
			'A1C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHVqRxVgDGAMYHQKmOiCJiUxhDWBtEAgIQBILaGUAijE6iCC5L2opCK3MmobkPjR1YBgaiikGUYdpB7pbAlpZQ9HdPFDhR0WIxX0AUpbKL4ONQxkAAAAASUVORK5CYII=',
			'0858' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHaY6IImxBrC2sjYwBAQgiYlMEWl0BaoWQRILaAWqmwpXB3ZS1NKVYUszs6ZmIbkPpA5IopgX0CrS6NAQiGIexA5UMZBbGB0dUPSC3MwQyoDi5oEKPypCLO4DAJZPy7+zs8QkAAAAAElFTkSuQmCC',
			'4C23' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37pjCGMgChA7JYCGujo6OjQwCSGGOISINrQ0CDCJIY6xQQL6AhAMl906ZNW7VqZdbSLCT3BYDUtTI0IJsXGgoUm8KAYh4DUJ1DALoY0C0OjChuAbmZNTQA1c0DFX7Ug1jcBwDNIMy3AIpemQAAAABJRU5ErkJggg==',
			'45E4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37poiGsoY6NAQgi4WINLA2MDQiizFCxFqRxViniIQAxaYEILlv2rSpS5eGroqKQnJfwBSGRtcGRgdkvaGhYLHQEBS3iADFGFDdMoW1lRVDjDEEw80DFX7Ug1jcBwBCU801YVaVbQAAAABJRU5ErkJggg==',
			'324F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxgaHUNDkMQCprC2MrQ6OqCobBVpdJiKJjaFodEhEC4GdtLKqFVLV2ZmhmYhu28KwxTWRnTzGAJYQwPRxBgdGNDUAd3SgC4mGiAa6oBu3gCFHxUhFvcBAPK+yjrBBbpXAAAAAElFTkSuQmCC',
			'C5CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WENEQxlCHUNDkMREWkUaGB0CHZDVBTSKNLA2CKKKNYiEsAJVIrsvatXUpUtXrQzNQnJfQANDoytCHW6xRhGgGKodIq2srehuYQ1hDAG6GUVsoMKPihCL+wC3sMod4MdQAAAAAABJRU5ErkJggg==',
			'D10B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QgMYAhimMIY6IIkFTGEMYAhldAhAFmtlDWB0dHQQQRFjCGBtCISpAzspaikIRYZmIbkPTR2KGLp5GHZMYcBwS2gAayi6mwcq/KgIsbgPAC3jypSrPOraAAAAAElFTkSuQmCC',
			'2E29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGaY6IImJTBFpYHR0CAhAEgtoFWlgbQh0EEHW3QriwcUgbpo2NWzVyqyoMGT3BQBVtDJMRdbLCNI1BWgXsltAvAAGFDtEgJDRgQHFLaGhoqGsoQEobh6o8KMixOI+APhHymOGNbu/AAAAAElFTkSuQmCC',
			'D496' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QgMYWhlCGaY6IIkFTGGYyujoEBCALAZUxdoQ6CCAIsboChJDdl/U0qVLV2ZGpmYhuS+gVaSVISQQzTzRUAegXhFUO1oZ0cWmAMXQ3ILNzQMVflSEWNwHAA5wzPQ+zYSXAAAAAElFTkSuQmCC',
			'FFEB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAASklEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHUMdkMQCGkQaWBsYHQKwiIngVgd2UmjU1LCloStDs5DcR6p5eOxAiKG5eaDCj4oQi/sA8ezMAYCGtkcAAAAASUVORK5CYII=',
			'41B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjAEsIYyhDogi4UwBrA2OjoEIIkxhrAGsDYENIggibGC9DY6NAQguW/atFVRS0NXLc1Ccl8AqjowDA1lwDAP7BZsYmhuYZjCGorh5oEKP+pBLO4DALDTyyEpX6GRAAAAAElFTkSuQmCC',
			'7DED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHUMdkEVbRVpZGxgdAlDFGl2BYiLIYlNQxCBuipq2MjV0ZdY0JPcBVWDoZW3AFBPBIhbQgOmWgAYsbh6g8KMixOI+AMyzyy7kz6fIAAAAAElFTkSuQmCC',
			'4908' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpI37pjCGMExhmOqALBbC2soQyhAQgCTGGCLS6Ojo6CCCJMY6RaTRtSEApg7spGnTli5NXRU1NQvJfQFTGAOR1IFhaCgDUG8ginkMU1gw7GCYgukWrG4eqPCjHsTiPgAvV8xHtJMXPgAAAABJRU5ErkJggg==',
			'E550' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHVqRxQIaRBpYGximOmCKBQSgioWwTmV0EEFyX2jU1KVLMzOzpiG5D6in0aEhEKYOj5hIo2tDAJodrK2Mjg4obgkNYQxhCGVAcfNAhR8VIRb3AQCTXc1aVIVpswAAAABJRU5ErkJggg==',
			'CC04' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WEMYQxmmMDQEIImJtLI2OoQyNCKLBTSKNDg6OrSiiDWINLA2BEwJQHJf1Kppq5auioqKQnIfRF2gA6bewNAQTDuwuQVFDJubByr8qAixuA8AeyXO2JqzrxcAAAAASUVORK5CYII=',
			'A43C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YWhlDGaYGIImxBjBMZW10CBBBEhOZwhDK0BDowIIkFtDK6MrQ6OiA7L6opUuXrpq6MgvZfQGtIq1I6sAwNFQ01AFoHgOKeQytmHYwtKK7BSSG7uaBCj8qQizuAwDG6swx1i4n7AAAAABJRU5ErkJggg==',
			'7B79' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nM2Quw2AMAwFTZENwj5mAyORFJnmUWSDZASaTIklhOQIShD4dSd/TqZ2KdCf8opfCOPiglS2NPtMEJGerYyZvWVF+9bpZIdTqrFtLUXjN7D2Fap21kH3CcEyr2xi6m4IfHagzkWgzqDe+aP/PZgbvx1Qx8w53NwU7QAAAABJRU5ErkJggg==',
			'006B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGUMdkMRYAxhDGB0dHQKQxESmsLayNjg6iCCJBbSKNLoCTQhAcl/U0mkrU6euDM1Cch9YHZp5EL2BKOZB7EAVw+YWbG4eqPCjIsTiPgDTv8pasloS+gAAAABJRU5ErkJggg==',
			'DCBB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGUMdkMQCprA2ujY6OgQgi7WKNLg2BDqIoImxItSBnRS1dNqqpaErQ7OQ3IemDiGGxTwMO7C4BZubByr8qAixuA8AEqjOfQxdKPUAAAAASUVORK5CYII='        
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