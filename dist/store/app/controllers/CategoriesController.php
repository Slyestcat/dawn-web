<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;

class CategoriesController extends Controller {

    public function index() {
        $categories = Categories::select(['*'])
            ->selectRaw("(SELECT COUNT(*) FROM products WHERE products.category = categories.id) AS products")
            ->get();

        $this->set("categories", $categories);
    }

    public function add() {
        if ($this->request->isPost() && CSRF::post()) {
            $newTitle = $this->request->getPost("title", "string");

            if (strlen($newTitle) < 3 || strlen($newTitle) > 100) {
                $this->set("error", "Title must be between 3 and 100 characters.");
            } else {
                $category = new Categories;
                $category->title = $newTitle;
                $saved = $category->save();

                if ($saved) {
                    $this->redirect("admin/categories");
                    return;
                }
            }
        }

        $this->set("csrf_token", CSRF::token());
    }

    public function edit($id = null) {
        if (isset($_SESSION['success'])) {
            $this->set("success", $_SESSION['success']);
            unset($_SESSION['success']);
        }

        $category = Categories::where('id', $id)->first();

        if (!$category) {
            $this->setView("errors/show404");
            return;
        }

        if ($this->request->isPost() && CSRF::post()) {
            $newTitle = $this->request->getPost("title", "string");

            if (strlen($newTitle) < 3 || strlen($newTitle) > 100) {
                $this->set("error", "Title must be between 3 and 100 characters.");
            } else {
                $category->title = $newTitle;
                $updated = $category->save();

                if ($updated) {
                    $_SESSION['success'] = "Category has been updated.";
                    $this->redirect("admin/categories");
                    return;
                }
            }
        }

        $this->set("category", $category);
        $this->set("csrf_token", CSRF::token());
    }

    public function delete($id = null) {
        $category = Categories::where("id", $id)->first();

        if (!$category) {
            $this->setView("errors/show404");
            return;
        }

        if ($this->request->isPost() && CSRF::post()) {
            $category->delete();
            $this->request->redirect("admin/categories");
            return;
        }

        $this->set("category", $category);
        $this->set("csrf_token", CSRF::token());
    }

    public function requireLogin() {
        return true;
    }

    public function isAdminPanel() {
        return true;
    }

}