<?php
/**
 * Plugin Name:     Validate Code
 * Plugin URI:      http://www.chekked.com/
 * Description:     Add validate code
 * Author:          index SYSTEMS
 * Author URI:      http://www.chekked.com/
 * Text Domain:     validate-code
 * Version:         1.0.0
 * @package         validate_code
 */
    register_activation_hook( __FILE__, 'code_create_db' );
    function code_create_db() {
        // Create DB Here
        global $wpdb;
	    $charset_collate = $wpdb->get_charset_collate();
        $table_name = $wpdb->prefix . 'codes';
        $sql = "CREATE TABLE $table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
        code varchar(15) NOT NULL,	
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,			
		UNIQUE KEY id (id)
	    ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
    }

 add_action('admin_menu', 'validate_code');
 function validate_code() { 
  add_menu_page( 
      'Add Member', 
      'Add Member', 
      'edit_posts', 
      'add_code', 
      'add_code_function', 
      'dashicons-media-spreadsheet' 
     );
}

function add_code_function()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'codes';

    ?>
<style>
.form-inline {
    display: flex;
    flex-flow: row wrap;
    align-items: center;
}

/* Add some margins for each label */
.form-inline label {
    margin: 5px 10px 5px 0;
}

table td:nth(1) {
    width: 250px;
    font-size: 16px;
    font-weight: 500;
}

/* Style the input fields */
.form-inline input {
    vertical-align: middle;
    margin: 5px 10px 5px 0;
    padding: 5px;
    background-color: #fff;
    border: 1px solid #ddd;
}

/* Style the submit button */
.form-inline button {
    padding: 10px 20px;
    background-color: dodgerblue;
    border: 1px solid #ddd;
    color: white;
}

.form-inline button:hover {
    background-color: royalblue;
}

/* Add responsiveness - display the form controls vertically instead of horizontally on screens that are less than 800px wide */
@media (max-width: 800px) {
    .form-inline input {
        margin: 10px 0;
    }

    .form-inline {
        flex-direction: column;
        align-items: stretch;
    }
}

.code_list {
    margin-top: 40px
}
#tableList td{width:150px;text-align:center}
</style>
<div class="add_code">
    <h2> Add Member </h2>
    <form class="form-inline" id="add_code" method="post" action="<?php echo admin_url('admin-ajax.php'); ?>">

        <input type="text" id="name" placeholder="Enter Name" name="code" required>
        <input type="text" id="location" placeholder="Enter Location" name="location" required>
        <input type="date" id="join_date" placeholder="Enter Join Date" name="join_date" required>
        <input type="checkbox" id="escrow" name="escrow" value="yes"> <label>Yes escrow</label>
        <button type="submit">Submit</button>
    </form>
</div>

<div class="code_list">
    <h2>Member List</h2>
    <table id="tableList">
        <tr>
            <th>Code</th>
            <th> Name </th>
            <th> Location </th>
            <th> Join Date </th>
            <th> Escrow </th>
        </tr>    
        <?php $results = $wpdb->get_results( "SELECT * FROM $table_name");
           foreach ($results as $result){   
         ?>
        <tr>
            <td style="border-bottom:1px solid #ccc"><?php echo $result->code ?></td>
            <td style="border-bottom:1px solid #ccc"><?php echo $result->name ?></td>
            <td style="border-bottom:1px solid #ccc"><?php echo $result->location ?></td>
            <td style="border-bottom:1px solid #ccc"><?php echo date('d-m-Y',strtotime($result->time)) ?></td>
            <td style="border-bottom:1px solid #ccc"><?php echo $result->escrow ?></td>
            <td class="remove" data-id="<?php echo $result->id ?>"><svg xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 101 101" width="22px" height="22px">
                    <path
                        d="M50.5 16.4c-18.8 0-34.1 15.3-34.1 34.1s15.3 34.1 34.1 34.1 34.1-15.3 34.1-34.1-15.3-34.1-34.1-34.1zm0 63.4c-16.1 0-29.3-13.1-29.3-29.3s13.1-29.3 29.3-29.3 29.3 13.1 29.3 29.3-13.2 29.3-29.3 29.3z" />
                    <path
                        d="M66.2 47.8H34.8c-1.3 0-2.4 1.1-2.4 2.4s1.1 2.4 2.4 2.4h31.4c1.3 0 2.4-1.1 2.4-2.4s-1.1-2.4-2.4-2.4z" />
                </svg></td>

        </tr>
        <?php } ?>
    </table>
