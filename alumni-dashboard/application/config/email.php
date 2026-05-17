<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Email Configuration (SMTP)
|--------------------------------------------------------------------------
|
| These settings are loaded by the Auth controller whenever it needs
| to send a verification or password-reset email.
|
| For Gmail:
|   1. Use an App Password (not your regular Gmail password).
|      Google Account → Security → 2-Step Verification → App Passwords
|   2. Set smtp_host to 'smtp.gmail.com'
|   3. Set smtp_port to 587
|   4. Set smtp_crypto to 'tls'
|
| All sensitive values should be moved to a .env file in production
| and read via getenv(). They are hard-coded here for coursework
| simplicity — add .env to .gitignore if you later use one.
|
*/
$config['protocol']    = 'smtp';
$config['smtp_host']   = getenv('SMTP_HOST');
$config['smtp_port']   = getenv('SMTP_PORT');
$config['smtp_crypto'] = 'tls';
$config['smtp_user']   = getenv('SMTP_USER');
$config['smtp_pass']   = getenv('SMTP_PASS');
$config['mailtype']    = 'html';
$config['charset']     = 'utf-8';
$config['newline']     = "\r\n";