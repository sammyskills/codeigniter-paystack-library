# CodeIgniter Paystack Library
A simple CodeIgniter 3.x library for accepting payments using Paystack https://paystack.com


### Usage
1. Download the `Paystack_lib.php` file and put it inside your `application/libraries` folder.

2. Get the __Public__ and __Secret__ keys _(both test and live)_ from your Paystack dashboard. [Click Here](https://dashboard.paystack.com/#/settings/developer).

3. There are two method you can use to store your API Keys, the __configuration file__ method _(simpler)_ OR the __database storage__ method _(highly recommended)_.  

    __Config File Method:__  
If you choose to use the configuration file method, download this file `config/paystack.php`, and paste your API keys there:

    ```
    $config['test_public_key'] = 'PASTE_YOUR_TEST_PUBLIC_KEY_HERE';  
    $config['test_secret_key'] = 'PASTE_YOUR_TEST_SECRET_KEY_HERE';   
    $config['live_public_key'] = 'PASTE_YOUR_LIVE_PUBLIC_KEY_HERE';    
    $config['live_secret_key'] = 'PASTE_YOUR_LIVE_SECRET_KEY_HERE';
    ```

    Make sure that the file is saved in your `application/config/` folder.

    __Database Storage (recommended)__:  
    Depending on how your database is structured, you can save the API keys in a table, and then fetch the details from that table.

4. In your controller that holds the page where you want the payment to be triggered, load the paystack library like so:

    `$this->load->library('paystack_lib');`

    Then add the email address, amount and callback url in the fields that will be sent to paystack's api, like so:

    ```
    $this->paystack_lib->add_field('email', 'THE CUSTOMER EMAIL ADDRESS');
    $this->paystack_lib->add_field('amount', 'TOTAL AMOUNT TO BE PAID'); // This amount MUST be multiplied by 100.
    $this->paystack_lib->add_field('callback_url', 'YOUR RETURN URL');
    ```

    Then, you can render the paystack form like so:
    
    `$this->paystack_lib->ps_auto_form();`

5. When the payment process is triggered, you should see a page like so:

![Payment Processing](../demo-images/payment-processing.PNG)

And then automatically redirect to Paystacks website to make payment, like so:

![Pay with Paystack](../demo-images/paystack-pay.PNG)

6. You need to verify the transaction to determine a failed or successful transaction. See `application/controllers/Sample_controller.php` for example.
