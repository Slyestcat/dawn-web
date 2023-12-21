<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\Cookies;
use Fox\Request;
use Router\Route;
use Router\Router;

class Security {

    private static $instance;

    /**
     * @param $controller
     * @return Security
     */
    public static function getInstance($controller) {
        if (!self::$instance) {
            self::$instance = new Security($controller);
        }
        return self::$instance;
    }

    private $is_root;
    private $user;
    private $controller;
    private $bridge;
    private $cookies;
    private $router;
    private $request;

    /**
     * Security constructor.
     * @param Controller $controller
     */
    public function __construct(Controller $controller) {
        $this->controller = $controller;
        $this->cookies    = $controller->getCookies();
        $this->router     = $controller->getRouter();
        $this->request    = $controller->getRequest();
    }

    /**
     * Verifies if a user has access to certain pages.
     * @return bool
     */
    public function checkAccess() {
        $controller = $this->router->getController();
        $action     = $this->router->getMethod();

        $omit = [
            'vote' => ['index', 'buttons', 'vote', 'callback'],
            'api'  => ['users', 'votes'],
        ];

        if (in_array($controller, array_keys($omit))) {
            if (in_array($action, $omit[$controller])) {
                return true;
            }
        }
        
        if (!$this->controller->requireLogin()) {
            return true;
        }

        $session_key = $this->cookies->get("session_key");

        if (!$session_key) {
            if ($action != "login") {
                $this->controller->redirect("login");
                return false;
            }
            return false;
        }

        $session = Sessions::where("sess_id", $session_key)->first();

        if (!$session || $session->expires < time()) {
            $this->cookies->delete("session_key");
            $this->controller->redirect("login");
            return false;
        }

        $this->controller->set("controller", $controller);
        $this->controller->set("action", $action);
        return true;
    }

    public function getUser() {
        return $this->user;
    }

    public function isRoot() {
        return $this->is_root;
    }
}