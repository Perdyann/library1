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
			'0497' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nM2QsQ2AMAwE3wUbhH2Sgv4L0rABTOHGG4QRUsCUiC4WlCD4706yfHrslyj+1Ff8JMKQJY8N64hVUtTQsFCQO6VjNBlOxsZvqrVu87QtjR8tGEYa3G2fo7LA/zBREt7FJKV44+zYV/s92Bu/A5sdyr9X9hDGAAAAAElFTkSuQmCC',
			'68B7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDGUNDkMREprC2sjY6NIggiQW0iDS6NgSgijVA1AUguS8yamXY0tBVK7OQ3BcCMa8V2d6AVrB5U7CIBTBguMXRAYubUcQGKvyoCLG4DwB+K80NL9MpXgAAAABJRU5ErkJggg==',
			'6946' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHaY6IImJTGFtZWh1CAhAEgtoEQGqcnQQQBZrAIoFOjoguy8yaunSzMzM1Cwk94VMYQx0bXRENa+VodE1NNBBBEWMpdGh0RFFDOyWRlS3YHPzQIUfFSEW9wEAqHHNaBe4V7AAAAAASUVORK5CYII=',
			'6233' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhDGUIdkMREprC2sjY6OgQgiQW0iDQ6NAQ0iCCLNTA0OoBFEe6LjFq1dNXUVUuzkNwXMoVhCgNCHURvK0MAA7p5rYwO6GJAtzSgu4U1QDTUEc3NAxV+VIRY3AcAjQLOEHVH1t4AAAAASUVORK5CYII=',
			'3ACE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7RAMYAhhCHUMDkMQCpjCGMDoEOqCobGVtZW0QRBWbItLo2sAIEwM7aWXUtJWpq1aGZiG7D1Ud1DzRUEwxkDpUOwKAeh3R3CIaINLogObmgQo/KkIs7gMAne7KZwTcjCYAAAAASUVORK5CYII=',
			'1587' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUNDkMRYHUQaGB0dGkSQxESBYqwNAShijA4iISB1AUjuW5k1demqUCCF5D5GB4ZGR0eHVlR7GRpdGwKmoIqJgMQCUMVYWxmBmpHFREMYQ4BuRhEbqPCjIsTiPgAaOcjLfATS3QAAAABJRU5ErkJggg==',
			'F010' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMZAhimMLQiiwU0MIYwhDBMdUARY20FigYEoIiJNDpMYXQQQXJfaNS0lVkghOQ+NHV4xFhbgW5BswNo6xR0tzAEMIY6oLh5oMKPihCL+wB9icyTH9eRnQAAAABJRU5ErkJggg==',
			'1225' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUMDkMRYHVhbGR0dHZDViTqINLo2BDqg6mVodGgIdHVAct/KrFVLV63MjIpCch9Q3RSGVoYGEVS9AUBRNDGQKKMDqhgrWC2y+0RDRENdQwOmOgyC8KMixOI+AKyKx981sd8uAAAAAElFTkSuQmCC',
			'FA09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7QkMZAhimMEx1QBILaGAMYQCKB6CIsbYyOjo6iKCIiTS6NgTCxMBOCo2atjJ1VVRUGJL7IOoCpqLqFQ11BcmgmQe0AsMOBwy3AMXQ3DxQ4UdFiMV9AOwyzfiqGwceAAAAAElFTkSuQmCC',
			'F29F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUNDkMQCGlhbGR0dHRhQxEQaXRsC0cQYkMXATgqNWrV0ZWZkaBaS+4DqpjCEYOgNYMAwj9GBEUOMtQHTLaKhDqGMKGIDFX5UhFjcBwB/ksq1Tyu69AAAAABJRU5ErkJggg==',
			'8FE6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7WANEQ11DHaY6IImJTBFpYG1gCAhAEgtoBYkxOghgqGN0QHbf0qipYUtDV6ZmIbkPqg6reSIExLC5hTUAKIbm5oEKPypCLO4DABv0y1j4vnBoAAAAAElFTkSuQmCC',
			'12CC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCHaYGIImxOrC2MjoEBIggiYk6iDS6Ngg6sKDoZQCKAUkk963MWrV0KYhEch9QxRRWhDqYWACmGKMDK4YdIFVobgkRDXVAc/NAhR8VIRb3AQDlrsfvbct3TQAAAABJRU5ErkJggg==',
			'2F84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7WANEQx1CGRoCkMREpog0MDo6NCKLBbSKNLACSWQxhlawuikByO6bNjVsVeiqqChk9wWA1Dk6IOtldACZFxgaguyWBrAdqG5pANuBIhYaKtLAgObmgQo/KkIs7gMAKuzM/s4VV/0AAAAASUVORK5CYII=',
			'0D09' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQximMEx1QBJjDRBpZQhlCAhAEhOZItLo6OjoIIIkFtAq0ujaEAgTAzspaum0lamroqLCkNwHURcwFVNvQIMIhh0OKHZgcws2Nw9U+FERYnEfADAkzERxZbX5AAAAAElFTkSuQmCC',
			'0227' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGUNDkMRYA1hbGR0dGkSQxESmiDS6NgSgiAW0MjQ6AMUCkNwXtXTV0lUrs1ZmIbkPqG4KQysQouoNAIpOYUCxg9EBKBrAgOoWkBsdUN0sGuoaGogiNlDhR0WIxX0AkKbKipV6T84AAAAASUVORK5CYII=',
			'3406' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7RAMYWhmmMEx1QBILAPIZQhkCApBVtjKEMjo6Ogggi01hdGVtCHRAdt/KqKVLl66KTM1Cdt8UkVagOjTzRENdgXpFUO1oBdkhguqWVnS3YHPzQIUfFSEW9wEAk7HK+37RZDMAAAAASUVORK5CYII=',
			'A4EB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7GB0YWllDHUMdkMRYAximsgJlApDERKYwhILERJDEAloZXZHUgZ0UtRQIQleGZiG5L6BVpBXdvNBQ0VBXDPMYWjHtYMDQCxZDc/NAhR8VIRb3AQAWJMrUqq71GgAAAABJRU5ErkJggg==',
			'A44A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7GB0YWhkaHVqRxVgDGKYytDpMdUASE5nCEMow1SEgAEksoJXRlSHQ0UEEyX1RS5cuXZmZmTUNyX0BrSKtrI1wdWAYGioa6hoaGBqCYh7ILajqiBUbqPCjIsTiPgB2PcyToSLFCgAAAABJRU5ErkJggg==',
			'0106' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YAhimMEx1QBJjDWAMYAhlCAhAEhOZAhR1dHQQQBILaGUIYG0IdEB2X9RSEIpMzUJyH1QdinkwvSIodjCA7RBBcQsDhlsYHVhD0d08UOFHRYjFfQDMMMi8cIeQHQAAAABJRU5ErkJggg==',
			'1DC3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHUIdkMRYHURaGR0CHQKQxEQdRBpdGwQaRFD0gsQYGgKQ3Lcya9rK1FWrlmYhuQ9NHYoYpnkYdmC6JQTTzQMVflSEWNwHAF4sysgonUd0AAAAAElFTkSuQmCC',
			'1BCA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7GB1EQxhCHVqRxVgdRFoZHQKmOiCJiTqINLo2CAQEoOgVaWUFkUjuW5k1NWwpkJyG5D40dTAxoHmMoSEYYoLo6oBuCUQREw0BudkRRWygwo+KEIv7AB4LyNESptrCAAAAAElFTkSuQmCC',
			'BED3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDGUIdkMQCpog0sDY6OgQgi7UCxRoCGkTQ1QHFApDcFxo1NWzpqqilWUjuQ1OH2zxcdqC5BZubByr8qAixuA8AjvbO5/FzRC4AAAAASUVORK5CYII=',
			'39C0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7RAMYQxhCHVqRxQKmsLYyOgRMdUBW2SrS6NogEBCALDYFJMboIILkvpVRS5emrlqZNQ3ZfVMYA5HUQc1jaMQUY8GwA5tbsLl5oMKPihCL+wAQgMwRGLTVyQAAAABJRU5ErkJggg==',
			'360A' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7RAMYQximMLQiiwVMYW1lCGWY6oCsslWkkdHRISAAWWyKSANrQ6CDCJL7VkZNC1u6KjJrGrL7poi2IqmDm+faEBgagibm6OiIog7iFkYUMYibUcUGKvyoCLG4DwDKtcr4ylXEgwAAAABJRU5ErkJggg==',
			'0F66' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1EQx1CGaY6IImxBog0MDo6BAQgiYlMEWlgbXB0EEASC2gFiTE6ILsvaunUsKVTV6ZmIbkPrM7REcU8iN5ABxEMO1DFsLmFEaQCzc0DFX5UhFjcBwDEgcsbUqLN4gAAAABJRU5ErkJggg==',
			'1661' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB0YQxhCGVqRxVgdWFsZHR2mIouJOog0sjY4hKLqFWlgbYDrBTtpZda0sKVTVy1Fdh+jg2grq6NDK5reRteGACLEwG5BERMNAbs5NGAQhB8VIRb3AQD8DskG3xk46gAAAABJRU5ErkJggg==',
			'313B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7RAMYAhhDGUMdkMQCpjAGsDY6OgQgq2xlDWBoCHQQQRabwhDAgFAHdtLKqFVRq6auDM1Cdh+qOqh5DJjmYRELAOpFd4toAGsoupsHKvyoCLG4DwAbHsm7BzQ6uAAAAABJRU5ErkJggg==',
			'9903' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7WAMYQximMIQ6IImJTGFtZQhldAhAEgtoFWl0dHRoEEETc20IaAhAct+0qUuXpq6KWpqF5D5WV8ZAJHUQ2MoA1otsnkArC4Yd2NyCzc0DFX5UhFjcBwD5vsy9NWaBRQAAAABJRU5ErkJggg==',
			'1321' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZ0lEQVR4nGNYhQEaGAYTpIn7GB1YQxhCGVqRxVgdRFoZHR2mIouJOjA0ujYEhKLqBekLgOkFO2ll1qowILEU2X1gda2odgDFGh2mYBELQBcDusUBVUw0hDWENTQgNGAQhB8VIRb3AQCtPMih30OxOQAAAABJRU5ErkJggg==',
			'D8D1' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAVklEQVR4nGNYhQEaGAYTpIn7QgMYQ1hDGVqRxQKmsLayNjpMRRFrFWl0bQgIRRUDqgOSyO6LWroybCmQRHYfmjpk8wiLQdyCIgZ1c2jAIAg/KkIs7gMAdh3O42TpJOIAAAAASUVORK5CYII=',
			'C4A9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WEMYWhmmMEx1QBITaWWYyhDKEBCAJBbQyBDK6OjoIIIs1sDoytoQCBMDOylq1dKlS1dFRYUhuS8AaCJrQ8BUVL2ioa6hQBlUO0DqUOwAugUkhuIWkJtB5iG7eaDCj4oQi/sAkovM0jRg26gAAAAASUVORK5CYII=',
			'B38C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCGaYGIIkFTBFpZXR0CBBBFmtlaHRtCHRgQVHHAFTn6IDsvtCoVWGrQldmIbsPTR2KedjEUO3AdAs2Nw9U+FERYnEfAEsKzFBn9G7UAAAAAElFTkSuQmCC',
			'1AFF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7GB0YAlhDA0NDkMRYHRhDWEEySGKiDqyt6GKMDiKNrggxsJNWZk1bmRq6MjQLyX1o6qBioqGYYtjUYYqJhmCKDVT4URFicR8AQGzGzpWx2VUAAAAASUVORK5CYII=',
			'4D4B' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpI37poiGMDQ6hjogi4WItDK0OjoEIIkxhog0Okx1dBBBEmOdAhQLhKsDO2natGkrMzMzQ7OQ3BcAVOfaiGpeaChQLDQQxTwGkHmNjuhirQxoerG6eaDCj3oQi/sAGwjNCxt/Wh4AAAAASUVORK5CYII=',
			'3216' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcklEQVR4nGNYhQEaGAYTpIn7RAMYQximMEx1QBILmMLayhDCEBCArLJVpNExhNFBAFlsCkOjwxRGB2T3rYxatXTVtJWpWcjumwKCjGjmMQQAxRxEUMSAZqGJAd0C0o+iVzRANNQx1AHFzQMVflSEWNwHAH6LyuVogGI7AAAAAElFTkSuQmCC',
			'0EF2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7GB1EQ1lDA6Y6IImxBog0sDYwBAQgiYlMAYkxOoggiQW0gtU1iCC5L2rp1LCloUAayX1QdY0OmHpbGTDsYJjCgMUtGG5uYAwNGQThR0WIxX0Ae9XKvFTO0ZsAAAAASUVORK5CYII=',
			'9CC0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7WAMYQxlCHVqRxUSmsDY6OgRMdUASC2gVaXBtEAgIQBNjbWB0EEFy37Sp01YtXbUyaxqS+1hdUdRBYCummAAWO7C5BZubByr8qAixuA8AbRvMSpSpBi4AAAAASUVORK5CYII=',
			'F86C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGaYGIIkFNLC2Mjo6BIigiIk0ujY4OrCgqWNtYHRAdl9o1MqwpVNXZiG7D6zO0dGBAcO8QKxi6HZgugXTzQMVflSEWNwHAGRjzHIXf8PIAAAAAElFTkSuQmCC',
			'1DE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7GB1EQ1hDHaY6IImxOoi0sjYwBAQgiYk6iDS6AlWLoOgFicHVgZ20MmvaytTQVVOzkNyHpg5JDJt5GGKYbgnBdPNAhR8VIRb3AQAKUMm2YlF8aAAAAABJRU5ErkJggg==',
			'F171' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QkMZAlhDA1qRxQIaGAOA5FRUMVaQWCiqGEMAQ6MDTC/YSaFRq6JWLQVCJPeB1U1haMXQG4ApxuiAKcbagC7GGgoUCw0YBOFHRYjFfQAmJcs34XUAFAAAAABJRU5ErkJggg==',
			'5CAF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMYQxmmMIaGIIkFNLA2OoQyOjCgiIk0ODo6oogFBog0sDYEwsTATgqbNm3V0lWRoVnI7mtFUYcQC0UVCwCKuaKpE5nC2oguxhrAGIph3gCFHxUhFvcBAP3RyzAmLqHvAAAAAElFTkSuQmCC',
			'61A6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7WAMYAhimMEx1QBITmcIYwBDKEBCAJBbQwhrA6OjoIIAs1sAQwNoQ6IDsvsioVVFLV0WmZiG5L2QKWB2qea1AsdBABxF0sQZUMRGw3gAUvaxAnUAxFDcPVPhREWJxHwDLG8qOdiZcLgAAAABJRU5ErkJggg==',
			'6C21' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7WAMYQxlCGVqRxUSmsDY6OjpMRRYLaBFpcG0ICEURaxABkTC9YCdFRk1btWpl1lJk94VMAaprRbUjoBUoNgVTzCEAi1scUMVAbmYNDQgNGAThR0WIxX0ArqbMk6b+vp4AAAAASUVORK5CYII=',
			'7219' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMZQximMEx1QBZtZW1lCGEICEARE2l0DGF0EEEWm8LQ6DAFLgZxU9SqpaumrYoKQ3IfowNQJdAOZL2sDQwBQLEGZDERiEoUOwJAKqeguiWgQTTUMdQB1c0DFH5UhFjcBwCfpcsf8KNhKwAAAABJRU5ErkJggg==',
			'C3E0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WEOAMNShFVlMpFWklbWBYaoDklhAI0OjawNDQACyWAMDUB2jgwiS+6JWrQpbGroyaxqS+9DUwcSA5qGJYbEDm1uwuXmgwo+KEIv7AMcay+NVIYmTAAAAAElFTkSuQmCC',
			'EAB3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUIdkMQCGhhDWBsdHQJQxFhbWYGkCIqYSKNro0NDAJL7QqOmrUwNXbU0C8l9aOqgYqKhrtjMw2oHqltCQ4BiaG4eqPCjIsTiPgAZO8+j/J1/rwAAAABJRU5ErkJggg==',
			'5A40' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QkMYAhgaHVqRxQIaGEMYWh2mOqCIsbYyTHUICEASCwwQaXQIdHQQQXJf2LRpKzMzM7OmIbuvVaTRtRGuDiomGuoaGogiFgBU59CIaofIFLAYiltYA8BiKG4eqPCjIsTiPgBIzs4Lfp5EFgAAAABJRU5ErkJggg==',
			'E171' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYElEQVR4nGNYhQEaGAYTpIn7QkMYAlhDA1qRxQIaGAOA5FRUMVaQWCiqGEMAQ6MDTC/YSaFRq6JWLQVCJPeB1U1haMXQG4ApxuiAKcbagCoWGsIaChQLDRgE4UdFiMV9AAEOyw/rCefqAAAAAElFTkSuQmCC',
			'E84F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMYQxgaHUNDkMQCGlhbGVodHRhQxEQaHaaiiwHVBcLFwE4KjVoZtjIzMzQLyX0gdayNmOa5hgZi2oGhDmgHmhjUzShiAxV+VIRY3AcA3NHL105boTEAAAAASUVORK5CYII=',
			'B2CA' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7QgMYQxhCHVqRxQKmsLYyOgRMdUAWaxVpdG0QCAhAUccAFGN0EEFyX2jUqqVLV63MmobkPqC6KawIdVDzGAKAYqEhKGKMDqwNgqjqwDoDUcRCA0RDHUIdUcQGKvyoCLG4DwAjR8y1DVJlBQAAAABJRU5ErkJggg==',
			'91FC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA6YGIImJTGEMYG1gCBBBEgtoZQWKMTqwoIgxgMWQ3Tdt6qqopaErs5Ddx+qKog4CWzHFBKBiyHaITGHAcAvQJaFAMRQ3D1T4URFicR8A2MjHys3WaTgAAAAASUVORK5CYII=',
			'6B9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMdkMREpoi0Mjo6OgQgiQW0iDS6NgQ6iCCLNYi0siLEwE6KjJoatjIzMmsakvtCgOYxhKDpbRVpdEA3DyjmiCaGzS3Y3DxQ4UdFiMV9AKSay80SGfiGAAAAAElFTkSuQmCC',
			'E6EC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkMYQ1hDHaYGIIkFNLC2sjYwBIigiIk0sjYwOrCgijWAxJDdFxo1LWxp6MosZPcFNIi2IqmDm+eKQwzVDky3YHPzQIUfFSEW9wEAQevLgIaATIsAAAAASUVORK5CYII=',
			'6A46' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdklEQVR4nGNYhQEaGAYTpIn7WAMYAhgaHaY6IImJTGEMYWh1CAhAEgtoYW1lmOroIIAs1iDS6BDo6IDsvsioaSszMzNTs5DcFzJFpNG10RHVvFbRUNfQQAcRFDGgeY2OKGIiU0BiqG5hDQCLobh5oMKPihCL+wDZvc3fW+8R0wAAAABJRU5ErkJggg==',
			'27AD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMIY6IImJTGFodAhldAhAEgtoZWh0dHR0EEHW3crQytoQCBODuGnaqmlLV0VmTUN2XwBDAJI6MGR0YHRgDUUVYwVDVDERIASJIbslNBQshuLmgQo/KkIs7gMA7AHLQgw9Oi4AAAAASUVORK5CYII=',
			'93E8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANYQ1hDHaY6IImJTBFpZW1gCAhAEgtoZWh0bWB0EEEVQ1YHdtK0qavCloaumpqF5D5WVwYM8xiwmCeARQybW7C5eaDCj4oQi/sAME/LUth1zusAAAAASUVORK5CYII=',
			'C0F6' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WEMYAlhDA6Y6IImJtDKGsDYwBAQgiQU0srayNjA6CCCLNYg0ugLFkN0XtWraytTQlalZSO6DqkM1D6pXBIsdIgTcAnZzAwOKmwcq/KgIsbgPAOxEyyykRVZVAAAAAElFTkSuQmCC',
			'6D8E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WANEQxhCGUMDkMREpoi0Mjo6OiCrC2gRaXRtCEQVaxBpdESoAzspMmrayqzQlaFZSO4LmYKiDqK3FYt5WMSwuQWbmwcq/KgIsbgPAKuxywXtOCIHAAAAAElFTkSuQmCC',
			'90FD' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYUlEQVR4nGNYhQEaGAYTpIn7WAMYAlhDA0MdkMREpjCGsDYwOgQgiQW0sraCxERQxEQaXRFiYCdNmzptZWroyqxpSO5jdUVRB4GtmGICWOzA5hawmxsYUdw8UOFHRYjFfQBQPsnGsyHn+wAAAABJRU5ErkJggg==',
			'1E67' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZUlEQVR4nGNYhQEaGAYTpIn7GB1EQxlCGUNDkMRYHUQaGB0dGkSQxESBYqwNqGKMYDGGhgAk963Mmhq2dCqQQnIfWJ2jQysDht6AKVjEAtDFGB0dHZDFREPAbkYRG6jwoyLE4j4AnYXIXixhGekAAAAASUVORK5CYII=',
			'0098' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YAhhCGaY6IImxBjCGMDo6BAQgiYlMYW1lbQh0EEESC2gVaXRtCICpAzspaum0lZmZUVOzkNwHUucQEoBiHlgMzTyQHYxoYtjcgs3NAxV+VIRY3AcAEnjLUv+sJCUAAAAASUVORK5CYII=',
			'6D29' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAc0lEQVR4nGNYhQEaGAYTpIn7WANEQxhCGaY6IImJTBFpZXR0CAhAEgtoEWl0bQh0EEEWaxBpdECIgZ0UGTVtZdbKrKgwJPeFTAGqa2WYiqK3FSg2BWgXulgAA4odYLc4MKC4BeRm1tAAFDcPVPhREWJxHwDeccy8MXg1LwAAAABJRU5ErkJggg==',
			'FF19' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7QkNFQx2mMEx1QBILaBBpYAhhCAhAE2MMYXQQQVc3BS4GdlJo1NSwVdNWRYUhuQ+ijmEqpl6GBixiWOzA4pZQBxQ3D1T4URFicR8Axe3Mz30gmjQAAAAASUVORK5CYII=',
			'61D0' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYAlhDGVqRxUSmMAawNjpMdUASC2hhDWBtCAgIQBZrAOptCHQQQXJfZNSqqKWrIrOmIbkvZAqKOojeVlxiqHaIgPSiuQXoklB0Nw9U+FERYnEfAMEsyxb4tPjrAAAAAElFTkSuQmCC',
			'0FDE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWUlEQVR4nGNYhQEaGAYTpIn7GB1EQ11DGUMDkMRYA0QaWBsdHZDViUwBijUEoogFtKKIgZ0UtXRq2NJVkaFZSO5DU4dTDJsd2NzC6AAUQ3PzQIUfFSEW9wEA6ZLKXUoUJ7cAAAAASUVORK5CYII=',
			'B3CB' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXklEQVR4nGNYhQEaGAYTpIn7QgNYQxhCHUMdkMQCpoi0MjoEOgQgi7UyNLo2CDqIoKhjaGVtYISpAzspNGpV2NJVK0OzkNyHpg7JPEZU87DagekWbG4eqPCjIsTiPgDOEsyvX1ltiwAAAABJRU5ErkJggg==',
			'D4D2' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpIn7QgMYWllDGaY6IIkFTGGYytroEBCALNbKEMraEOgggiLG6MraENAgguS+qKVAsCoKCBHuC2gVaQWqa0Sxo1U01BVkKqodIHVTGFDd0gpyC6abGUNDBkH4URFicR8APjDOnqylRi8AAAAASUVORK5CYII=',
			'2A8D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGUMdkMREpjCGMDo6OgQgiQW0srayNgQ6iCDrbhVpdASqE0F237RpK7NCV2ZNQ3ZfAIo6MGR0EA11RTOPtUGkEV1MpAGiF9ktoaEijQ5obh6o8KMixOI+AFdXywdUPuseAAAAAElFTkSuQmCC',
			'7FDC' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAW0lEQVR4nGNYhQEaGAYTpIn7QkNFQ11DGaYGIIu2ijSwNjoEiKCLNQQ6sCCLTYGIobgvamrY0lWRWcjuY3RAUQeGrA2YYiINmHYENGC6BSyG7uYBCj8qQizuAwCVesvKWDk+uwAAAABJRU5ErkJggg==',
			'C99F' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WEMYQxhCGUNDkMREWllbGR0dHZDVBTSKNLo2BKKKNaCIgZ0UtWrp0szMyNAsJPcFNDAGOoSg62VodEA3r5Gl0RFNDJtboG5GERuo8KMixOI+APmvykijSsVUAAAAAElFTkSuQmCC',
			'D1B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QgMYAlhDGUIdkMQCpjAGsDY6OgQgi7WyBrA2BDSIoIgB9TY6NAQguS9qKRCFrlqaheQ+NHUIMWzmoYtNYcBwSyjQxehuHqjwoyLE4j4AQN3ND1twmbMAAAAASUVORK5CYII=',
			'82EE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WAMYQ1hDHUMDkMREprC2sjYwOiCrC2gVaXRFExOZwoAsBnbS0qhVS5eGrgzNQnIfUN0UTPMYAjDFGB3QxYBuaUAXYw0QDXVFc/NAhR8VIRb3AQDX5cl3mwTRtwAAAABJRU5ErkJggg==',
			'0D4C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB1EQxgaHaYGIImxBoi0MrQ6BIggiYlMEQGqcnRgQRILaAWKBTo6ILsvaum0lZmZmVnI7gOpc22Eq0OIhQaiiIHtaES1A+yWRlS3YHPzQIUfFSEW9wEAiaPMfk8pZGwAAAAASUVORK5CYII=',
			'2068' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbElEQVR4nGNYhQEaGAYTpIn7WAMYAhhCGaY6IImJTGEMYXR0CAhAEgtoZW1lbXB0EEHW3SrS6NrAAFMHcdO0aStTp66amoXsvgCgOjTzGB1AegNRzGNtANmBKibSgOmW0FBMNw9U+FERYnEfAAJey0Zs9bMWAAAAAElFTkSuQmCC',
			'6F4D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAa0lEQVR4nGNYhQEaGAYTpIn7WANEQx0aHUMdkMREpog0MLQ6OgQgiQW0AMWmOjqIIIs1AHmBcDGwkyKjpoatzMzMmobkvhCgeayNaHpbgWKhgRhiDGjqwG5pRHULawBYDMXNAxV+VIRY3AcAgw7MdJqIsUkAAAAASUVORK5CYII=',
			'3E36' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7RANEQxlDGaY6IIkFTBFpYG10CAhAVtkqAiQDHQSQxYDqGBodHZDdtzJqatiqqStTs5DdB1GH1TwRAmLY3ILNzQMVflSEWNwHAPQLzAoFkd4hAAAAAElFTkSuQmCC',
			'0C2E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7GB0YQxlCGUMDkMRYA1gbHR0dHZDViUwRaXBtCEQRC2gVAZJwMbCTopZOW7VqZWZoFpL7wOpaGTH1TmHEsMMhAFUM7BYHVDGQm1lDA1HcPFDhR0WIxX0A6kPJf4OX0JsAAAAASUVORK5CYII=',
			'6F18' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7WANEQx2mMEx1QBITmSLSwBDCEBCAJBbQItLAGMLoIIIs1gDkTYGrAzspMmpq2Kppq6ZmIbkvZAqKOojeVpAYmnlYxESw6GUNALol1AHFzQMVflSEWNwHABPyzCzijOZsAAAAAElFTkSuQmCC',
			'2546' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAeElEQVR4nGNYhQEaGAYTpIn7WANEQxkaHaY6IImJTBFpYGh1CAhAEgtoBYpNdXQQQNbdKhLCEOjogOK+aVOXrszMTM1Cdl8AQ6NroyOKeYwOQLHQQAcRZLc0iDQ6NDqiiIk0sLYC3YeiNzSUMQTdzQMVflSEWNwHAEAXzFAAES0eAAAAAElFTkSuQmCC',
			'E892' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaElEQVR4nGNYhQEaGAYTpIn7QkMYQxhCGaY6IIkFNLC2Mjo6BASgiIk0ujYEOoigqWMFySC5LzRqZdjKzKhVUUjuA6ljCAlodEAzz6EhoJUBTcyxIWAKAxa3YLqZMTRkEIQfFSEW9wEA6VjNi3ztWAEAAAAASUVORK5CYII=',
			'4C9D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpI37pjCGMoQyhjogi4WwNjo6OjoEIIkxhog0uDYEOoggibFOEWlgRYiBnTRt2rRVKzMjs6YhuS8AqI4hBFVvaCiIhyrGAFTniCGG6Rasbh6o8KMexOI+ACBYy3Jojo17AAAAAElFTkSuQmCC',
			'2F20' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WANEQx1CGVqRxUSmiDQwOjpMdUASC2gVaWBtCAgIQNYNFGNoCHQQQXbftKlhq1ZmZk1Ddl8AUEUrI0wdGIJ5U1DFWBuAvAAGFDtEgJDRgQHFLaGhQLeEBqC4eaDCj4oQi/sAthrK6iDJXQEAAAAASUVORK5CYII=',
			'A6F9' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7GB0YQ1hDA6Y6IImxBrC2sjYwBAQgiYlMEWlkBaoWQRILaBVpQBIDOylq6bSwpaGrosKQ3BfQKgoybyqy3tBQkUZXkLmo5oHE0OzAdEtAK9DNQPOQ3TxQ4UdFiMV9AC6my71d6U8tAAAAAElFTkSuQmCC',
			'EADE' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAX0lEQVR4nGNYhQEaGAYTpIn7QkMYAlhDGUMDkMQCGhhDWBsdHRhQxFhbWRsC0cREGl0RYmAnhUZNW5m6KjI0C8l9aOqgYqKhmGLY1AHF0NwSGgIUQ3PzQIUfFSEW9wEArCLM4CeN9hkAAAAASUVORK5CYII=',
			'2BDF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAY0lEQVR4nGNYhQEaGAYTpIn7WANEQ1hDGUNDkMREpoi0sjY6OiCrC2gVaXRtCEQRY2gFqkOIQdw0bWrY0lWRoVnI7gtAUQeGjA6Y5rE2YIqJNGC6JTQU7GZUtwxQ+FERYnEfAAFxylgoB4J1AAAAAElFTkSuQmCC',
			'760E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAaUlEQVR4nGNYhQEaGAYTpIn7QkMZQximMIYGIIu2srYyhDI6oKhsFWlkdHREFZsi0sDaEAgTg7gpalrY0lWRoVlI7mN0EG1FUgeGrA0ija5oYiJAMUc0OwIaMN0S0IDFzQMUflSEWNwHAJL9yXeml5EgAAAAAElFTkSuQmCC',
			'9DE8' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXElEQVR4nGNYhQEaGAYTpIn7WANEQ1hDHaY6IImJTBFpZW1gCAhAEgtoFWl0bWB0EMEQg6sDO2na1GkrU0NXTc1Cch+rK4o6CMRingAWMWxuwebmgQo/KkIs7gMAbyfMTRKsu7MAAAAASUVORK5CYII=',
			'C9B3' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WEMYQ1hDGUIdkMREWllbWRsdHQKQxAIaRRpdGwIaRJDFGoBijQ4NAUjui1q1dGlq6KqlWUjuC2hgDERSBxVjwDSvkQVDDJtbsLl5oMKPihCL+wADOs51l2e9dAAAAABJRU5ErkJggg==',
			'094C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAcElEQVR4nGNYhQEaGAYTpIn7GB0YQxgaHaYGIImxBrC2MrQ6BIggiYlMEQGqcnRgQRILaAWKBTo6ILsvaunSpZmZmVnI7gtoZQx0bYSrg4oxNLqGBqKIiUxhaXRoRLUD7JZGVLdgc/NAhR8VIRb3AQDGiMvcc//W6gAAAABJRU5ErkJggg==',
			'E038' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAWklEQVR4nGNYhQEaGAYTpIn7QkMYAhhDGaY6IIkFNDCGsDY6BASgiLG2MjQEOoigiIk0OiDUgZ0UGjVtZdbUVVOzkNyHpg4hhmEeNjsw3YLNzQMVflSEWNwHAIczzhPUG5rXAAAAAElFTkSuQmCC',
			'8CF7' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAYklEQVR4nGNYhQEaGAYTpIn7WAMYQ1lDA0NDkMREprA2uoJoJLGAVpEGdDGRKSINrCA5JPctjZq2amnoqpVZSO6DqmtlQDMPKDYFXQxoRwBDA7pbGB0w3IwmNlDhR0WIxX0AVEPMBRnBo4wAAAAASUVORK5CYII=',
			'664E' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZklEQVR4nGNYhQEaGAYTpIn7WAMYQxgaHUMDkMREprC2MrQ6OiCrC2gRaWSYiibWINLAEAgXAzspMmpa2MrMzNAsJPeFTBFtZW1E09sq0ugaGogh5oCmDuwWNDFsbh6o8KMixOI+ANVAyzFGKVehAAAAAElFTkSuQmCC',
			'FB5D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7QkNFQ1hDHUMdkMQCGkRaWRsYHQJQxRpdgWIi6OqmwsXATgqNmhq2NDMzaxqS+0DqGBoC0fU2OmARc8UUa2V0dERzi2gIQygjipsHKvyoCLG4DwDIrMy8FQneRQAAAABJRU5ErkJggg==',
			'6263' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdElEQVR4nGNYhQEaGAYTpIn7WAMYQxhCGUIdkMREprC2Mjo6OgQgiQW0iDS6Njg0iCCLNTAAxcA03H2RUauWLp26amkWkvtCpjBMYXV0aEAxr5UhgBUogmJeK6MDuhjQLQ3obmENEA11QHPzQIUfFSEW9wEAPPLNCes51EIAAAAASUVORK5CYII=',
			'4E42' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbklEQVR4nGNYhQEaGAYTpI37poiGMjQ6THVAFgsRaWBodQgIQBJjBIlNdXQQQRJjnQLkBTo0iCC5b9q0qWErM7NWRSG5LwCojrXRoRHZjtBQoFhoQCuqW4AmNTpMwSIWgOlmx9CQwRB+1INY3AcAwD7Ms00bCJcAAAAASUVORK5CYII=',
			'5A84' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAdUlEQVR4nGNYhQEaGAYTpIn7QkMYAhhCGRoCkMQCGhhDGB0dGlHFWFtZGwJakcUCA0QaHR0dpgQguS9s2rSVWaGroqKQ3dcKUufogKyXoVU01LUhMDQE2Q6gOlegTcjqRKaA7UARYwXa64Dm5oEKPypCLO4DAOaMzpHFKm+/AAAAAElFTkSuQmCC',
			'88CF' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAXUlEQVR4nGNYhQEaGAYTpIn7WAMYQxhCHUNDkMREprC2MjoEOiCrC2gVaXRtEEQRA6ljbWCEiYGdtDRqZdjSVStDs5Dch6YOyTxsYph2oLsF6mYUsYEKPypCLO4DAFf2ydFxWjYDAAAAAElFTkSuQmCC',
			'D57C' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAb0lEQVR4nGNYhQEaGAYTpIn7QgNEQ1lDA6YGIIkFTBEBkQEiyGKtIF6gAwuqWAhDo6MDsvuilk5dumrpyixk9wW0MjQ6TGF0YEDRCxQLQBcTAZrGiGrHFNZW1gYGFLeEBjCGAMVQ3DxQ4UdFiMV9AJQxzRQb5HLjAAAAAElFTkSuQmCC',
			'8362' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAZElEQVR4nGNYhQEaGAYTpIn7WANYQxhCGaY6IImJTBFpZXR0CAhAEgtoZWh0bXB0EEFRx9DKCqKR3Lc0alXY0qmrVkUhuQ+sztGh0QHDPCCJKTaFAYtbMN3MGBoyCMKPihCL+wCYXcyG1FfQzgAAAABJRU5ErkJggg==',
			'F92D' => 'iVBORw0KGgoAAAANSUhEUgAAAEkAAAAhAgMAAADoum54AAAACVBMVEX///8AAADS0tIrj1xmAAAAbUlEQVR4nGNYhQEaGAYTpIn7QkMZQxhCGUMdkMQCGlhbGR0dHQJQxEQaXRsCHUTQxBwQYmAnhUYtXZq1MjNrGpL7AhoYAx1aGdH0MjQ6TEEXY2l0CEAXA7rFgRHNLYwhrKGBKG4eqPCjIsTiPgBAN8wx0K5KFwAAAABJRU5ErkJggg=='        
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