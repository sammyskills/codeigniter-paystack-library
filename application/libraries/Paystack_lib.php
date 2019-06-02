<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 

/**  
 * Paystack Standard Library for CodeIgniter 3.x
 * 
 * Library for Paystack payment gateway. This follows the Paystack
 * Standard form submission, which redirects customers to paystack's
 * secured page, where the payment is made.
 * 
 * This class handles submission of an order (purchasing of a package)
 * to Paystack, as well as processing Payment Verification process.
 * 
 * @package CodeIgniter
 * @subpackage Libraries
 * @category eCommerce (Payments)
 * @author Samuel Asor
 * @link https://github.com/Sammyskills/codeigniter-paystack-library
*/
class Paystack_lib
{
    public  $CI,
            $test_secret_key,
            $test_public_key,
            $live_secret_key,
            $live_public_key,
            $api_mode,
            $fields = array(),
            $submit_button;
    
    public function __construct()
    {
        // Access CI's native resources
        $this->CI =& get_instance();
        
        /** 
         * For Configuration file application/config/paystack
         * 
         * Comment the lines below if you're fetching from database instead
         */
        // Load the configuration file
        $this->CI->load->config('paystack');
        $this->test_secret_key = $this->CI->config->item('test_secret_key');
        $this->test_public_key = $this->CI->config->item('test_public_key');
        $this->live_secret_key = $this->CI->config->item('live_secret_key');
        $this->live_public_key = $this->CI->config->item('live_public_key');
        $this->api_mode = $this->CI->config->item('api_mode');

        // NOTE: YOU CAN ONLY USE EITHER OF THE TWO. DO NOT USE BOTH! COMMENT OR DELETE THE METHOD YOU DON'T NEED. //
        
        /** 
         * For Database Storage method
         * 
         * Comment the lines below if you are using the configuration file method
         * This is just an example. Fetch the data anyhow you want.
        */
        $this->test_secret_key = $this->CI->db->get_where('your_table_name',array('type'=>'paystack_test_secret_key'))->row()->value;
        $this->test_public_key = $this->CI->db->get_where('your_table_name',array('type'=>'paystack_test_public_key'))->row()->value;
        $this->live_secret_key = $this->CI->db->get_where('your_table_name',array('type'=>'paystack_live_secret_key'))->row()->value;
        $this->live_public_key = $this->CI->db->get_where('your_table_name',array('type'=>'paystack_live_public_key'))->row()->value;
        $this->api_mode = $this->CI->db->get_where('your_table_name',array('type'=>'paystack_api_mode'))->row()->value;

    }


    /**  
     * Adds a key => value pair to the fields array, which is
     * what will be sent as POST variables. Values that already exist
     * in the array will be overwritten.
    */
    public function add_field($name, $value)
    {
        $this->fields[$name] = $value;
    }


    /**  
     * Change the default text of the submit button
    */
    public function button($text)
    {
        $btn_data = array(
            'type' => 'submit',
            'content' => $text
        );

        $this->submit_button = form_button($btn_data);
    }


    /*  
     * Generate an entire HTML page consisting of a form with form action of
     * the authorization url, where the customer will make the payment. For ease
     * of use, this page is automatically redirect to payment page in 5 seconds,
     * via the BODY element's onLoad attribute.
    */
    public function ps_auto_form()
    {
        // Add text to the button
        $this->button('Click here if you\'re not automatically redirected...');

        // HTML
        echo '<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8">';
        echo '<meta name="viewport" content="width=device-width,initial-scale=1">';
        echo '<meta http-equiv="X-UA-Compatible" content="ie=edge"><title>Payment Processing</title>';
        echo '<link href="https://fonts.googleapis.com/css?family=Lato&display=swap" rel="stylesheet">';
        echo '<style>body{margin:0;padding:0;width:100%;height:100vh;display:flex;justify-content:center;align-items:center;font-family:Lato,sans-serif}.box{max-width:600px;width:100%;padding:10px 20px;margin:0 20px;position:absolute;top:20%;box-sizing:border-box;box-shadow:0 0 15px rgba(0,0,0,.2);border-radius:5px}h2{color:#4c4949}.button{width:100%;text-align:center}button{background-color:#09a5db;color:#fff;padding:10px 20px;border:none;border-radius:5px;font-weight:700;cursor:pointer;font-family:Lato,sans-serif;font-size:1rem;margin:15px 0;transition:background-color 250ms ease-in-out}button:hover{background-color:#524f4f}</style>';
        echo '</head><body><div class="box"><h2>';
        echo 'Please wait, your order is being processed. You will be redirected to Paystack\'s website...';
        echo '</h2><div class="button">';
        echo $this->ps_form('psAutoForm');
        echo '</div></div>';
        echo '<script>window.onload=function(){window.setTimeout(function(){document.psAutoForm.submit()},5e3)};</script>';
        echo '</body></html>';

    }


    /**  
     * The actual form that is submitted to Paystack
    */
    public function ps_form($form_name = 'psForm')
    {
        $output = '';
        $output .= '<form name="'.$form_name.'" action="'.$this->get_authorization_url().'" method="post" class="text-center">';        
        $output .= $this->submit_button;
        $output .= form_close();
        
        return $output;
    }



    /** 
     * Generate Authorization URL
     * 
     * Using Paystack's Standand, we use cURL to
     * generate an authorization URL, which we would
     * redirect customers to, for actual payment.
    */
    public function get_authorization_url()
    {
        // Declare post data as an empty array
        $post_data = array();

        // Add all input fields into the post data array
        foreach ($this->fields as $name => $value)
            $post_data[$name] = $value;

        // Declare result as an empty array
        $result = array();

        // Paystack initialize url
        $url = "https://api.paystack.co/transaction/initialize";

        // Perform cURL operation
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,json_encode($post_data));  //Post Fields
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        // Secret key
        $s_key = ( $this->api_mode == 'TEST' ) ? $this->test_secret_key : $this->live_secret_key;

        // Set headers
        $headers = [
            'Authorization: Bearer '. $s_key,
            'Content-Type: application/json',

        ];
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $request = curl_exec($ch);

        curl_close($ch);

        // Check result
        if ($request) 
        {
            $result = json_decode($request, true);
        }
        
        //Use the $result array to get redirect URL
        return $result['data']['authorization_url'];
    }



    /**  
     * Verify Transaction
     * 
     * Verify the transaction by query Paystack's API using the
     * reference supplied before checkout.
    */
    public function verify_transaction($reference)
    {
        $s_key = ( $this->api_mode == 'TEST' ) ? $this->test_secret_key : $this->live_secret_key;
        
        $context = stream_context_create(
            array(
                'http' => array(
                        'method'=>"GET",
                        'header'=>"Authorization: Bearer " .  $s_key,
                    )
            )
        );
        $url = 'https://api.paystack.co/transaction/verify/'. rawurlencode($reference);
        $request = file_get_contents($url, false, $context);
        return json_decode($request, true);
        
        
    }

    /**  
     * Log Error Message
    */
    public function log_error($message)
    {
        log_message('Error', $message);
    }
}