</div>
<script>

jQuery('#add_code').submit(function(e) {
    e.preventDefault();
    var name = jQuery("#name").val();
    var location = jQuery("#location").val();
    var join_date = jQuery("#join_date").val();
    if(jQuery('#escrow').prop('checked'))
    {
        var escrow = 'Yes';
    }
    else
    {
        var escrow = 'No';
    }
    jQuery.ajax({
        data: {
            action: 'addcode',
            name: name,
            location: location,
            join_date: join_date,
            escrow: escrow,
        },
        dataType: 'json',
        type: 'post',
        url: ajaxurl,
        success: function(data) {
            // console.log(data); //should print out the name since you sent it along
            jQuery('#tableList').append('<tr><td style="border-bottom:1px solid #ccc">' + data.code + '</td><td style="border-bottom:1px solid #ccc">'+ name +'</td><td style="border-bottom:1px solid #ccc">'+location+'</td><td style="border-bottom:1px solid #ccc">'+data.join_date+'</td><td style="border-bottom:1px solid #ccc">'+data.escrow+'</td><td class="remove" data-id="' + data + '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 101 101" width="22px" height="22px"><path d="M50.5 16.4c-18.8 0-34.1 15.3-34.1 34.1s15.3 34.1 34.1 34.1 34.1-15.3 34.1-34.1-15.3-34.1-34.1-34.1zm0 63.4c-16.1 0-29.3-13.1-29.3-29.3s13.1-29.3 29.3-29.3 29.3 13.1 29.3 29.3-13.2 29.3-29.3 29.3z" /><path d="M66.2 47.8H34.8c-1.3 0-2.4 1.1-2.4 2.4s1.1 2.4 2.4 2.4h31.4c1.3 0 2.4-1.1 2.4-2.4s-1.1-2.4-2.4-2.4z" /></svg></td></tr>'
                );
            jQuery("#code").val('');
        }
    });

});
jQuery('body').on('click', '.remove',function() {

    var id = jQuery(this).attr('data-id');
    jQuery(this).parent().remove();
    jQuery.get(ajaxurl, {
            'action': 'deletecode',
            'id': id,
        },
        function(msg) {

        });

})
</script>

<?php
}

add_action('wp_ajax_addcode', 'addd_code');
add_action('wp_ajax_nopriv_addcode', 'addd_code');

function addd_code()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'codes';
    $six_digit_random_number = random_int(100000, 999999);
    $code = 'CHECK-'.$six_digit_random_number;
    $wpdb->insert($table_name, array('code'=> $code,'name'=>$_REQUEST['name'],'location'=>$_REQUEST['location'],'time'=>$_REQUEST['join_date'],'escrow'=> $_REQUEST['escrow']));
    $lastid = $wpdb->insert_id;
    $data['code'] = $code;
    $data['id'] =  $lastid;
    $data['join_date'] = date('d-m-Y',strtotime($_REQUEST['join_date']));
    $data['escrow'] = $_REQUEST['escrow'];
    echo json_encode($data);
    die();
}

add_action('wp_ajax_deletecode', 'delete_code');
add_action('wp_ajax_nopriv_deletecode', 'delete_code');
function delete_code()
{
    global $wpdb; 
	$table_name = $wpdb->prefix . 'codes';
	$id = $_REQUEST['id'];
	$wpdb->delete( $table_name, array( 'id' => $id ) );
    echo $id;
    die();
}

add_action('wp_ajax_validateCode', 'check_code');
add_action('wp_ajax_nopriv_validateCode', 'check_code');
function check_code()
{
    global $wpdb; 
	$table_name = $wpdb->prefix . 'codes';
	$code = $_REQUEST['code'];
	$results = $wpdb->get_results( "SELECT * FROM $table_name WHERE code = '".$_REQUEST['code']."'");	
    foreach($results as $result)
    {
         $data['join_date'] = date('d-m-Y',strtotime($result->time));
         $data['name'] = $result->name;
         $data['location'] = $result->location;
         $data['code']=$result->code;
         $data['escrow']=$result->escrow;
    }
    if(count($results) == 0) {
        $data['validate'] = false;
        echo json_encode($data);
    }else{
        $data['validate'] = true;
        echo json_encode($data);
   
    }    
    die();
}

