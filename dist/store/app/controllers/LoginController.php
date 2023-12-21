<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;

class LoginController extends Controller {

    public function index() {
        if ($this->request->isPost()) {
            $username = $this->request->getPost("username", "string");
            $password = $this->request->getPost("password");

            if ($username != admin_username || $password != admin_password) {
                $this->set("error", "Invalid username or password.");
            } else {
                $address = $this->request->getAddress();
                $session_key = Functions::generateString(30);
                $session = new Sessions;

                $session->fill([
                    'username'   => $username,
                    'sess_id'    => $session_key,
                    'ip_address' => $address,
                    'created'    => time(),
                    'expires'    => time() + 86400,
                ]);

                $session->save();

                $this->cookies->set("session_key", $session_key);
                $this->redirect("admin");
                exit;
            }
        }
        
        $this->set("csrf_token", CSRF::token());
    }

}