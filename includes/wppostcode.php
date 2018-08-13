<?php
/**
* Bloack direct access.
*
* @since  0.0.1
*/
if(!defined('ABSPATH')){
	exit;
}
class PCPC_API{
	private $options;
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	public function __construct(){
		add_shortcode( 'postcode_calculator',array($this,'calculate_postcode_price'));
		add_action('admin_menu',array($this,'add_menu'));
		add_action('admin_init',array($this,'page_init'));

        add_action('wp_ajax_wp_pcpc_ajax',array($this,'wp_pcpc_ajax'));
        add_action('wp_ajax_nopriv_wp_pcpc_ajax',array($this,'wp_pcpc_ajax'));
	}
	public function add_menu(){
	//	add_menu_page('Postcode','PCPC','administrator','postcode-price-calculator',array($this,'settings_page'),'dashicons-location-alt');
		add_options_page('Postcode','PCPC','administrator','postcode-price-calculator',array($this,'settings_page'));
	}
	public function settings_page(){
		$this->options = get_option('wppcpc_options');
		?>
		<div class="wrap"><h1>Postcode Price Calculator <sup>Settings</sup></h1>
			<form method="post" action="options.php">
			<?php
				settings_fields('pcpc_option_group');
				do_settings_sections('postcode-price-calculator');
				submit_button();
			?>
			</form>
		</div>
		
		<?php
	}
	public function page_init(){
        register_setting('pcpc_option_group','wppcpc_options',array($this,'sanitize'));
        add_settings_section('pcpc_setting_section_id','',array($this,'print_section_info'),'postcode-price-calculator');
        add_settings_field('start_postcode',	'Start Postcode (Start Point)',	array($this,'start_postcode_callback'),	'postcode-price-calculator','pcpc_setting_section_id');
        add_settings_field('base_price',		'Base Price',					array($this,'base_price_callback'),		'postcode-price-calculator','pcpc_setting_section_id');
        add_settings_field('price_per_mile',	'Price Per Mile',				array($this,'price_per_mile_callback'),	'postcode-price-calculator','pcpc_setting_section_id');
        add_settings_field('currency',			'Currency',						array($this,'currency_callback'),		'postcode-price-calculator','pcpc_setting_section_id');
    }
    public function wp_pcpc_ajax(){
        if($_POST){
            $act    =   $_POST['act'];
            if($act=='do_calculate'){
                $options        =   get_option('wppcpc_options');
                $start_postcode =   $options['start_postcode'];
                $base_price     =   $options['base_price'];
                $price_per_mile =   $options['price_per_mile'];
                $currency       =   $options['currency'];
                $end_postcode   =   $_POST['postcode'];
                if($end_postcode){
                    if(Postcode::validate($end_postcode)==true){
                        $distance       =   Postcode::distance($start_postcode,$end_postcode,'M');
                        $price          =   PCPC_API::get_price($distance,$price_per_mile,$base_price);
                        echo json_encode(array('status'=>'success','msg'=>'','result'=>array('distance'=>$distance,'price'=>$price,'currency'=>$currency)));
                    }
                    else{
                        echo json_encode(array('status'=>'error','msg'=>'Invalid Postcode.'));
                    }
                }
                else{
                    echo json_encode(array('status'=>'error','msg'=>'Please add a postcode')); 
                }
            }
        }
        die();
    }
  
    public function get_price($distance,$price_per_mile,$base_price){
        $price          =   $distance * $price_per_mile + $base_price;
        return $price;
    }
	public function calculate_postcode_price(){
        $options        =   get_option('wppcpc_options');
        $start_postcode =   $options['start_postcode'];
        
        ob_start();
        
        if($start_postcode){
            ?>
            <div id="pcpcform">

                <div class="pcpcpdvi">
                    <label>Enter postcode</label><a href="javascript:void(0)" class="wppcpc_pcpcbtn">Calculate</a>
                    <input type="text" name="postcode" class="wppcpc_postcode">
                </div>
                <div class="pcpcpdvi wppcpc_result_distance">
                    
                </div>
                <div class="pcpcpdvi wppcpc_result_cost">
                    
                </div>
                


            </div>
            <?php    
        }
		else{
            echo 'You need to add starting postalcode from Wp-Admin undert settings (PCPC)';
        }
        $o=ob_get_contents();
        ob_end_clean();
        return $o;
	}
	/** 
     * Print the Section text
     */
    public function print_section_info(){ print 'Enter your settings below:'; }
	/**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
	public function sanitize($input){
        $new_input = array();
		if( isset( $input['start_postcode'] ) )
            $new_input['start_postcode'] 	= sanitize_text_field( $input['start_postcode'] );
        if( isset( $input['base_price'] ) )
            $new_input['base_price'] 		= absint( $input['base_price'] );
        if( isset( $input['price_per_mile'] ) )
            $new_input['price_per_mile'] 	= absint( $input['price_per_mile'] );
        if( isset( $input['currency'] ) )
            $new_input['currency'] 			= sanitize_text_field( $input['currency'] );
        return $new_input;
    }
    public function start_postcode_callback(){
        printf('<input type="text" id="start_postcode" name="wppcpc_options[start_postcode]" value="%s" />',
            isset( $this->options['start_postcode'] ) ? esc_attr( $this->options['start_postcode']) : ''
        );
    }
    public function base_price_callback(){
        printf('<input type="text" id="base_price" name="wppcpc_options[base_price]" value="%s" />',
            isset( $this->options['base_price'] ) ? esc_attr( $this->options['base_price']) : ''
        );
    }
    public function price_per_mile_callback(){
        printf('<input type="text" id="price_per_mile" name="wppcpc_options[price_per_mile]" value="%s" />',
            isset( $this->options['price_per_mile'] ) ? esc_attr( $this->options['price_per_mile']) : ''
        );
    }
    public function currency_callback(){
        printf('<input type="text" id="currency" name="wppcpc_options[currency]" value="%s" />',
            isset( $this->options['currency'] ) ? esc_attr( $this->options['currency']) : ''
        );
    }

}
add_action('plugins_loaded',array('PCPC_API','init'));