add_shortcode('validate-form','validateForm');
function validateForm()
{
    ob_start();
    ?>

<div class="elementor-element elementor-element-21780007 elementor-widget__width-initial elementor-widget-mobile__width-inherit elementor-widget elementor-widget-bdt-mailchimp animated fadeInUp"
    data-id="21780007" data-element_type="widget"
    data-settings="{&quot;_animation&quot;:&quot;fadeInUp&quot;,&quot;_animation_delay&quot;:600}"
    data-widget_type="bdt-mailchimp.default">
    <div class="elementor-widget-container">
        <div class="bdt-newsletter-wrapper">
            <form action="" class="bdt-grid bdt-flex-middle" id="check_code_form">
                <div class="bdt-newsletter-input-wrapper bdt-width-expand bdt-first-column">
                    <input type="text" id="code" name="code" placeholder="11 Digit membership " required="" class="bdt-input"
                        data-com.bitwarden.browser.user-edited="yes">
                    <input type="hidden" name="action" value="validateCode">
                </div>
                <div class="bdt-newsletter-signup-wrapper bdt-width-auto">
                    <button type="submit" class="bdt-newsletter-btn bdt-button bdt-button-primary bdt-width-1-1">
                        <div class="bdt-newsletter-btn-content-wrapper  ">
                            <div id="btn-validate-code" class="bdt-newsletter-btn-text bdt-display-inline-block">VERIFY</div>
                        </div>
                    </button>
                </div>
            </form>
            
        </div>
        
    </div>
</div>
<div class="response"></div>
<script>
    
    jQuery('body').on('click','.close',function(){
        jQuery('#myModal').hide();
        jQuery('#myModal').remove();
    })
    jQuery('#check_code_form').submit(function(e) {
     e.preventDefault();
     var code = jQuery("#code").val();
     jQuery.ajax({
        data: {
            action: 'validateCode',
            code: code
        },
        dataType: 'json',
        type: 'post',
        url: wc_add_to_cart_params.ajax_url,
        success: function(msg) {
            // console.log(data); //should print out the name since you sent it along
            console.log(msg);
            if(msg.validate)
            {
               // var html = '<ul><li>Name: '+msg.name+'</li><li>Location: '+msg.location+' </li><li>Join Date: '+msg.join_date+' </li></ul>';

                var html = '<div id="myModal" class="modal"><div class="modal-content"><span class="close">&times;</span> <h2>Pass <i class="fa-exclamation fa"></i></h2><h4>'+msg.name+'</h4><span class="green">'+ code +'</span><p>Confirmed Location - <span>'+msg.location+'</span></p> <p>Membership Since - <span>'+msg.join_date+'</span></p><p>Will accept escrow - <span>'+msg.escrow+'</span></div></div>';
        
                jQuery('.response').html(html);
                jQuery('#myModal').show();

            }
            else
            {
                var html = '<div id="myModal" class="modal"><div class="modal-content"><span class="close">&times;</span> <h2>Fail <i class="fa-exclamation fa" style="color:red"></i></h2><h3>This is not a valid Membership number</h3></div></div>';
                jQuery('.response').html(html);
                  jQuery('#myModal').show();
            }
            
            
        }
    });
});
</script>
<style>
.response{color:#fff;text-align:center}
/* The Modal (background) */
.modal {
  display: none; /* Hidden by default */
  position: fixed; /* Stay in place */
  z-index: 1; /* Sit on top */
  left: 0;
  top: 0;
  width: 100%; /* Full width */
  height: 100%; /* Full height */
  overflow: auto; /* Enable scroll if needed */
  background-color: rgb(0,0,0); /* Fallback color */
  background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}

/* Modal Content/Box */
.modal-content {
  background-color: #fefefe;
  margin: 15% auto; /* 15% from the top and centered */
  padding: 20px;
  border: 1px solid #888;
  width: 40%; /* Could be more or less, depending on screen size */
}

/* The Close Button */
.close {
  color: #aaa;
  float: right;
  font-size: 28px;
  font-weight: bold;
}

.close:hover,
.close:focus {
  color: black;
  text-decoration: none;
  cursor: pointer;
}
.modal-content h2{font-size: 22px;font-weight: 600;color: #000;}
.modal-content h3 {font-size: 32px;
    letter-spacing: -1px;
    font-weight: 600;
    line-height: 36px;}
.modal-content .green{display: block;    
    margin-top: -20px;
    color: green;}
.modal-content p {color:#888}
.modal-content p span{color:#000}
</style>    
<?php
$output = ob_get_contents();
    ob_end_clean();
    return $output;
}