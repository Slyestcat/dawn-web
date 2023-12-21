<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

class AccountController extends Controller {

    public function index() {
        
    }
    
    public function requireLogin() {
        return true;
    }
    
}