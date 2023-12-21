<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;

class ProductsController extends Controller {

    public function index() {
        $this->set("products", Products::get()->toArray());
    }

    public function add() {
        if ($this->request->isPost() && CSRF::post()) {
            $validation = Products::validate($this->request);

            if (!$validation['success']) {
                $this->set("error", $validation['error']);
            } else {
                $product = new Products;
                $product->fill($validation['data']);
                $saved = $product->save();

                if ($saved) {
                    $this->redirect("admin/products");
                    return;
                }
            }
        }

        $this->set("csrf_token", CSRF::token());
        $this->set("categories", Categories::get());
    }

    public function edit($id = null) {
        $product = Products::where("id", $id)->first();

        if ($this->request->isPost() && CSRF::post()) {
            $validation = Products::validate($this->request);

            if (!$validation['success']) {
                $this->set("error", $validation['error']);
            } else {
                $product->fill($validation['data']);
                $saved = $product->save();

                if ($saved) {
                    $this->redirect("admin/products");
                    return;
                }
            }
        }

        $this->set("product", $product);
        $this->set("categories", Categories::get());
        $this->set("csrf_token", CSRF::token());
    }

    public function delete($id = null) {
        $product = Products::where('id', $id)->first();

        if (!$product) {
            $this->setView("errors/show404");
            return;
        }

        if ($this->request->isPost() && CSRF::post()) {
            $product->delete();
            $this->request->redirect("admin/products");
            return;
        }

        $this->set("product", $product);
        $this->set("csrf_token", CSRF::token());
    }

    public function requireLogin() {
        return true;
    }

    public function isAdminPanel() {
        return true;
    }

}