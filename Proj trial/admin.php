<?php
require_once( dirname(__FILE__).'/form.lib.php' );

define( 'PHPFMG_USER', "okekeperdita@yahoo.com" ); // must be a email address. for sending password to you.
define( 'PHPFMG_PW', "ec7271" );

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
			'217B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0MdkMREpjAGMDQEOgQgiQW0soLFRJB1tzIEMDQ6wtRB3DRtVdSqpStDs5DdB7SDYQojinmMDiBRRhTzWBtAIqhiQHYAawOq3tBQ1lCgGIqbByr8qAixuA8A25rIeiB05xkAAAAASUVORK5CYII=',
			'1210' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YQximMLQii7E6sLYyhDBMdUASE3UQaXQMYQgIQNHL0OgwhdFBBMl9K7NWLV01bWXWNCT3AdVNYUCog4kFYIqBVaLZwdoAFEN1S4hoqGOoA4qbByr8qAixuA8Ajg7IkmIGWN0AAAAASUVORK5CYII=',
			'D59F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQxlCGUNDkMQCpog0MDo6OiCrC2gVaWBtCEQXC0ESAzspaunUpSszI0OzkNwX0MrQ6BCCrhcohmleoyO62BTWVnS3hAYwhgDdjCI2UOFHRYjFfQBUE8toA8sbFAAAAABJRU5ErkJggg==',
			'858E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMDkMREpog0MDo6OiCrC2gVaWBtCEQRA6oLQVIHdtLSqKlLV4WuDM1Ccp/IFIZGRwzzGBpd0cwD2oEhJjKFtRXdLawBjCHobh6o8KMixOI+AI0hyigRhWwuAAAAAElFTkSuQmCC',
			'517E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA0MDkMQCGhgDGBoCHRhQxFgxxAIDGAIYGh1hYmAnhU1bFbVq6crQLGT3tQLVTWFE0QsWC0AVC2gFiaCKiUwBuq8BVYwV6GKgGIqbByr8qAixuA8AS/jH5c/8g00AAAAASUVORK5CYII=',
			'FCAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZQxmmMIY6IIkFNLA2OoQyOgSgiIk0ODo6OoigibE2BMLUgZ0UGjVt1dJVkaFZSO5DU4cQCw3EMM+1AV2MtdEVQy9jKNA8FDcPVPhREWJxHwA6VM4UdC+/DQAAAABJRU5ErkJggg==',
			'5FFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA6YGIIkFNIg0sDYwBIhgiDE6sCCJBQZAxJDdFzZtatjS0JVZKO5rRVGHUyygFdMOkSmYbmEF28uA4uaBCj8qQizuAwB4L8qaFhbz0AAAAABJRU5ErkJggg==',
			'E70A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMLQiiwU0MDQ6hDJMdUATc3R0CAhAFWtlbQh0EEFyX2jUqmlLV0VmTUNyH1BdAJI6qBijA1AsNARFjLWBEWgJqjogL5QRRSw0BMibgio2UOFHRYjFfQDnXsx1aTlEpwAAAABJRU5ErkJggg==',
			'E7B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkNEQ11DGUIdkMQCGhgaXRsdHQLQxYCkCKpYK2ujQ0MAkvtCo1ZNWxq6amkWkvuA8gFI6qBijA6sGOaxNmCKiTSworklNAQohubmgQo/KkIs7gMAnQbO0V4Pt3kAAAAASUVORK5CYII=',
			'90A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMLQii4lMYQxhCGWY6oAkFtDK2sro6BAQgCIm0ujaEOggguS+aVOnrUxdFZk1Dcl9rK4o6iAQpDcUVUwAaAdrQwCKHSC3AMVQ3AJyM1AMxc0DFX5UhFjcBwD91cwzTmyYWwAAAABJRU5ErkJggg==',
			'8648' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoFWlkmOroIIKiDsgLhKsDO2lp1LSwlZlZU7OQ3CcyRbSVtRHTPNfQQBTzQGIOjeh2AN2Cphebmwcq/KgIsbgPAKAezW0auETqAAAAAElFTkSuQmCC',
			'70B8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZAlhDGaY6IIu2MoawNjoEBKCIsbayNgQ6iCCLTRFpdEWog7gpatrK1NBVU7OQ3MfogKIODFkbgGJo5ok0YNoR0IDpFiAb080DFH5UhFjcBwBx2MyEV4JDvgAAAABJRU5ErkJggg==',
			'C92C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGaYGIImJtLK2Mjo6BIggiQU0ijS6NgQ6sCCLNYg0OgDFkN0XtWrp0qyVmVnI7gtoYAx0aGV0YEDRy9DoMAVNrJGl0SGAEcUOsFscGFDcAnIza2gAipsHKvyoCLG4DwAzo8tOQdOaMwAAAABJRU5ErkJggg==',
			'D059' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDHaY6IIkFTGEMYW1gCAhAFmtlbWVtYHQQQRETaXSdChcDOylq6bSVqZlZUWFI7gOpc2gImIquFyjWIIJhRwCqHUC3MDo6oLgF5GaGUAYUNw9U+FERYnEfAGnIzTjo9jANAAAAAElFTkSuQmCC',
			'2852' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nM2QsRGAMAhFQ8EGGSgW9hTBwg10ClKwgXEDCzOl2OFpqXdCxb/P5x2h3UrCn/oTPiTIyKkmp8UFFSUQOY00ll4gRb+t5qvm93zrPmzT3EbPR6iWUPwNS7KZ9MIi5w1avBYFFbpEXmOGHBg4/+B/L/YD3wGs9cu+K6hi9QAAAABJRU5ErkJggg==',
			'2233' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUIdkMREprC2sjY6OgQgiQW0ijQ6NAQ0iCDrbmVodACLIrlv2qqlq6auWpqF7L4AhikMCHVgyOgAFEUzjxUkiiYmAhRFd0toqGioI5qbByr8qAixuA8AXtbNGNDfUH0AAAAASUVORK5CYII=',
			'D734' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1DGRoCkMQCpjA0ujY6NKKItTI0OgBJNDGQ6JQAJPdFLV01bdXUVVFRSO4DqgtgaHR0QNXL6MDQEBgagiLGCiLR3CLSwAqyGcXNIg2MaG4eqPCjIsTiPgC7NNCA00/5hwAAAABJRU5ErkJggg==',
			'59E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHaY6IIkFNLC2sjYwBASgiIk0ujYwOoggiQUGgMTg6sBOCpu2dGlq6KqpWcjua2UMdEUzj6GVAcO8gFYWDDGRKZhuYQ3AdPNAhR8VIRb3AQBdAswq3khUBwAAAABJRU5ErkJggg==',
			'7A47' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZAhgaHUNDkEVbGUMYWh0aRFDEWFsZpqKJTRFpdAh0aAhAdl/UtJWZmVkrs5Dcx+gg0uja6NCKbC9rg2ioa2jAFGQxkQageY0OAchiAWAxRwdCYgMVflSEWNwHAF/+zU0eZ4m9AAAAAElFTkSuQmCC',
			'8992' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGaY6IImJTGFtZXR0CAhAEgtoFWl0bQh0EEFRBxILaBBBct/SqKVLMzOjVkUhuU9kCmOgQ0hAowOKeQxAPpBEEWNpdGwImMKAxS2YbmYMDRkE4UdFiMV9ABhEzNw0my3oAAAAAElFTkSuQmCC',
			'F6A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMEx1QBILaGBtZQhlCAhAERNpZHR0dBBBFWtgbQiEiYGdFBo1LWzpqqioMCT3BTSItrI2BExF09voGgoyAU2sIQDNDlaQXjS3MIaAzEN280CFHxUhFvcBAOz5zfeSFcN6AAAAAElFTkSuQmCC',
			'A463' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7GB0YWhlCGUIdkMRYAximMjo6OgQgiYlMYQhlbXBoEEESC2hldGUF0Ujui1oKBFNXLc1Ccl9Aq0grq6NDA7J5oaGioa5AEVTzGFpZsYihuwUkhu7mgQo/KkIs7gMAqEXM78ZT4+wAAAAASUVORK5CYII=',
			'092A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgDWFsZHR2mOiCJiUwRaXRtCAgIQBILaBVpdGgIdBBBcl/U0qVLs1ZmZk1Dcl9AK2OgQysjTB1UjKHRYQpjaAiKHSyNDgGo6sBucUAVA7mZNTQQRWygwo+KEIv7AJWhyqjGmxeBAAAAAElFTkSuQmCC',
			'4E58' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGsoY6THVAFgsRaWBtYAgIQBJjBIsxOoggibFOAYpNhasDO2natKlhSzOzpmYhuS9gCkhXAIp5oaEgsUAU8xhA5mERY3R0QNELcjNDKAOqmwcq/KgHsbgPAEDRy4xb4GP/AAAAAElFTkSuQmCC',
			'BDEC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDHaYGIIkFTBFpZW1gCBBBFmsVaXRtYHRgQVUHFkN2X2jUtJWpoSuzkN2Hpg7FPGxiaHZguAWbmwcq/KgIsbgPAB5wzOz7ycJzAAAAAElFTkSuQmCC',
			'901C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEwNQBITmcIYwhDCECCCJBbQytrKGMLowIIiJtLoMIXRAdl906ZOW5kFRMjuY3VFUQeBrZhiAkA7GKag2gF2yxRUt4DczBjqgOLmgQo/KkIs7gMAWZfJ7U79tpYAAAAASUVORK5CYII=',
			'7F95' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGUMDkEVbRRoYHR0dGNDEWBsCUcWmgMVcHZDdFzU1bGVmZFQUkvsYHUQaGEICGkSQ9LKCeahiIkDICLQDWQykgtHRISAATYwhlGGqwyAIPypCLO4DAIzVyyKLRn5sAAAAAElFTkSuQmCC',
			'EDCC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNEQxhCHaYGIIkFNIi0MjoEBIigijW6Ngg6sGCIMToguy80atrK1FUrs5Ddh6aOgBiGHRhuwebmgQo/KkIs7gMAVW7NGKhy0/gAAAAASUVORK5CYII=',
			'08D6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDGaY6IImxBrC2sjY6BAQgiYlMEWl0bQh0EEASC2gFqgOKIbsvaunKsKWrIlOzkNwHVYdiXkArxDwRLHaIEHALNjcPVPhREWJxHwAj2cw85Am06gAAAABJRU5ErkJggg==',
			'D306' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNYQximMEx1QBILmCLSyhDKEBCALNbK0Ojo6OgggCrWytoQ6IDsvqilq8KWropMzUJyH1QdhnmuQL0iWOwQIeAWbG4eqPCjIsTiPgB/9s1JO1ri3gAAAABJRU5ErkJggg==',
			'12CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHaYGIImxOrC2MjoEBIggiYk6iDS6Ngg6sKDoZQCKAUkk963MWrV0KYhEch9QxRRWhDqYWACmGKMDK4YdIFVobgkRDXVAc/NAhR8VIRb3AQDlrsfvbct3TQAAAABJRU5ErkJggg==',
			'D76B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QgNEQx1CGUMdkMQCpjA0Ojo6OgQgi7UyNLo2ODqIoIq1sjYwwtSBnRS1dNW0pVNXhmYhuQ+oLoAVwzxGB9aGQDTzWBswxKaINDCi6Q0NAKpAc/NAhR8VIRb3AQBDyczt2iFKlgAAAABJRU5ErkJggg==',
			'3734' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nM2QsRGAIBAEj4AOoB8IzN8ZSajmP6ADtAep0sEI0FBH/7IN7nYe5XKMP+UVP0s2+ACmhlGGTOKkZUgQx5Q6lk+aqfHbY9nKWmJs/TII4l3fpxx4DkvHdF0fXAzrutw5G1aD81f/ezA3fgdOf86SQlCDfAAAAABJRU5ErkJggg==',
			'59AB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYQximMIY6IIkFNLC2MoQyOgSgiIk0Ojo6OoggiQUGiDS6NgTC1IGdFDZt6dLUVZGhWcjua2UMRFIHFWNodA0NRDEvoJUFbB6ymMgU1lZWNL2sAYwhQDEUNw9U+FERYnEfAJ7EzJtb6xqIAAAAAElFTkSuQmCC',
			'1602' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQximMEx1QBJjdWBtZQhlCAhAEhN1EGlkdHR0EEHRK9LA2hDQIILkvpVZ08KWrooCQoT7GB1EW4HqGh1Q9Ta6NgS0MqCJAa2YgioGcQuymGgIyM2MoSGDIPyoCLG4DwBe0ckyYpFWCgAAAABJRU5ErkJggg==',
			'C8DC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGaYGIImJtLK2sjY6BIggiQU0ijS6NgQ6sCCLNQDVAcWQ3Re1amXY0lWRWcjuQ1MHFYOYx0DADmxuwebmgQo/KkIs7gMAqC7MmaeFIhkAAAAASUVORK5CYII=',
			'299E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gVaXRtCEQRY0AVg7hp2tKlmZmRoVnI7gtgDHQIQdXL6MDQ6IBmHmsDS6MjmphIA6ZbQkMx3TxQ4UdFiMV9APyGyZoVp1s0AAAAAElFTkSuQmCC',
			'ABF0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDA1qRxVgDRFpZGximOiCJiUwRaXRtYAgIQBILaAWpY3QQQXJf1NKpYUtDV2ZNQ3IfmjowDA0FmYcqBlSHww5UtwS0At3cwIDi5oEKPypCLO4DABj3zGPek849AAAAAElFTkSuQmCC',
			'D87C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDA6YGIIkFTGFtBZIBIshirSKNDg2BDiwoYkB1jY4OyO6LWroybNXSlVnI7gOrm8LowIBuXgCmmKMDI6odQLewNjCguAXs5gYGFDcPVPhREWJxHwB3osz6rOP//AAAAABJRU5ErkJggg==',
			'0123' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsQ3AIAwE7cIbMJDZ4Bs3jMAUNGzAChRhyqBUJkmZKPF3J791Mo3LFPpTXvFjJZCRqWMCBseocCw0gRSU4Bjq7E4G55f6SGPLPTu/Y69SwbnbaLkX2mRYmUzCyosLq5gYFuev/vdgbvx2yg7JbdxqfSEAAAAASUVORK5CYII=',
			'4F6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpI37poiGOoQyhgYgi4WINDA6Ojogq2MEirE2oIqxTgGJMcLEwE6aNm1q2NKpK0OzkNwXAFKHZl5oKEhvoAOqW7CLobsFJMaA7uaBCj/qQSzuAwDklMmp+8jrRAAAAABJRU5ErkJggg==',
			'CBDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGUMDkMREWkVaWRsdHZDVBTSKNLo2BKKKAVWyIsTATopaNTVs6arI0Cwk96Gpg4lhmofFDmxuwebmgQo/KkIs7gMApGjLyOr4ZTwAAAAASUVORK5CYII=',
			'3D5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7RANEQ1hDHUMdkMQCpoi0sjYwOgQgq2wVaXQFiokgi00Bik2Fi4GdtDJq2srUzMysacjuA6pzaAhE1duKXcwVTQzkFkZHRxS3gNzMEMqI4uaBCj8qQizuAwCbgsu7NfWvTgAAAABJRU5ErkJggg==',
			'DFA1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWElEQVR4nGNYhQEaGAYTpIn7QgNEQx2mMLQiiwVMEWlgCGWYiiLWKtLA6OgQii7GCiSR3Re1dGrYUiCJ7D40dQixUCxi6OqmYIqFBoDFQgMGQfhREWJxHwDpo86dqY7x1wAAAABJRU5ErkJggg==',
			'0AC4' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHRoCkMRYAxhDGB0CGpHFRKawtrI2CLQiiwW0ijS6NjBMCUByX9TSaStTgVQUkvsg6oAmougVDQWKhYag2AFSJ4DmFpFGR6BOZDFGB5FGBzQ3D1T4URFicR8ACwDN/RzTEboAAAAASUVORK5CYII=',
			'ADCD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHUMdkMRYA0RaGR0CHQKQxESmiDS6Ngg6iCCJBbSCxBhhYmAnRS2dtjJ11cqsaUjuQ1MHhqGhmGIQdRh2YLgloBXTzQMVflSEWNwHADRUzHsYV/Z4AAAAAElFTkSuQmCC',
			'3FC5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RANEQx1CHUMDkMQCpog0MDoEOqCobBVpYG0QRBWbAhJjdHVAct/KqKlhS4FkFLL7wOoYGkQwzMMmJuiALAZxS0AAsvtEA4AqQh2mOgyC8KMixOI+AK3Uyx0h8XZ+AAAAAElFTkSuQmCC',
			'01C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YAhhCHaY6IImxBjAGMDoEBAQgiYlMYQ1gbRB0EEESC2hlAIoxwsTATopaCkKrosKQ3AdRxzAVUy/QXBQ7QGICKHawBjBguIXRgTUU3c0DFX5UhFjcBwAcU8j8hoYZtAAAAABJRU5ErkJggg==',
			'B0F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDA6Y6IIkFTGEMYW1gCAhAFmtlbWVtYHQQQVEn0uiKEAM7KTRq2srU0FVRYUjug6hjmIqitxUs1iCCYQcDmh2YbgG7GWgespsHKvyoCLG4DwCJscx6XnI3ZgAAAABJRU5ErkJggg==',
			'7747' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkNFQx0aHUNDkEVbGRodWh0aRNDFpqKJTQGKBjo0BCC7L2rVtJWZWSuzkNzH6MAQwAo0EdleVqAoa2jAFGQxEaAo0JYAZLEAkI2Njg6ExAYq/KgIsbgPAOPJzHtZU2iGAAAAAElFTkSuQmCC',
			'F2C7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCHUNDkMQCGlhbGR0CGkRQxEQaXRsE0MQYgGJgGu6+0KhVS5euWrUyC8l9QPkprA0MrQyoegOAYlNQxRgdWBsEAlDFWIGigQ6oYqKhDqGOKGIDFX5UhFjcBwBYjcza+YU6uwAAAABJRU5ErkJggg==',
			'5836' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkMYQxhDGaY6IIkFNLC2sjY6BASgiIk0OjQEOgggiQUGsLYyNDo6ILsvbNrKsFVTV6ZmIbuvFawOxTyGVoh5Ish2YBETmYLpFtYATDcPVPhREWJxHwA8OMz1gcbLjwAAAABJRU5ErkJggg==',
			'14C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YWhlCHVqRxVgdGKYyOgRMdUASE3VgCGVtEAgIQNHL6MoKJEWQ3Lcya+nSpUByGpL7gCpakdRBxURDXTHEGFox7WBoxXBLCKabByr8qAixuA8AqV7IqARILmQAAAAASUVORK5CYII=',
			'9FB1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WANEQ11DGVqRxUSmiDSwNjpMRRYLaAWKNQSEYog1OsD0gp00berUsKWhq5Yiu4/VFUUdBELMQxETwCIGdQuKGGsAUCyUITRgEIQfFSEW9wEAw43Mnj7ccZcAAAAASUVORK5CYII=',
			'849C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYWhlCGaYGIImJTGGYyujoECCCJBYAVMXaEOjAgqKO0RUkhuy+pVFLl67MjMxCdp/IFJFWhhC4Oqh5oqEODehiDK2MGHYAxdDcgs3NAxV+VIRY3AcAdvfK30VEC6AAAAAASUVORK5CYII=',
			'4DD3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpI37poiGsIYyhDogi4WItLI2OjoEIIkxhog0ujYENIggibFOgYgFILlv2rRpK1NXRS3NQnJfAKo6MAwNxTSPYQpWMQy3YHXzQIUf9SAW9wEA9/nOjOfXB4EAAAAASUVORK5CYII=',
			'0C40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7GB0YQxkaHVqRxVgDWIEiDlMdkMREpog0AEUCApDEAlpFGhgCHR1EkNwXtXTaqpWZmVnTkNwHUsfaCFeHEAsNRBED29GIagfYLY2obsHm5oEKPypCLO4DAGUmzS62mK/VAAAAAElFTkSuQmCC',
			'F78C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQx1CGaYGIIkB2Y2Ojg4BImhirg2BDiyoYq2MQIXI7guNWjVtVejKLGT3AdUFIKmDijE6sALNQxVjbWDFsEOkgRHDLUAempsHKvyoCLG4DwAyRcwf+cjh1AAAAABJRU5ErkJggg==',
			'A5CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHaYGIImxBogAxQOAJEJMZIpIA2uDoAMLklhAq0gIK1Alsvuilk5dunTVyixk9wW0MjS6ItSBYWgophjQPKAYuh2srehuCWhlDEF380CFHxUhFvcBADRDy7XUijr7AAAAAElFTkSuQmCC',
			'3985' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7RAMYQxhCGUMDkMQCprC2Mjo6OqCobBVpdG0IRBWbItLo6Ojo6oDkvpVRS5dmha6MikJ23xTGQKBxDSIo5jEAzQtAE2MB2yGC4RaHAGT3QdzMMNVhEIQfFSEW9wEA2/jLTuyBhq8AAAAASUVORK5CYII=',
			'AEED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDHUMdkMRYA0QaWIEyAUhiIlMgYiJIYgGtKGJgJ0UtnRq2NHRl1jQk96GpA8PQUILmoYgFoIhhunmgwo+KEIv7ANaKypdHbGNQAAAAAElFTkSuQmCC',
			'A7F2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB1EQ11DA6Y6IImxBjA0ujYwBAQgiYlMAYkxOoggiQW0MrSyguSQ3Be1dNW0paFAGsl9QHUBQHWNyHaEhjI6AMVaGVDMY20Aik1BFRMBiQVgijGGhgyC8KMixOI+ACrCzDaQNHM3AAAAAElFTkSuQmCC',
			'D540' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgNEQxkaHVqRxQKmiDQwtDpMdUAWawWKTXUICEAVC2EIdHQQQXJf1NKpS1dmZmZNQ3JfQCtDo2sjXB1CLDQQTUyk0aERzY4prECVqG4JDWAMQXfzQIUfFSEW9wEAoALO/GngSIEAAAAASUVORK5CYII=',
			'0C0B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7GB0YQxmmMIY6IImxBrA2OoQyOgQgiYlMEWlwdHR0EEESC2gVaWBtCISpAzspaum0VUtXRYZmIbkPTR2KmAgBO7C5BZubByr8qAixuA8AD7nLTG9Zc+MAAAAASUVORK5CYII=',
			'53B2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QkNYQ1hDGaY6IIkFNIi0sjY6BASgiDE0ujYEOoggiQUGMIDUNYgguS9s2qqwpaGrVkUhu68VrK4R2Q6gGNC8gFZktwRAxKYgi4lMgbgFWYw1AORmxtCQQRB+VIRY3AcAzcnNU2nu+94AAAAASUVORK5CYII=',
			'CFA0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WENEQx2mMLQii4m0ijQwhDJMdUASC2gUaWB0dAgIQBZrEGlgbQh0EEFyX9SqqWFLV0VmTUNyH5o6hFgomlgjSF0Aih0gtwDFUNzCGgIWQ3HzQIUfFSEW9wEAVsjNU99MzVMAAAAASUVORK5CYII=',
			'9ED5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGUMDkMREpog0sDY6OiCrC2gFijUEYhNzdUBy37SpU8OWroqMikJyH6srSF1Agwiyza2YYgJQO0Qw3OIQgOw+iJsZpjoMgvCjIsTiPgBrzcuuYgEpVwAAAABJRU5ErkJggg==',
			'AD9C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaYGIImxBoi0Mjo6BIggiYlMEWl0bQh0YEESC2iFiCG7L2rptJWZmZFZyO4DqXMIgasDw9BQoFgDqhhInSOmHRhuCWjFdPNAhR8VIRb3AQA5XcyCRbf3FAAAAABJRU5ErkJggg==',
			'70C2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHaY6IIu2MoYwOgQEBKCIsbayNgg6iCCLTRFpdAXSIsjui5q2MhVEIbmP0QGsrhHZDtYGsFgrsltEGkB2CExBFgtogLgFVQzkZsfQkEEQflSEWNwHAHBby6Gfh4HRAAAAAElFTkSuQmCC',
			'34D7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RAMYWllDGUNDkMQCpjBMZW10aBBBVtnKEMraEIAqNoXRFSQWgOS+lVFLly5dFbUyC9l9U0RagepaUWxuFQ11BdmEagdIXQADqltaWRsdHbC4GUVsoMKPihCL+wBQh8waNA3gBgAAAABJRU5ErkJggg==',
			'3AC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYAhhCHUIdkMQCpjCGMDoEOgQgq2xlbWVtEGgQQRabItLoClKP5L6VUdNWpq5atTQL2X2o6qDmiYaCxFDMawWpQ7UjAKjXEc0togEijQ5obh6o8KMixOI+ADgyzU2ZG12uAAAAAElFTkSuQmCC',
			'9978' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZWgICAhAEgtoFWl0aAh0EEEXa3SAqQM7adrUpUuzlq6amoXkPlZXxkCHKQwo5jG0MgB1MqKYJ9DK0ujogCoGcgtrA6pesJsbGFDcPFDhR0WIxX0A3gfMY5QI4s8AAAAASUVORK5CYII=',
			'4CD5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37pjCGsoYyhgYgi4WwNro2Ojogq2MMEWlwbQhEEWOdItLA2hDo6oDkvmnTpq1auioyKgrJfQFgdQENIkh6Q0MxxRimQOxAFQO5xSEAxX1gNzNMdRgM4Uc9iMV9AKfjzNj/Zku5AAAAAElFTkSuQmCC',
			'65D9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WANEQ1lDGaY6IImJTBFpYG10CAhAEgtoAYo1BDqIIIs1iIQgiYGdFBk1denSVVFRYUjuC5nC0OjaEDAVRW8rWKwBVUwEJIZih8gU1lZ0t7AGMIagu3mgwo+KEIv7AKx/zX8q6p9QAAAAAElFTkSuQmCC',
			'B296' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGaY6IIkFTGFtZXR0CAhAFmsVaXRtCHQQQFHHABZDdl9o1KqlKzMjU7OQ3AdUN4UhJBDNPIYABqBeERQxRgdGdLEprA3obgkNEA11QHPzQIUfFSEW9wEARLbNCeKk+tsAAAAASUVORK5CYII=',
			'8A7F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0NDkMREpjCGMDQEOiCrC2hlbUUXE5ki0ujQ6AgTAztpadS0lVlLV4ZmIbkPrG4KI5p5oqEOAehiIkDTGDHscG1AFWMNwBQbqPCjIsTiPgBsPcq3YQp4xQAAAABJRU5ErkJggg==',
			'528F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGUNDkMQCGlhbGR0dHRhQxEQaXRsCUcQCAxgaHRHqwE4Km7Zq6arQlaFZyO5rZZiCbh5QLIAVzbyAVkYHdDGRKawN6HpZA0RDHUIZUc0boPCjIsTiPgAFf8lf2WfxlQAAAABJRU5ErkJggg==',
			'478B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGOoQyhjogi4UwNDo6OjoEIIkxAsVcGwIdRJDEWKcwtDIi1IGdNG3aqmmrQleGZiG5L2AKQwAjmnmhoYwOrGjmMUxhbcAUE2lA1wsSY0B380CFH/UgFvcBAHW+ys1FY4RjAAAAAElFTkSuQmCC',
			'ADC1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHVqRxVgDRFoZHQKmIouJTBFpdG0QCEUWC2gFiTHA9IKdFLV02srUVauWIrsPTR0YhoZiikHUCaCLgdyCJgZ2c2jAIAg/KkIs7gMAaunNgeNyfDIAAAAASUVORK5CYII=',
			'A3B0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1YQ1hDGVqRxVgDRFpZGx2mOiCJiUxhaHRtCAgIQBILaGUAqnN0EEFyX9TSVWFLQ1dmTUNyH5o6MAwNBZkXiCIGVIfFDky3BLRiunmgwo+KEIv7AFHbzW37aGgMAAAAAElFTkSuQmCC',
			'36DD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7RAMYQ1hDGUMdkMQCprC2sjY6OgQgq2wVaWRtCHQQQRabItKAJAZ20sqoaWFLV0VmTUN23xTRVgy9QPNciRDD5hZsbh6o8KMixOI+AH7ly7Yn/NjAAAAAAElFTkSuQmCC',
			'6E8B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQxlCGUMdkMREpog0MDo6OgQgiQW0iDSwNgQ6iCCLNaCoAzspMmpq2KrQlaFZSO4LwWZeKxbzsIhhcws2Nw9U+FERYnEfAKVJywDYbzISAAAAAElFTkSuQmCC',
			'E986' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOgigiTk6Ojoguy80aunSrNCVqVlI7gtoYAwEqkMzjwFsngiKGAsWMUy3YHPzQIUfFSEW9wEAF8TM9YDyYRoAAAAASUVORK5CYII=',
			'FA27' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUNDkMQCGhhDGB0dGkRQxFhbWYEkqphIowOQDEByX2jUtJVZIIjkPrC6VoZWBhS9oqEOUximMKCbFwB0D5qYowOjA7qYa2ggithAhR8VIRb3AQCTx81SbNAaewAAAABJRU5ErkJggg==',
			'AC52' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1lDHaY6IImxBrA2ujYwBAQgiYlMEWlwBaoWQRILaBVpYJ0KlENyX9TSaauWZmatikJyH0gdkGxEtiM0FCzWyoBmnmtDwBRUMdZGR0eHAFQxxlCGUMbQkEEQflSEWNwHAHguzW93SeZHAAAAAElFTkSuQmCC',
			'069D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMdkMRYA1hbGR0dHQKQxESmiDSyNgQ6iCCJBbSKNCCJgZ0UtXRa2MrMyKxpSO4LaBVtZQjB0NvogGYeyA5HNDFsbsHm5oEKPypCLO4DAMl7ylvgjyMnAAAAAElFTkSuQmCC',
			'4F07' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpI37poiGOkxhDA1BFgsRaWAIZWgQQRJjBIoxOjqgiLFOEWlgbQgAQoT7pk2bGrZ0VdTKLCT3BUDUtSLbGxoKFpuC6hawHQHoYgyhjA4YYlPQxAYq/KgHsbgPAGiZy2psBnanAAAAAElFTkSuQmCC',
			'5ECB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkNEQxlCHUMdkMQCGkQaGB0CHQLQxFgbBB1EkMQCA0BijDB1YCeFTZsatnTVytAsZPe1oqhDEUM2L6AV0w6RKZhuYQ3AdPNAhR8VIRb3AQDTTcrzZuJP6gAAAABJRU5ErkJggg==',
			'CBBB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WENEQ1hDGUMdkMREWkVaWRsdHQKQxAIaRRpdGwIdRJDFGlDUgZ0UtWpq2NLQlaFZSO5DUwcTwzQPix3Y3ILNzQMVflSEWNwHACjTzQGpVvwqAAAAAElFTkSuQmCC',
			'C65F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDHUNDkMREWllbWRsYHZDVBTSKNGKINYg0sE6Fi4GdFLVqWtjSzMzQLCT3BTSItjI0BKLrbXRAFwPa4YomBnILo6MjihjIzQyhqG4ZqPCjIsTiPgBHdcnOcjGw2gAAAABJRU5ErkJggg==',
			'915D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAlhDHUMdkMREpjAGsDYwOgQgiQW0soLFRFDEgHqnwsXATpo2dVXU0szMrGlI7mN1ZQhgaAhE0cvQiikmADIPTUxkCkMAo6MjiluALgllCGVEcfNAhR8VIRb3AQB6mMhaxKu7/QAAAABJRU5ErkJggg==',
			'7586' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGaY6IIu2ijQwOjoEBKCJsTYEOgggi00RCWF0dHRAcV/U1KWrQlemZiG5j9GBodHR0RHFPNYGhkZXoHkiSGIiDSIYYgENrK3obgloYAzBcPMAhR8VIRb3AQD30MtqW1HZogAAAABJRU5ErkJggg==',
			'3725' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nM2QsRGAIAxFQ+EGsE8o6EORhg3cIhRugO4AU4odUUu9I7979+/fu0B7nMBM+cXPkWNkwzQwKpC996iaG+QgUbPSqcSAg19N7Wh1TWn0K0BX06o9g53f2CJABq1ysdKbNPo5srIw7TjB/z7Mi98JUW7KpkCuzwUAAAAASUVORK5CYII=',
			'AA73' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0IdkMRYAxhDGBoCHQKQxESmsLYyNAQ0iCCJBbSKNDo0OjQEILkvaum0lVlLVy3NQnIfWN0UhgZk80JDRUMdAhgwzHN0wBRzBboyAEOMAcXNAxV+VIRY3AcAXWbORlreJz0AAAAASUVORK5CYII=',
			'24BE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYWllDGUMDkMREpjBMZW10dEBWF9DKEMraEIgixtDK6IqkDuKmaUuXLg1dGZqF7L4AkVZ08xgdRENd0cxjBZqIbocISAxNb2goppsHKvyoCLG4DwAdmcnH75pUMwAAAABJRU5ErkJggg==',
			'F832' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMZQxhDGaY6IIkFNLC2sjY6BASgiIk0OjQEOoigqWMAiooguS80amXYqqmrVkUhuQ+qrtEBw7yAVgZMsSkMWNyCKgZyM2NoyCAIPypCLO4DAFpzzr3+cWvBAAAAAElFTkSuQmCC',
			'7DE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHaY6IIu2irSyNjAEBKCKNbo2MDqIIItNAYnB1UHcFDVtZWroqqlZSO4D6kJWB4asDZjmiWARC2jAdEtAAxY3D1D4URFicR8ATUbMYBxH2k0AAAAASUVORK5CYII=',
			'B45C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYWllDHaYGIIkFTGGYytrAECCCLNbKEMrawOjAgqKO0ZV1KqMDsvtCo5YuXZqZmYXsvoApIq0MDYEODCjmiYY6YIgB3QIUQ7WDoZXR0QHFLSA3M4QyoLh5oMKPihCL+wAGB8wYtRoi1AAAAABJRU5ErkJggg==',
			'F18D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMZAhhCGUMdkMQCGhgDGB0dHQJQxFgDWBsCHURQxBjA6kSQ3BcatSpqVejKrGlI7kNTBxfDZh4uO9DcEoru5oEKPypCLO4DACkHydhisXyWAAAAAElFTkSuQmCC',
			'2591' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQxlCGVqRxUSmiDQwOjpMRRYLaBVpYG0ICEXR3SoSAhSD6YW4adrUpSszo5aiuC+AodEhJADFDkYHoFgDqhhrg0ijI5oY0NZWoFtQxEJDGUOAbg4NGAThR0WIxX0AZI7LpQm3vSEAAAAASUVORK5CYII='        
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