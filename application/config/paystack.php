<?php 
defined('BASEPATH') OR exit('No direct script access allowed'); 

// --------------------------------------------------------------
// Paystack Payment Library Configuration
// 
// You can obtain these API keys via the link below:
// @link https://dashboard.paystack.com/#/settings/developer
// --------------------------------------------------------------

// Test Public Key (for testing purposes)
$config['test_public_key'] = '';

// Test Secret key (for testing purposes)
$config['test_secret_key'] = '';

// Live Public Key (for production)
$config['live_public_key'] = '';

// Live Secret key (for production)
$config['live_secret_key'] = '';

// API Mode (Remember to update this settings in your paystack dashboard)
$config['api_mode'] = 'TEST'; // Change this to LIVE when you are ready to start receiving payments