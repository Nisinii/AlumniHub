<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * MY_Security — Core Security Override
 */
class MY_Security extends CI_Security {

    public function csrf_verify()
    {
        $is_api_request = (
            (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'json') !== FALSE) ||
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'json') !== FALSE) ||
            (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'multipart/form-data') !== FALSE) ||
            (isset($_GET['format']) && $_GET['format'] === 'json') ||
            (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest')
        );

        if ($is_api_request) {
            return $this;
        }

        return parent::csrf_verify();
    }
}