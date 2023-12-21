<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Router\Router;

class PageRouter extends Router {

    private static $instance;

    public static function getInstance() {
        if (!self::$instance) {
            self::$instance = new PageRouter(web_root);
        }
        return self::$instance;
    }

    private $controller;
    private $method;
    private $params;

    public $route_paths = [];

    public function initRoutes() {
        

        /**
         * Store
         */
        $this->all('', function() {
            return $this->setRoute('index', 'index', ['catId' => null, 'page' => 1]);
        });
        
        $this->all('success', function() {
            return $this->setRoute('index', 'success'); 
        });

        $this->all('checkout', function() {
            return $this->setRoute('index', 'checkout');
        });
        
        $this->all('stripe', function() {
            return $this->setRoute('index', 'stripe');
        });

        $this->all('ipn', function() {
            return $this->setRoute('index', 'ipn');
        });
        
        $this->all('ipn_stripe', function() {
            return $this->setRoute('index', 'ipn_stripe'); 
        });
        
        $this->all('([0-9]+)', function($page) {
            return $this->setRoute('index', 'index', ['id' => null, 'page' => $page]);
        });
        
        $this->all('([0-9]+)-([A-Za-z0-9\-]+)', function($id, $title) {
            return $this->setRoute('index', 'index', ['id' => $id, 'page' => 1]);
        });

        $this->all('([0-9]+)-([A-Za-z0-9\-]+)/([0-9]+)', function($id, $title, $page) {
            return $this->setRoute('index', 'index', ['id' => $id, 'page' => $page]);
        });

        /**
         * Cart Stoof
         */
        $this->post('cart', function() {
            return $this->setRoute('cart', 'index');
        });

        $this->post('cart/add', function() {
            return $this->setRoute('cart', 'add');
        });

        $this->post('cart/delete', function() {
            return $this->setRoute('cart', 'delete');
        });

        /**
         * Admin Pages
         */
        $this->all('admin', function() {
            return $this->setRoute('admin', 'index');
        });

        $this->all('login', function() {
            return $this->setRoute('login', 'index');
        });
        
        /**
         * Payments
         */
        $this->all('admin/payments', function() {
            return $this->setRoute('payments', 'index');
        });

        $this->all('admin/payments/([0-9]+)', function($page) {
            return $this->setRoute('payments', 'index', ['username' => null, 'page' => $page]);
        });

        $this->all('admin/payments/([A-Za-z0-9 ]+)', function($username) {
            return $this->setRoute('payments', 'index', ['username' => $username, 'page' => 1]);
        });

        $this->all('admin/payments/([A-Za-z0-9 ]+)/([0-9]+)', function($username, $page) {
            return $this->setRoute('payments', 'index', ['username' => $username, 'page' => $page]);
        });

        $this->all('admin/payments/add', function() {
            return $this->setRoute('payments', 'add');
        });

        /**
         * Products
         */
        $this->all('admin/products', function() {
            return $this->setRoute('products', 'index');
        });

        $this->all('admin/products/add', function() {
            return $this->setRoute('products', 'add');
        });

        $this->all('admin/products/edit/([0-9]+)', function($id) {
            return $this->setRoute('products', 'edit', ['id' => $id]);
        });

        $this->all('admin/products/delete/([0-9]+)', function($id) {
            return $this->setRoute('products', 'delete', ['id' => $id]);
        });

        /**
         * Categories
         */
        $this->all('admin/categories', function() {
            return $this->setRoute('categories', 'index');
        });

        $this->all('admin/categories/add', function() {
            return $this->setRoute('categories', 'add');
        });

        $this->all('admin/categories/edit/([0-9]+)', function($id) {
            return $this->setRoute('categories', 'edit', ['id' => $id]);
        });

        $this->all('admin/categories/delete/([0-9]+)', function($id) {
            return $this->setRoute('categories', 'delete', ['id' => $id]);
        });

        /**
         * Discounts
         */
        $this->all('admin/discounts', function() {
            return $this->setRoute('discounts', 'index');
        });

        $this->all('admin/discounts/add', function() {
            return $this->setRoute('discounts', 'add');
        });

        $this->all('admin/discounts/delete/([0-9]+)', function($id) {
            return $this->setRoute('discounts', 'delete', ['id' => $id]);
        });

        /**
         * Sessions
         */
        $this->all('admin/sessions', function() {
            return $this->setRoute('sessions', 'index');
        });
        
        $this->all('admin/sessions/([0-9]+)', function($page) {
            return $this->setRoute('sessions', 'index', ['address' =>null, 'page' => $page]);
        });

        $this->all('admin/sessions/search', function() {
            return $this->setRoute('sessions', 'index', ['address' => null, 'page' => 1]);
        });

        $this->all('admin/sessions/search/([A-Za-z0-9\-.]+)', function($address) {
            return $this->setRoute('sessions', 'index', ['address' => $address, 'page' => 1]);
        });

        $this->all('admin/sessions/search/([A-Za-z0-9\-]+)/([0-9]+)', function($address, $page) {
            return $this->setRoute('sessions', 'index', ['address' => $address, 'page' => $page]);
        });

    }

    public function setRoute($controller, $method, $params = []) {
        $this->controller = $controller;
        $this->method = $method;
        $this->params = $params;

        return [ $controller, $method, $params ];
    }

    public function getController($formatted = false) {
        return $formatted ? ucfirst($this->controller).'Controller' : $this->controller;
    }

    public function getViewPath() {
        return $this->getController().'/'.$this->getMethod();
    }

    public function getMethod() {
        return $this->method;
    }

    public function getParams() {
        return $this->params;
    }

    public function isSecure() {
        return
          (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
            || $_SERVER['SERVER_PORT'] == 443;
    }

    public function getUrl() {
        $baseUrl =  'http'.($this->isSecure() ? 's' : '').'://' . $_SERVER['HTTP_HOST'];
        return $baseUrl.web_root;
    }
}