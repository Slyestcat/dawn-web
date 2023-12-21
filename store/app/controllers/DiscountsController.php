<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;
use Fox\Paginator;

class DiscountsController extends Controller {

    public function index() {
        $this->set("discounts", DiscountCodes::get());
    }

    public function add() {
        if ($this->request->isPost() && CSRF::post()) {
            $code = $this->request->getPost("code", "string");
            $exp  = $this->request->getPost("expires", "int");
            $perc = $this->request->getPost("percentage", "int");

            if ($exp < 1) {
                $this->set("error", "expire time must be greater than 0.");
            } else if ($perc < 1 || $perc > 99) {
                $this->set("error", "Percentage must be between 1 and 99");
            } else if (strlen($code) < 3 || strlen($code) > 15) {
                $this->set("error", "Code must be between 3 and 15 characters");
            } else {
                $discount = new DiscountCodes;
                $discount->code = $code;
                $discount->percentage = $perc;
                $discount->expires = time() + $exp;
                $updated = $discount->save();
    
                if ($updated) {
                    $this->redirect("admin/discounts");
                    return;
                }
            }
        }

        $this->set("csrf_token", CSRF::token());
    }

    public function delete($id) {
        $discount = DiscountCodes::where('id', $id)->first();

        if (!$discount) {
            $this->setView("errors/show404");
            return;
        }

        if ($this->request->isPost() && CSRF::post()) {
            $discount->delete();
            $this->request->redirect("admin/discounts");
            return;
        }

        $this->set("discount", $discount);
        $this->set("csrf_token", CSRF::token());
    }
    
    public function requireLogin() {
        return true;
    }

    public function isAdminPanel() {
        return true;
    }

}