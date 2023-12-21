<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;
use Fox\Paginator;

class PaymentsController extends Controller {

    public function index($username = null, $page = 1) {
        if ($this->request->isPost() && CSRF::post()) {
            $username = $this->request->getPost("username", "string");
            $username = str_replace(" ", "+", $username);
            $this->redirect("admin/payments/".$username);
            return;
        }

        if ($username == null) {
            $payments = Payments::get();
        } else {
            $payments = Payments::where("player_name", 'LIKE', "%$username%")->get();

            if (!$payments || empty($payments) || count($payments) == 0) {
                $payments = Payments::get();
                $this->set("error", "Your query returned 0 results. Did you type the name correctly?");
            } else {
                $this->set("success", "Your query returned ".count($payments)." results.");
            }
        }
        
        $paginator = (new Paginator($payments->toArray(), $page, 15))->paginate();
        $results   = $paginator->getResults();

        $this->set("payments", $results);
        $this->set("csrf_token", CSRF::token());
    }

    public function requireLogin() {
        return true;
    }

    public function isAdminPanel() {
        return true;
    }

}