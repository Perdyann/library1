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
			'FFEA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAU0lEQVR4nGNYhQEaGAYTpIn7QkNFQ11DHVqRxQIaRBpYGximOmCKBQRgiDE6iCC5LzRqatjS0JVZ05Dch6YOWSw0BLd5+MVCHVHEBir8qAixuA8AT03MIETt5BMAAAAASUVORK5CYII=',
			'4495' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpI37pjC0MoQyhgYgi4UwTGV0dHRAVscYwhDK2hCIIsY6hdEVKObqgOS+adOWLl2ZGRkVheS+gCkirQwhAQ0iSHpDQ0VDHRpQxUBuYQTagSHm6BAQgCbGEMow1WEwhB/1IBb3AQD/gcrE87xcGwAAAABJRU5ErkJggg==',
			'BE55' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDHUMDkMQCpog0sDYwOiCrC2jFIgZSN5XR1QHJfaFRU8OWZmZGRSG5D6QOSDaIoJmHTYy1IdBBBM0ORkeHAGT3gdzMEMow1WEQhB8VIRb3AQBNSMxzqy+/kQAAAABJRU5ErkJggg==',
			'B642' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgMYQxgaHaY6IIkFTGFtZWh1CAhAFmsVaWSY6ugggqIOyAt0aBBBcl9o1LSwlZlZq6KQ3BcwRbSVtdGh0QHNPNfQgFYGNDGgqikM6G5pdAjAdLNjaMggCD8qQizuAwBZcc673RUJeQAAAABJRU5ErkJggg==',
			'8AAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYAhimMIaGIImJTGEMYQhldEBWF9DK2sro6IgiJjJFpNG1IRAmBnbS0qhpK1NXRYZmIbkPTR3UPNFQ11B0MUx12PSyBmCKDVT4URFicR8AtpPLa3vYl5IAAAAASUVORK5CYII=',
			'74A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QkMZWhmmMEx1QBZtZZjKEMoQEIAqFsro6Ogggiw2hdGVtSEQJgZxU9TSpUtXRUWFIbmP0UGklbUhYCqyXtYG0VDX0IAGZDEgG6QOxY4AiBiKW6BiqG4eoPCjIsTiPgCRicwewpK1JAAAAABJRU5ErkJggg==',
			'4F91' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37poiGOoQytKKIhYg0MDo6TEUWYwSKsTYEhCKLsU4Bi8H0gp00bdrUsJWZUUuR3RcAVMcQEoBiR2ioCEgG1V6gOkZsYo4OGGIMoQyhAYMh/KgHsbgPAIrly9HgCYevAAAAAElFTkSuQmCC',
			'8D4F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQxgaHUNDkMREpoi0MrQ6OiCrC2gVaXSYiioGVNfoEAgXAztpadS0lZmZmaFZSO4DqXNtxDTPNTQQ045GDDtaGdDEoG5GERuo8KMixOI+AM7Jy8oEqQqGAAAAAElFTkSuQmCC',
			'E69A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGVqRxQIaWFsZHR2mOqCIiTSyNgQEBKCKNbA2BDqIILkvNGpa2MrMyKxpSO4LaBBtZQiBq4Ob59AQGBqCJubYgK4O5BZHFDGImxlRxAYq/KgIsbgPAJWXzGgd4Y5YAAAAAElFTkSuQmCC',
			'3BAE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RANEQximMIYGIIkFTBFpZQhldEBR2SrS6OjoiCoGVMfaEAgTAztpZdTUsKWrIkOzkN2Hqg5unmsoFjE0dQFY9ILcDBRDcfNAhR8VIRb3AQBRfcrvGaA2pAAAAABJRU5ErkJggg==',
			'0170' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA1qRxVgDGAMYGgKmOiCJiUxhBYkFBCCJAXUFMDQ6OogguS9q6aqoVUtXZk1Dch9Y3RRGmDqEWACqmMgUkAgDih1AWwNYGxhQ3MLowBoKFENx80CFHxUhFvcBANnhyU4vpmHoAAAAAElFTkSuQmCC',
			'5CF5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1lDA0MDkMQCGlgbXRsYHRhQxEQa0MUCA0QaWBsYXR2Q3Bc2bdqqpaEro6KQ3dcKUscANAFJNxaxgFaIHchiIlNAbmEIQHYfawDQzQ0MUx0GQfhREWJxHwCFDsuiAP+S7wAAAABJRU5ErkJggg==',
			'9BFC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDA6YGIImJTBFpZW1gCBBBEgtoFWl0bWB0YEEVA6pjdEB237SpU8OWhq7MQnYfqyuKOgiEmocsJoDFDmxuAbu5gQHFzQMVflSEWNwHAD3+yobWJtZXAAAAAElFTkSuQmCC',
			'2B9B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMdkMREpoi0Mjo6OgQgiQW0ijS6NgQ6iCDrbhVpZQWKBSC7b9rUsJWZkaFZyO4LEGllCAlEMY/RQaTRAc081gaRRkc0MZEGTLeEhmK6eaDCj4oQi/sAvGPLDMKaB/4AAAAASUVORK5CYII=',
			'2C65' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nM2QsRGAMAhFoWCDDEQKeyxoMg0WbmCygYWZ0qQjp6Xehd+9+xzvgPoYg5nyix8JKiiqOBYO2mKM7HuyB1tsZNAYGS7s/UqpZ75S8n7SepEtuF3kvisDI+s3VvastZoLi/dT7c6QeYL/fZgXvxsFtMtx4i8YQAAAAABJRU5ErkJggg==',
			'0704' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QwQ2AMAhF4dAN6j50A0zkwjT00A0aN+ilU9ojVY8a5d9ePuEF6Jcx+FNe8UNahCoYOxYYMglkz2KFnBIVz7hACcaVnZ+2vreuqs5v9DjYSvMu0mCyTTeCYaKTSzSQ2Q9psJPzV/97MDd+B4ihzSsv2g71AAAAAElFTkSuQmCC',
			'DC7E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDA0MDkMQCprA2OjQEOiCrC2gVacAmxtDoCBMDOylq6bRVq5auDM1Cch9Y3RRGTL0BmGKODmhiQLe4NqCKgd3cwIji5oEKPypCLO4DAMkfzGVp4eKOAAAAAElFTkSuQmCC',
			'5A6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGUMDkMQCGhhDGB0dHRhQxFhbWRtQxQIDRBpdGxhhYmAnhU2btjJ16srQLGT3tQLVoZnH0Coa6toQiGoHSB2amMgUkUZHNL2sQHsd0Nw8UOFHRYjFfQBLq8rbzTitjAAAAABJRU5ErkJggg==',
			'9C29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGaY6IImJTGFtdHR0CAhAEgtoFWlwbQh0EEETY0CIgZ00beq0VatWZkWFIbmP1RWoopVhKrJeBpDeKUC7kMQEgGIOAQwodoDd4sCA4haQm1lDA1DcPFDhR0WIxX0AwbLLvJ1ZwKoAAAAASUVORK5CYII=',
			'C56B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQxlCGUMdkMREWkUaGB0dHQKQxAIaRRpYGxwdRJDFGkRCWBsYYerATopaNXXp0qkrQ7OQ3AeUb3RFNw8k1hCIal6jCIaYSCtrK7pbWEMYQ9DdPFDhR0WIxX0Av+PL6CVuxD0AAAAASUVORK5CYII=',
			'5769' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7QkNEQx1CGaY6IIkFNDA0Ojo6BASgibk2ODqIIIkFBjC0sjYwwsTATgqbtmra0qmrosKQ3dfKEMDq6DAVWS9DK6MDK9BUZLEAoGlAMRQ7RKaINDCiuYU1AKgCzc0DFX5UhFjcBwBB9cwNDltkKAAAAABJRU5ErkJggg==',
			'66B9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGaY6IImJTGFtZW10CAhAEgtoEWlkbQh0EEEWaxBpYG10hImBnRQZNS1saeiqqDAk94VMEQWZNxVFb6tIoyvYBAwxFDuwuQWbmwcq/KgIsbgPAA7mzQkDFfr7AAAAAElFTkSuQmCC',
			'E9B6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDGaY6IIkFNLC2sjY6BASgiIk0ujYEOgigizU6OiC7LzRq6dLU0JWpWUjuC2hgDASqQzOPAWyeCIoYCxYxTLdgc/NAhR8VIRb3AQB2Mc4KLEJhngAAAABJRU5ErkJggg==',
			'B19A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7QgMYAhhCGVqRxQKmMAYwOjpMdUAWa2UNYG0ICAhAUccAFAt0EEFyX2jUqqiVmZFZ05DcB1LHEAJXBzUPKNYQGBqCJsbYgKYOqJfR0RFFLDSANZQhlBFFbKDCj4oQi/sAWCHKjCZzgbsAAAAASUVORK5CYII=',
			'9A6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMDkMREpjCGMDo6OiCrC2hlbWVtQBcTaXRtYISJgZ00beq0lalTV4ZmIbmP1RWoDs08hlbRUNeGQBQxAbB5qGIiU0QaHdH0sgaINDqguXmgwo+KEIv7AJq1ylzwSRLcAAAAAElFTkSuQmCC',
			'6558' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaY6IImJTBFpYG1gCAhAEgtoAYkxOoggizWIhLBOhasDOykyaurSpZlZU7OQ3BcyhaHRoSEA1bxWkFggqnmtIo2uaGIiU1hbGR0dUPSyBjCGMIQyoLh5oMKPihCL+wDVC8zCAVsgkQAAAABJRU5ErkJggg==',
			'3114' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7RAMYAhimMDQEIIkFTGEMYAhhaEQWY2hlDWAMYWhFEZsC1jslAMl9K6NWRa2atioqCtl9YHWMDqjmgcVCQzDE0N2CKSYawBrKGOqAIjZQ4UdFiMV9AFv/ytVCHlAJAAAAAElFTkSuQmCC',
			'4C03' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjCGMkxhCHVAFgthbXQIZXQIQBJjDBFpcHR0aBBBEmOdItLA2hDQEIDkvmnTpq1auipqaRaS+wJQ1YFhaChETATFLZh2MEzBdAtWNw9U+FEPYnEfAEEAzSgB7b2TAAAAAElFTkSuQmCC',
			'3B9A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RANEQxhCGVqRxQKmiLQyOjpMdUBW2SrS6NoQEBCALAZUx9oQ6CCC5L6VUVPDVmZGZk1Ddh9QHUMIXB3cPIeGwNAQNDHHBlR1ELc4oohB3MyIat4AhR8VIRb3AQCRosuMizeBVQAAAABJRU5ErkJggg==',
			'7C22' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkMZQxlCGaY6IIu2sjY6OjoEBKCIiTS4NgQ6iCCLTQHxAhpEkN0XNW3VqpVZq6KQ3McI0tXK0IhsBytI1xSgKJKYCBA6BABFkcQCGoBucWAIQBVjDGUNDQwNGQThR0WIxX0APCvMMAMEadcAAAAASUVORK5CYII=',
			'86F1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA1qRxUSmsLayNjBMRRYLaBVpBIqFoqoTaQCKwfSCnbQ0alrY0tBVS5HdJzJFtBVJHdw8VyLEoG5BEQO7GeiWgEEQflSEWNwHAFpfy5wvsGE0AAAAAElFTkSuQmCC',
			'D837' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhDGUNDkMQCprC2sjY6NIggi7WKAEUC0MRYWxnAogj3RS1dGbZq6qqVWUjug6prZcA0bwoWsQAGDLc4OmBxM4rYQIUfFSEW9wEAZEXOiCdoZS4AAAAASUVORK5CYII=',
			'A94C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHaYGIImxBrC2MrQ6BIggiYlMEQGqcnRgQRILaAWKBTo6ILsvaunSpZmZmVnI7gtoZQx0bYSrA8PQUIZG19BAFLGAVpZGh0Z0O4BuaUR1C9A8DDcPVPhREWJxHwDLbMzyVUoYJwAAAABJRU5ErkJggg==',
			'C7AA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WENEQx2mMLQii4m0MjQ6hDJMdUASC2hkaHR0dAgIQBZrYGhlbQh0EEFyX9SqVdOWrorMmobkPqC6ACR1UDFGB9bQwNAQFDtYG9DVibSKYIixhmCKDVT4URFicR8A32fMp8GRSuAAAAAASUVORK5CYII=',
			'7435' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AMAwE30U2CPs4RfpQuMk0TpENyAgUMCWhc4ASJPzdSa8/GfvtFH/KJ34iqCQkydKK5kpgjEyg88gWiighsvXL67q3LWfjR+wrCqs3XaeTsKaB+XOlb1jWvaornNKFkaDxD/73Yh78DkX7y8jKN0lNAAAAAElFTkSuQmCC',
			'09F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MDkMRYA1hbWYEyyOpEpog0uqKJBbSCxVwdkNwXtXTp0tTQlVFRSO4LaGUMdAWZgaKXoRFdTGQKC9gOZDGIWxgCkN0HdnMDw1SHQRB+VIRY3AcAC57KmIltvj8AAAAASUVORK5CYII=',
			'02F5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA0MDkMRYA1hbWYEyyOpEpog0uqKJBbQygMRcHZDcF7V01dKloSujopDcB1Q3hRVkBqreAHQxkSmMDiB7kcWAbmkAqgtAdh+jg2ioawPDVIdBEH5UhFjcBwCHpsom75lzZgAAAABJRU5ErkJggg==',
			'9A88' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaY6IImJTGEMYXR0CAhAEgtoZW1lbQh0EEERE2l0RKgDO2na1Gkrs0JXTc1Cch+rK4o6CGwVDXVFM08AaB66mMgUTL2sASKNDmhuHqjwoyLE4j4ASNnMgUovQUsAAAAASUVORK5CYII=',
			'86DA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGVqRxUSmsLayNjpMdUASC2gVaWRtCAgIQFEn0sDaEOggguS+pVHTwpauisyahuQ+kSmirUjq4Oa5NgSGhmCKoaiDuMURRQziZkYUsYEKPypCLO4DALczzIhY6QAhAAAAAElFTkSuQmCC',
			'B49A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7QgMYWhlCgRhJLGAKw1RGR4epDshiQFWsDQEBASjqGF1ZGwIdRJDcFxq1dOnKzMisaUjuC5gi0soQAlcHNU801KEhMDQE1Y5WxgY0dVOAYo6OKGIQNzOiiA1U+FERYnEfALO0zHnhcgpNAAAAAElFTkSuQmCC',
			'5EE1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDHVqRxQIaRBpYGximYhELRRYLDACLwfSCnRQ2bWrY0tBVS1Hc14qiDqdYABYxkSmYYqwBYDeHBgyC8KMixOI+AB4wyz8RcCsLAAAAAElFTkSuQmCC',
			'BD75' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1hDA0MDkMQCpoi0MjQEOiCrC2gVaXRAF5sCFGt0dHVAcl9o1LSVWUtXRkUhuQ+sbgpDgwi6eQGYYo4OjA4iaG5hbWAIQHYf2M0NDFMdBkH4URFicR8AkULOBbmJhpgAAAAASUVORK5CYII=',
			'F529' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkNFQxlCGaY6IIkFNIg0MDo6BASgibE2BDqIoIqFMCDEwE4KjZq6dNXKrKgwJPcBzWl0aGWYiqoXKDaFoQHNvEaHAAY0O1hbGR0Y0NzCGMIaGoDi5oEKPypCLO4DAAznzPAbYaJeAAAAAElFTkSuQmCC',
			'623A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGVqRxUSmsLayNjpMdUASC2gRaXRoCAgIQBZrYGh0aHR0EEFyX2TUqqWrpq7MmobkvpApDFMYEOogelsZAhgaAkNDUMQYHYBiKOqAbmlgRdPLGiAa6hjKiCI2UOFHRYjFfQABtcylIN7GfgAAAABJRU5ErkJggg==',
			'D3CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QgNYQxhCHUMdkMQCpoi0MjoEOgQgi7UyNLo2CDqIoIq1sjYwwsTATopauips6aqVWdOQ3IemDsk8bGJodmBxCzY3D1T4URFicR8AySzMquLsZAQAAAAASUVORK5CYII=',
			'2DDB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGUMdkMREpoi0sjY6OgQgiQW0ijS6NgQ6iCDrhooFILtv2rSVqasiQ7OQ3ReAog4MGR0wzWNtwBQTacB0S2goppsHKvyoCLG4DwC6s8yhaYM8ygAAAABJRU5ErkJggg==',
			'3B69' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7RANEQxhCGaY6IIkFTBFpZXR0CAhAVtkq0uja4OgggiwGVMfawAgTAztpZdTUsKVTV0WFIbsPpM7RYaoIhnkBDVjEUOzA5hZsbh6o8KMixOI+APEPzBhh8rLtAAAAAElFTkSuQmCC',
			'C314' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WENYQximMDQEIImJtIq0MoQwNCKLBTQyNDqGMLSiiDUwtAL1TglAcl/UqlVhq6atiopCch9EHaMDmt5GhymMoSFodjhgcwuaGMjNjKEOKGIDFX5UhFjcBwAPVM3QSrXeFgAAAABJRU5ErkJggg==',
			'E092' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGaY6IIkFNDCGMDo6BASgiLG2sjYEOoigiIk0uoJIJPeFRk1bmZkZtSoKyX0gdQ4hAY0OaHodGgJaGdDsYGwImMKAxS2YbmYMDRkE4UdFiMV9AF3jzReG2URMAAAAAElFTkSuQmCC',
			'74C9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkMZWhlCHaY6IIu2MkxldAgICEAVC2VtEHQQQRabwujK2sAIE4O4KWrp0qVAMgzJfUAVrawNDFOR9bI2iIa6AmlkMSAbqE4AxQ6gG1rR3RLQgMXNAxR+VIRY3AcAuvXLNWeq6ogAAAAASUVORK5CYII=',
			'2F38' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANEQx1DGaY6IImJTBFpYG10CAhAEgtoFQGSgQ4iyLpBYgh1EDdNmxq2auqqqVnI7gtAUQeGjA6Y5rE2YIqJNGC6JTRUpIERzc0DFX5UhFjcBwDMMcym/4Et6wAAAABJRU5ErkJggg==',
			'E4D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYWllDGUMDkMSA7KmsjY4ODKhioawNgWhijK5AMVcHJPeFRi1dunRVZFQUkvsCGkRaWUEkil7RUFcMMaBbgHZgiDU6BCC7D+JmhqkOgyD8qAixuA8AKTnNMueILYkAAAAASUVORK5CYII=',
			'5F02' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QkNEQx2mMEx1QBILaBBpYAhlCAhAE2N0dHQQQRILDBBpYIWohrsvbNrUsKWrooAQyX2tYHWNyHZAxVqR3RLQCrID6BokMZEpELcgi7EC7WWYwhgaMgjCj4oQi/sAtsvMZosFNiwAAAAASUVORK5CYII=',
			'D359' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7QgNYQ1hDHaY6IIkFTBFpZW1gCAhAFmtlaHRtYHQQQRVrZZ0KFwM7KWrpqrClmVlRYUjuA6kDklPR9DY6NAQ0oIu5NgSg2gF0C6OjA4pbQG5mCGVAcfNAhR8VIRb3AQDQPc2N9dBl7AAAAABJRU5ErkJggg==',
			'3D6E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAV0lEQVR4nGNYhQEaGAYTpIn7RANEQxhCGUMDkMQCpoi0Mjo6OqCobBVpdG1AE5sCEmOEiYGdtDJq2srUqStDs5DdB1KH1bxAgmLY3ILNzQMVflSEWNwHADNzyqBb8Tz/AAAAAElFTkSuQmCC',
			'75A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMLSiiLaKNDCEMkx1QBNjdHQICEAWmyISwtoQ6CCC7L6oqUuXrorMmobkPkYHhkZXhDowZG0AioWiiok0iADVBaDYEdDA2sraEIDiloAGRqC9AahuHqDwoyLE4j4AhAfM1OkIlh8AAAAASUVORK5CYII=',
			'50D5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUMDkMQCGhhDWBsdHRhQxFhbWRsCUcQCA0QaXRsCXR2Q3Bc2bdrK1FWRUVHI7msFqQtoEEG2GYtYQCvEDmQxkSkgtzgEILuPNQDkZoapDoMg/KgIsbgPAC9lzD5CTybPAAAAAElFTkSuQmCC',
			'B4CD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QgMYWhlCHUMdkMQCpjBMZXQIdAhAFgOqYm0QdBBBUcfoytrACBMDOyk0aunSpatWZk1Dcl/AFJFWJHVQ80RDXTHEGFox7WBoRXcLNjcPVPhREWJxHwAInMwYd+BRowAAAABJRU5ErkJggg==',
			'14A0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YWhmmADGSGKsDw1SGUIapDkhiog4MoYyODgEBKHoZXVkbAh1EkNy3Mmvp0qWrIrOmIbmP0UGkFUkdVEw01DUUXYwBqC4AzQ6wGKpbQsBiKG4eqPCjIsTiPgB/8smRAGaSDQAAAABJRU5ErkJggg==',
			'12EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDHaYGIImxOrC2sjYwBIggiYk6iDS6AlWzoOhlAIshu29l1qqlS0OBJJL7gCqmsCLUwcQCMMUYHVgx7GBtwHBLiGioK5qbByr8qAixuA8AP/HHbNOyOTAAAAAASUVORK5CYII=',
			'70C5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZAhhCHUMDkEVbGUMYHQIdUFS2srayNgiiik0RaXRtYHR1QHZf1LSVqatWRkUhuY/RAaSOoUEESS9rA6aYSAPEDmSxgAaQWwICAlDEQG52mOowCMKPihCL+wALQcq+UwYDOAAAAABJRU5ErkJggg==',
			'464E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37pjCGMDQ6hgYgi4WwtjK0Ojogq2MMEWlkmIoqxjpFpIEhEC4GdtK0adPCVmZmhmYhuS9gimgrayOq3tBQkUbX0EAHVLeINDqgqWOYAnQLhhgWNw9U+FEPYnEfAPusypp/lRa9AAAAAElFTkSuQmCC',
			'88F8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVUlEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA6Y6IImJTGFtZW1gCAhAEgtoFWl0bWB0EMGtDuykpVErw5aGrpqaheQ+Ys0jwg6EmxsYUNw8UOFHRYjFfQD4LcwBcFo8awAAAABJRU5ErkJggg==',
			'C624' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGRoCkMREWllbGR0dGpHFAhpFGlkbAlpRxBpEQOSUACT3Ra2aFrZqZVZUFJL7AhpEWxlaGR3Q9DY6TGEMDUGzwyEAi1scUMVAbmYNDUARG6jwoyLE4j4A+RDNolQMPxEAAAAASUVORK5CYII=',
			'74DE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QkMZWllDGUMDkEVbGaayNjo6MKCKhbI2BKKKTWF0RRKDuClq6dKlqyJDs5Dcx+gg0oqul7VBNNQVTUwEaAu6ugCQGJpbwGLobh6g8KMixOI+AJnSykW7Qu3cAAAAAElFTkSuQmCC',
			'EF70' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkNEQ11DA1qRxQIaREDkVAdMsYAAdLFGRwcRJPeFRk0NW7V0ZdY0JPeB1U1hhKlDiAVgijE6MGDYwdrAgOKW0BCwGIqbByr8qAixuA8A8RjNVhc99TcAAAAASUVORK5CYII=',
			'7E19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QkNFQxmmMEx1QBZtFWlgCGEICEATYwxhdBBBFpsC5E2Bi0HcFDU1bNW0VVFhSO4DqwDagayXtQEs1oAsJgIRQ7EjACKG4paABtFQxlAHVDcPUPhREWJxHwAuusrSf3F4ogAAAABJRU5ErkJggg==',
			'376C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RANEQx1CGaYGIIkFTGFodHR0CBBBVtnK0Oja4OjAgiw2haGVtYHRAdl9K6NWTVs6dWUWivumMASwAg1EsbmV0YG1IRBNjLUBJIZsR8AUkQZGNLeIgnhobh6o8KMixOI+AI5IysSsANVRAAAAAElFTkSuQmCC',
			'F2ED' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMZQ1hDHUMdkMQCGlhbWRsYHQJQxEQaXYFiIihiDMhiYCeFRq1aujR0ZdY0JPcB1U1hxdQbgCnG6IApxtqA6RbRUFc0Nw9U+FERYnEfAJB9y63ZvORyAAAAAElFTkSuQmCC',
			'DCB6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QgMYQ1lDGaY6IIkFTGFtdG10CAhAFmsVaXBtCHQQQBNjbXR0QHZf1NJpq5aGrkzNQnIfVB2GeaxA80Sw2CFCwC3Y3DxQ4UdFiMV9AImYzuXRWFQIAAAAAElFTkSuQmCC',
			'7ED5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7QkNFQ1lDGUMDkEVbRRpYGx0dGNDFGgJRxaaAxVwdkN0XNTVs6arIqCgk9zE6gNQFNIgg6WVtwBQTaYDYgSwGUsHa6BAQgCIGcjPDVIdBEH5UhFjcBwBJ7MvB+TWDWgAAAABJRU5ErkJggg==',
			'EEBF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAATUlEQVR4nGNYhQEaGAYTpIn7QkNEQ1lDGUNDkMQCGkQaWBsdHRjQxRoCMcUQ6sBOCo2aGrY0dGVoFpL7SDIPvx3IbkYRG6jwoyLE4j4ABeHLJmL8JVcAAAAASUVORK5CYII=',
			'B530' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7QgNEQxlDGVqRxQKmiDSwNjpMdUAWaxUBkQEBqOpCGBodHUSQ3BcaNXXpqqkrs6YhuS9gCkOjA0Id1DygWEMgmpgIUAzdDtZWdLeEBjCGoLt5oMKPihCL+wBVvs7Ca/5fygAAAABJRU5ErkJggg==',
			'74B5' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nM2QsRGAMAhFScEGcR8s0mNBk2lIkQ3MCCl0SpOOqKXehd9w7+B4B5yPUpgpv/iJQEZxwpZmKJhWgpEJ6jay3YU2F8j6xVqrHDEaP0c+YyL1Zhd1kaA8sNbnfsMy7iwR850JFJrgfx/mxe8CFlfLpJ+kEJkAAAAASUVORK5CYII=',
			'CC57' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WEMYQ1lDHUNDkMREWlkbXUE0klhAo0gDhhiQxzoVRCPcF7Vq2qqlmVkrs5DcFwDWFdDKgKYXSE5hwLAjIABZDOQWR0dHB3Q3M4QyoogNVPhREWJxHwA2dszDjXXTpwAAAABJRU5ErkJggg==',
			'A981' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgDWFsZHR2mIouJTBFpdG0ICEUWC2gVaXR0dIDpBTspaunSpVmhq5Yiuy+glTEQSR0YhoYygMxrRTWPBYsY2C1oYmA3hwYMgvCjIsTiPgBtg8y7iOUMtgAAAABJRU5ErkJggg==',
			'D995' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7QgMYQxhCGUMDkMQCprC2Mjo6OiCrC2gVaXRtCMQm5uqA5L6opUuXZmZGRkUhuS+glTHQISSgQQRFL0OjQwO6GEujI9AOEQy3OAQguw/iZoapDoMg/KgIsbgPAIrYzWsDHr68AAAAAElFTkSuQmCC',
			'3C06' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7RAMYQxmmMEx1QBILmMLa6BDKEBCArLJVpMHR0dFBAFlsikgDa0OgA7L7VkZNW7V0VWRqFrL7IOowzAPpFcFihwgBt2Bz80CFHxUhFvcBAPKIzAZ7N5rDAAAAAElFTkSuQmCC',
			'6BDA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGVqRxUSmiLSyNjpMdUASC2gRaXRtCAgIQBZrAKprCHQQQXJfZNTUsKWrIrOmIbkvZAqKOojeVpB5gaEhmGIo6iBucUQRg7iZEUVsoMKPihCL+wCRbM0sow0PSwAAAABJRU5ErkJggg==',
			'6EE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDHaY6IImJTBFpYG1gCAhAEgtoAYkxOgggizVAxJDdFxk1NWxp6MrULCT3hYDNY0Q1rxWiV4SAGDa3YHPzQIUfFSEW9wEAsMfLCftEo68AAAAASUVORK5CYII=',
			'1315' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1YQximMIYGIImxOoi0MoQwOiCrE3VgaHREEwPyWoF6XR2Q3Lcya1XYqmkro6KQ3AdRx9Aggqq30QGrGKMDqpgISG8AsvtEQ1hDGEMdpjoMgvCjIsTiPgCcDMgMehFU6gAAAABJRU5ErkJggg==',
			'82F7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDA0NDkMREprC2soJoJLGAVpFGVzQxkSkMYLEAJPctjVq1dGnoqpVZSO4DqpsCNK+VAcU8hgCg2BRUMUYHoFgAA4odrA2sDYwOqG4WDXVFExuo8KMixOI+AGzBy0FCv3FoAAAAAElFTkSuQmCC',
			'AB98' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7GB1EQxhCGaY6IImxBoi0Mjo6BAQgiYlMEWl0bQh0EEESC2gVaWVtCICpAzspaunUsJWZUVOzkNwHUscQEoBiXmioSKMDpnmNjljsQHdLQCummwcq/KgIsbgPAAfkzSxEjUztAAAAAElFTkSuQmCC',
			'A5CE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaklEQVR4nGNYhQEaGAYTpIn7GB1EQxlCHUMDkMRYA0SA4oEOyOpEpog0sDYIoogFtIqEsAJVIrsvaunUpUtXrQzNQnJfQCtDoytCHRiGhmKKAc0DiqHbwdqK7paAVsYQdDcPVPhREWJxHwDLasqUBXSL4gAAAABJRU5ErkJggg==',
			'056B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUMdkMRYA0QaGB0dHQKQxESmiDSwNjg6iCCJBbSKhLACTQhAcl/U0qlLl05dGZqF5L6AVoZGVzTzwGINgSjmAe3AEGMNYG1FdwujA2MIupsHKvyoCLG4DwB70srojqVY8gAAAABJRU5ErkJggg==',
			'42DB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpI37pjCGsIYyhjogi4WwtrI2OjoEIIkxhog0ujYEOoggibFOYQCLBSC5b9q0VUuXrooMzUJyX8AUhimsCHVgGBrKEMCKZh7QLQ6YYqwN6G5hmCIa6oru5oEKP+pBLO4DAMhHy+4eDM8ZAAAAAElFTkSuQmCC',
			'9885' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUMDkMREprC2Mjo6OiCrC2gVaXRtCEQTA6tzdUBy37SpK8NWha6MikJyH6srSJ1DgwiyzWDzAlDEBKB2iGC4xSEA2X0QNzNMdRgE4UdFiMV9AH5MyvvHTfj8AAAAAElFTkSuQmCC',
			'8945' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUMDkMREprC2MrQ6OiCrC2gVaXSYiiomMgUoFujo6oDkvqVRS5dmZmZGRSG5T2QKY6Bro0ODCIp5DI2uQFtRxVgaHRodHUTQ3dLoEIDsPoibHaY6DILwoyLE4j4ACFTNC71G/ZAAAAAASUVORK5CYII=',
			'BA10' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7QgMYAhimMLQiiwVMYQxhCGGY6oAs1sraChQNCEBRJ9LoMIXRQQTJfaFR01ZmgRCS+9DUQc0TDcUUA6nDZgeqW0IDRBodQx1Q3DxQ4UdFiMV9ANq+zefh0qO2AAAAAElFTkSuQmCC',
			'8E19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WANEQxmmMEx1QBITmSLSwBDCEBCAJBbQKtLAGMLoIIKubgpcDOykpVFTw1ZNWxUVhuQ+iDqGqSJo5gHFGrCIYbED1S0gNzOGOqC4eaDCj4oQi/sA7ILLVGqqzn4AAAAASUVORK5CYII=',
			'65F3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WANEQ1lDA0IdkMREpog0sDYwOgQgiQW0gMSAcshiDSIhrGAa4b7IqKlLl4auWpqF5L6QKQyNrgh1EL2tEDEU81pFMMREprC2oruFNYARZC+Kmwcq/KgIsbgPAOQizMUfsLsZAAAAAElFTkSuQmCC',
			'3CAB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7RAMYQxmmMIY6IIkFTGFtdAhldAhAVtkq0uDo6Ogggiw2RaSBtSEQpg7spJVR01YtXRUZmoXsPlR1cPNYQwNRzQOKuTagioHc4oqmF+RmoHkobh6o8KMixOI+AFhWzIcJCy+fAAAAAElFTkSuQmCC',
			'A0A1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMLQii7EGMIYwhDJMRRYTmcLayujoEIosFtAq0ugKJJHdF7V02spUIInsPjR1YBgaChQLRRULaGVtZW1AF2MMwRRjCACKhQYMgvCjIsTiPgCcIc0aCXuiOwAAAABJRU5ErkJggg==',
			'0DDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDGUMDkMRYA0RaWRsdHZDViUwRaXRtCEQRC2hFEQM7KWrptJWpqyJDs5Dch6YOpxg2O7C5BZubByr8qAixuA8AFSvLVFTRglcAAAAASUVORK5CYII=',
			'DB14' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QgNEQximMDQEIIkFTBFpZQhhaEQRaxVpdAxhaEUTawXqnRKA5L6opVPDVk1bFRWF5D6IOkYHdPMcpjCGhmCIYXELmhjIzYyhDihiAxV+VIRY3AcALerPf6ZRrBkAAAAASUVORK5CYII=',
			'92E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHaY6IImJTGFtZW1gCAhAEgtoFWl0bWB0EEERYwCKwdWBnTRt6qqlS0NXTc1Cch+rK8MUdPMYWhkCWNHME2hldEAXA7qlAV0va4BoqCuamwcq/KgIsbgPACgjyzk5UPyWAAAAAElFTkSuQmCC',
			'459F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpI37poiGMoQyhoYgi4WINDA6Ojogq2MEirE2BKKIsU4RCUESAztp2rSpS1dmRoZmIbkvYApDo0MIqt7QUKAYmnkMU0QaHTHEWFvR3cIwhTEE6GZUsYEKP+pBLO4DAMQJyXpbn7KMAAAAAElFTkSuQmCC',
			'6CE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDHaY6IImJTGFtdG1gCAhAEgtoEWlwbWB0EEAWaxBpYAWKIbsvMmraqqWhK1OzkNwXMgWsDtW8VoheETQxVzQxbG7B5uaBCj8qQizuAwAJQ8waCPO3+QAAAABJRU5ErkJggg==',
			'9ABA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGVqRxUSmMIawNjpMdUASC2hlbWVtCAgIQBETaXRtdHQQQXLftKnTVqaGrsyahuQ+VlcUdRDYKhrq2hAYGoIkJgAyryEQRZ3IFEy9rAFAsVBGVPMGKPyoCLG4DwDKCcy6yfJDDQAAAABJRU5ErkJggg==',
			'7C30' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7QkMZgZChFUW0lbXRtdFhqgOKmEiDQ0NAQACy2BSRBoZGRwcRZPdFTVu1aurKrGlI7mN0QFEHhqwNIF4giphIA6YdAQ2YbglowOLmAQo/KkIs7gMAnwDNcnRDTVcAAAAASUVORK5CYII='        
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