<?php
use Fox\CSRF;
use Fox\Paginator;

class AdminController extends Controller {

    public function index() {
        $totals = Votes::getTotals();
        $days   = 14;

        $this->set("total_votes", $totals['total']);
        $this->set("unique_users", $totals['u_total']);
        $this->set("claimed", $totals['claimed']);
        $this->set("pending", $totals['pending']);
        $this->set("days", $days);
        $this->set("lastNDays", implode(",", Functions::getLastNDays($days, "d")));
        $this->set("vote_data", json_encode(array_values(Votes::getVoteData($days))));

        $this->set("callback_url", $this->router->getUrl().'callback');
    }

    public function voters($page = 1) {
        if ($this->request->hasQuery("search")) {
            $search = $this->filter($this->request->getQuery("search"));
            $search = str_replace("-", " ", $search);

            $this->set("search", $search);
            $voters = Votes::searchUsers($search);
        } else {
            $voters = Votes::getUsers();
        }

        if ($voters) {
            $paginator = (new Paginator($voters, $page, 15))->paginate();
            $results   = $paginator->getResults();

            $this->set("voters", $results);
        }
        return true;
    }

    public function votes($page = 1) {
        $votes_arr = [];

        if ($this->request->hasQuery("search")) {
            $search = $this->filter($this->request->getQuery("search"));
            $search = str_replace("-", " ", $search);

            $this->set("search", $search);
            $votes = Votes::searchVotes($search);
        } else {
            $votes = Votes::getVotes();
        }

        if ($votes) {
            $paginator = (new Paginator($votes, $page, 15))->paginate();
            $results   = $paginator->getResults();

            $this->set("votes", $results);
        }
        return true;
    }

    public function mfa() {
        $user_id  = $this->security->getUser()['id'];
        $username = $this->security->getUser()['username'];

        if ($this->security->getUser()['mfa_secret']) {
            if ($this->request->hasQuery("remove")) {
                $updated = Users::updateMfa($user_id, null);
                if ($updated) {
                    $this->redirect("admin/mfa");
                    exit;
                }
            }
        } else {
            try {
                $tfa = new RobThree\Auth\TwoFactorAuth(site_title);

                if ($this->request->isPost() /*&& CSRF::post()*/) {
                    $code   = $this->request->getPost("code");
                    $secret = $this->request->getPost("secret");

                    $verified = $tfa->verifyCode($secret, $code);

                    if ($verified) {
            
                        $updated = Users::updateMfa($user_id, $secret);
                        
                        if ($updated) {
                            $this->redirect("admin/mfa");
                            exit;
                        }

                        $this->set("error", "Failed to update user.");
                    } else {
                        $this->set("error", "Code failed.");
                    }
                }

                $secret = $tfa->createSecret();
                $qrcode = $tfa->getQRCodeImageAsDataUri($username, $secret);
                
                $this->set("auth_secret", $secret);
                $this->set("qr_code", $qrcode);
            } catch (Exception $e) {
                $this->set("error", $e);
            }
        }
    }

    public function beforeExecute() {
        parent::beforeExecute();

        $req_bearer = ['votes', 'voters'];

        if ($this->request->isPost() && in_array($this->getActionName(), $req_bearer)) {
            $this->disableView(true);
        }
    }

}