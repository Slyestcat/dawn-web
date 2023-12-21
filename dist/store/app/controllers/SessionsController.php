<?php
use Fox\Paginator;
use Fox\CSRF;

class SessionsController extends Controller {
    
    public function index($address = null, $page = 1) {
        if ($this->request->isPost() && CSRF::post()) {
            $address = $this->request->getPost("address", "string");
            $address = str_replace(" ", "+", $address);
            $this->redirect("admin/sessions/search/".$address);
            return;
        }
        
        if ($this->request->hasQuery("del")) {
            $id   = $this->request->getQuery("del", "int");
            $sess = Sessions::where('id', $id)->first(); 

            if ($sess->sess_id == $this->cookies->get("session_key")
                    && $this->request->getAddress() == $sess->ip_address) {
                $this->set("error", "Can not delete your own session. Use sign out button instead?");
            } else {
                $sess->delete();
                $this->redirect("admin/sessions");
                exit;
            }
        }

        if ($address == null) {
            $sessions = Sessions::get();
        } else {
            $sessions = Sessions::where("ip_address", 'LIKE', "%$address%")->get();

            if (!$sessions || empty($sessions) || count($sessions) == 0) {
                $sessions = Sessions::get();
                $this->set("error", "Your query returned 0 results. Did you type the name correctly?");
            } else {
                $this->set("success", "Your query returned ".count($sessions)." results.");
            }
        }

        $paginator = (new Paginator($sessions->toArray(), $page, 15))->paginate();
        $results   = $paginator->getResults();

        $this->set("sessions", $results);
        $this->set("csrf_token", CSRF::token());
    }

    public function requireLogin() {
        return true;
    }

    public function isAdminPanel() {
        return true;
    }
}