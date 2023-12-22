<?php
use Fox\CSRF;

class IndexController extends Controller {

    public function index($site_id = null) {
        if ($this->cookies->has("vote_user")) {
            $this->vote($site_id);
            $this->setView("index/vote");
            return;
        }

        if ($this->request->isPost() && $this->request->hasPost("username") && csrf::post()) {
            $username = trim($this->filter($this->request->getPost("username")));

            if (strlen($username) < 1 || strlen($username) > 20) {
                $this->set("error", "Username must be between 1 and 20 characters.");
            } else {
                setcookie("vote_user", $username, time() + 86400, web_root);
                $this->redirect("");
                exit;
            }
        }
        //$top  = Votes::topVoters();
        
        /*
         * Setting the usernames for the top 3 voters
        */
        $top_voter_1 = (!empty($top[0]['username']) ? $top[0]['username'] : 'Vote to get here');
        $top_voter_2 = (!empty($top[1]['username']) ? $top[1]['username'] : 'Vote to get here');
        $top_voter_3 = (!empty($top[2]['username']) ? $top[2]['username'] : 'Vote to get here');
        $this->set("top_1_user", $top_voter_1);
        $this->set("top_2_user", $top_voter_2);
        $this->set("top_3_user", $top_voter_3);
        /*
         * Setting the top 3 voters votes per user
        */
        
        $top_total_1 = (!empty($top[0]['total']) ? $top[0]['total'] : 0);
        $top_total_2 = (!empty($top[1]['total']) ? $top[1]['total'] : 0);
        $top_total_3 = (!empty($top[2]['total']) ? $top[2]['total'] : 0);
        
        $this->set("top_1_votes", $top_total_1);
        $this->set("top_2_votes", $top_total_2);
        $this->set("top_3_votes", $top_total_3);
        
        $this->set("month", date('F'));
        $this->set("csrf_token", CSRF::token());
    }

    public function vote($site_id = null) {
        if (!$this->cookies->has("vote_user")) {
            $this->redirect("");
            exit;
        }

        if ($this->request->hasQuery("reset")) {
            setcookie("vote_user", "", time() - 10, web_root);
            $this->redirect("");
            exit;
        }

        $name  = $this->filter($this->cookies->get("vote_user"));

        if ($site_id != null) {
            $site_id = $this->filterInt($site_id);
            $link    = VoteLinks::getLink($site_id);

            if (!$link) {
                $this->redirect("");
                exit;
            }

            $lastVote = Votes::getLatestVote($name, $site_id);

            $timeDiff = time() - $lastVote['voted_on'];

            if ($timeDiff < 43200) {
                $future_date = new DateTime(date('Y-m-d H:i:s', $lastVote['voted_on'] + 43200));
                $interval    = $future_date->diff(new DateTime());
                $time_format = $interval->format("%h hours, %i minutes, %s seconds");

                $this->set("error", "You can vote on {$link['title']} in another $time_format");
                $this->setView("index/vote");
                $this->set("vote_link", $link);
                $this->set("vote_user", $name);
                return;
            }

            $active = Votes::getActiveVote($name);

            if ($active) {
                $vote_key = $active['vote_key'];
            } else {
                $vote_key = Functions::generateString();

                Votes::createVote($name, $this->request->getAddress(), $vote_key, $link['id']);
            }

            $replace = str_replace(['{sid}', '{incentive}'], ['%s', '%s'], $link['url']);
            $siteUrl = sprintf($replace, $link['site_id'], $vote_key);
            $bob = "Bob";
			$this->set("vote_link", $link);
            $this->setView("index/redirect");
            $this->delayedRedirect($siteUrl, 1);
            return;
        }
        //$top  = Votes::topVoters();
        
        /*
         * Setting the usernames for the top 3 voters
        */
        $this->set("top_1_user", $top[0]['username']);
        $this->set("top_2_user", $top[1]['username']);
        $this->set("top_3_user", $top[2]['username']);
        /*
         * Setting the top 3 voters votes per user
        */
        $this->set("top_1_votes", $top[0]['total']);
        $this->set("top_2_votes", $top[1]['total']);
        $this->set("top_3_votes", $top[2]['total']);
        
        $this->set("month", date('F'));
        $this->set("vote_user", $name);
    }

    public function callback() {
        $this->disableView(true);

        if (empty($_POST) && empty($_GET)) {
            return [
                'success' => false,
                'message' => 'No parameters were defined.'
            ];
        }

        $request = $this->request->getRequest();
        $vote    = null;

        foreach ($request as $req) {
            $req  = $this->filter($req);

            if (preg_match("/^[A-Za-z0-9]{15}+$/", $req)) {

                $vote = Votes::getVote($req);

                if ($vote) {
                    break;
                }
            }
        }

        if ($vote) {
            $id   = $vote['id'];
            $time = time();
            $complete = Votes::completeVote($id, $time);

            if ($complete) {
                return [
                    'success' => true,
                    'message' => 'Thanks for voting for us!'
                ];
            }
        }

       return [
            'success' => false,
            'message' => 'Callback failed. Code already used.'
       ];
    }

    public function beforeExecute() {
        parent::beforeExecute();

        if ($this->cookies->has("vote_user")) {
            $name = $this->filter($this->cookies->get("vote_user"));
            setcookie("vote_user", $name, time() + 86400, web_root);
        }
    }

    public function buttons() {
        $links    = VoteLinks::getVoteLinks();
        $name     = $this->filter($this->cookies->get("vote_user"));
        $linksArr = [];

        foreach ($links as $link) {
            $lastVote = Votes::getLatestVote($name, $link['id']);

            if ($lastVote) {
                $future_date = new DateTime(date('Y-m-d H:i:s', $lastVote['voted_on'] + 43200));
                $interval    = $future_date->diff(new DateTime());
                $time_format = $interval->format("%hh, %im, %ss");

                $link['time_left'] = $time_format;
            }

            $linksArr[] = $link;
        }

        $this->set("sites", $linksArr);
    }
}