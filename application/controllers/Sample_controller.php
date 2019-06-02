<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

/** 
 * Sample Controller
 * 
 * An example class to show how to configure 
 * your controller for Paystack payments.
*/
class Sample_controller extends CI_Controller
{
    
    /**  
     * class construct
    */
    public function __construct()
    {
        // Load parent construct
        parent::__construct();

        // Load required libraries, models, helpers, etc.
        $this->load->library('paystack_lib'); // the paystack library
        $this->load->helper('string'); // to generate random string
        
    }



    /**  
     * Index
     * 
     * Maps to the following URL
	 * 		http://example.com/
	 *	- or -
	 * 		http://example.com/index.php/
     * 
     * This initializes payment to paystack's page.
    */
    public function index()
    {
        // Payment amount (to be multiplied by 100)
        $payment_amount = 500 * 100;

        // User email. Ideally, this will be fetched dynamically
        $user_email = "sammyskills@gmail.com";
        
        // Payment reference. That is, if you do not want to use paystack's supplied reference.
        $payment_reference = random_string('md5');
        
        // Add fields to Paystack form
        $this->paystack_lib->add_field('email', $user_email); // user email (required)
        $this->paystack_lib->add_field('amount', $payment_amount); // amount (required)

        $this->paystack_lib->add_field('callback_url', base_url('paystack-verify')); // callback (required for verifaction)
        $this->paystack_lib->add_field('reference', $payment_reference); // only if you do not want to use reference provided by paystack
        

        // Render Paystack form
        $this->paystack_lib->ps_auto_form();
    }



    /**  
     * Verify
     * 
     * Maps to the following URL
	 * 		http://example.com/paystack-verify
	 *	- or -
	 * 		http://example.com/index.php/paystack-verify
     * 
     * This method handles the verification of payments from 
     * the paystack api.
    */
    public function verify()
    {
        // Check if trxref or reference is passed in the url
        if ( $this->input->get('trxref') OR $this->input->get('reference') )
        {
            // Valid url, store reference in variable
            $reference = ($this->input->get('trxref')) ? $this->input->get('trxref') : $this->input->get('reference');

            // Callback paystack to get real transaction status
            $ps_api_response = $this->paystack_lib->verify_transaction($reference);

            /**  
             * Check API response
            */
            if (array_key_exists('data', $ps_api_response) && array_key_exists('status', $ps_api_response['data']) && ($ps_api_response['data']['status'] === 'success')) 
            {
                
                // Payment was successful

                // Redirect to success page

            } 
            else
            {
                // Payment was not successful

                // Redirect to error page
            } 
            
        }
        else 
        {
            // Redirect to dashboard or 404 (as you choose)
            redirect(base_url());
        }
    }


}
