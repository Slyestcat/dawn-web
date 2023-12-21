<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;
use Fox\Paginator;

class IndexController extends Controller {
    public function getRank($total) {
        switch($total) {
            case $total >= 10 && $total < 50:
                return 'https://primalps.net/highscores/assets/img/5.png';
                break;
            case $total >= 50 && $total < 100:
                return "https://primalps.net/highscores/assets/img/6.png";
                break;
            case $total >= 100 && $total < 250:
                return "https://primalps.net/highscores/assets/img/7.png";
                break;
            case $total >= 250 && $total < 1000:
                return "https://primalps.net/highscores/assets/img/8.png";
                break;
            case $total >= 1000:
                return "https://primalps.net/highscores/assets/img/9.png";
                break;
        }
    }
    public function index($catId = null, $page = 1) {
        if ($this->request->hasPost("code") && CSRF::post()) {
            $code = $this->request->getPost("code", "string");

            $discount = DiscountCodes::where('code', $code)->first();

            if ($discount) {
                if ($discount->expires < time()) {
                    $this->set("error", "The discount code you've entered has expired.");
                } else {
                    $this->cookies->set("discount", $discount->code);
                    return $this->request->redirect("");
                }
            } else {
                $this->set("error", "The discount code you've entered is not valid.");
            }
        }
        
        if ($this->cookies->has("discount")) {
            $code     = $this->cookies->get("discount");
            $discount = DiscountCodes::where('code', $code)->first();

            if (!$discount) {
                $this->cookies->delete("discount");
                return $this->request->redirect("");
            }

            $this->set("discount", $discount);
        }

        if ($this->request->hasPost("store_name") && CSRF::post()) {
            $store_name = $this->request->getPost("store_name", 'string');
            $this->cookies->set("store_username", $store_name);
            return $this->request->redirect("");
        }

        if ($this->request->hasQuery("reset")) {
            $this->cookies->delete("store_username");
            return $this->request->redirect("");
        }

        if ($this->request->hasQuery("removeDiscount")) {
            $this->cookies->delete("discount");
            return $this->request->redirect("");
        }

        if ($catId == null) {
            $products = Products::get();
        } else {
            $category = Categories::where('id', $catId)->get()->toArray();

            if (!$category) {
                $this->setView("errors/show404");
                return false;
            }

            $products = Products::where('category', $catId)->get();
            $this->set("cur_cat", $category);
        }

        $paginator = (new Paginator($products->toArray(), $page, 12))->paginate();
        
        $month = date('n');
        /*
         * Fetch Top donators from payments table. Sum their paid totals up
        */
        $top_donor = Payments::fromQuery("SELECT player_name, SUM(paid) as total FROM payments WHERE status='completed' AND MONTH(FROM_UNIXTIME(dateline)) = '$month' GROUP BY player_name ORDER BY total DESC LIMIT 3")->toArray();
        
        // $count = Payments::fromQuery("SELECT COUNT(users) as total FROM top_donors WHERE month = '$month'");
        
        
        // $donors = json_encode($top_donor);
        // if ($count[0]['total'] < 1) {
        //     Payments::fromQuery("INSERT INTO top_donors(users, month) VALUES('$donors', '$month')");
        // } else {
        //     Payments::fromQuery("UPDATE top_donors SET users='$donors' WHERE month='$month'");
        // }
        /*
         * Set the top 3 donations to their values so we can call them from view page.
        */
        $donor_user_1 = (!empty($top_donor[0]['player_name']) ? $top_donor[0]['player_name'] : 'Donate for place');
        $donor_user_2 = (!empty($top_donor[1]['player_name']) ? $top_donor[1]['player_name'] : 'Donate for place');
        $donor_user_3 = (!empty($top_donor[2]['player_name']) ? $top_donor[2]['player_name'] : 'Donate for place');
        
        $this->set("top_donor_1", $donor_user_1);
        $this->set("top_donor_2", $donor_user_2);
        $this->set("top_donor_3", $donor_user_3);
        
        $donor_total_1 = (!empty($top_donor[0]['total']) ? $top_donor[0]['total'] : '1500');
        $donor_total_2 = (!empty($top_donor[1]['total']) ? $top_donor[1]['total'] : '900');
        $donor_total_3 = (!empty($top_donor[2]['total']) ? $top_donor[2]['total'] : '200');
   
        
        $this->set("top_donor_1_rank", $this->getRank($donor_total_1));
        $this->set("top_donor_2_rank", $this->getRank($donor_total_2));
        $this->set("top_donor_3_rank", $this->getRank($donor_total_3));
        
        $this->set("month", Date("F"));
        $this->set("product_count", Products::count());
        $this->set("categories", Categories::get()->toArray());
        $this->set("products", $paginator->getResults());
        $this->set("cur_page", $page);
        $this->set("csrf_token", CSRF::token());
        $this->set("store_name", $this->cookies->get("store_username"));
    } 

    public function checkout() {
        if (!$this->cookies->has("store_username")) {
            return $this->redirect("");
        }

        if ($this->request->isPost() && CSRF::post()) {
            $username = $this->cookies->get("store_username");
            $data     = [];
            $pp_keys  = array_keys(pp_config);

            for ($i = 0; $i < count(pp_config); $i++) {
                $data[$pp_keys[$i]] = pp_config[$pp_keys[$i]];
            }

            $items = UsersCart::where('username', $username)
                ->leftJoin("products", "users_cart.product_id", "=", "products.id")
                ->get();

            if (!$items || empty($items) || count($items) <= 0) {
                return $this->redirect("");
            }
            
            for($i = 0; $i < count($items); $i++) {
                $product_id = $items[$i]->id;
                $index = $i + 1;

                $data['item_number_'.$index] = $items[$i]->product_id;
                $data['item_name_'.$index]   = $items[$i]->item_name;
                $data['amount_'.$index]      = $items[$i]->price;
                $data['quantity_'.$index]    = $items[$i]->quantity;
            }

            $data['custom'] = $this->filter($username);

            if ($this->cookies->has("discount")) {
                $code = $this->filter($this->cookies->get("discount"));
                $discount = DiscountCodes::where('code', $code)->first();

                if ($discount && $discount['expires'] > time()) {
                    $data['discount_rate_cart'] = $discount['percentage'];
                }
            }

            $base_url = "https://www.".(USE_SANDBOX ? "sandbox." : "")."paypal.com/cgi-bin/webscr?";
            $location = $base_url.http_build_query($data, '', '&');
            $this->delayedRedirect($location, 1);
            $this->set("redirect", true);
            return;
        }

        $this->set("csrf_token", CSRF::token());
    }
    
    public function success() {
        return;    
    }
    
    public function stripe() {
        if (!$this->cookies->has("store_username")) {
            return $this->redirect("");
        }

        if ($this->request->isPost() && CSRF::post()) {
            $username = $this->cookies->get("store_username");
            $data     = [];
            

            $items = UsersCart::where('username', $username)
                ->leftJoin("products", "users_cart.product_id", "=", "products.id")
                ->get();

            if (!$items || empty($items) || count($items) <= 0) {
                return $this->redirect("");
            }
            $line_items = array();
            for($i = 0; $i < count($items); $i++) {
                $product_id = $items[$i]->id;
                $index = $i + 1;

                $data['item_number_'.$index] = $items[$i]->product_id;
                $data['item_name_'.$index]   = $items[$i]->item_name;
                $data['amount_'.$index]      = $items[$i]->price;
                $data['quantity_'.$index]    = $items[$i]->quantity;

                array_push($line_items, [
                'price_data' => [
                  'currency' => 'usd',
                  'unit_amount' => $items[$i]->price * 100,
                  'product_data' => [
                    'name' => $items[$i]->item_name,
                    'description' => $items[$i]->summary,
                    'images' => ['https://primalps.net/store/'.$items[$i]->image_url],
                  ],
                ],
                'quantity' => $items[$i]->quantity,
              ]);
            }
            
            $data['custom'] = $this->filter($username);

            if ($this->cookies->has("discount")) {
                $code = $this->filter($this->cookies->get("discount"));
                $discount = DiscountCodes::where('code', $code)->first();

                if ($discount && $discount['expires'] > time()) {
                    $data['discount_rate_cart'] = $discount['percentage'];
                }
            }

            \Stripe\Stripe::setApiKey(SECTRET_KEY);
            $session = \Stripe\Checkout\Session::create([
              'line_items' => [
                 $line_items
             ],
              'mode' => 'payment',
              'success_url' => 'https://primalps.net/store/success',
              'cancel_url' => 'https://primalps.net/store',
            ]);
            
            
        for ($i = 0; $i < count($items); $i++) {
            $item_name   = $items[$i]->item_name;
            $item_number = $items[$i]->product_id;
            $paid        = $items[$i]->price * $items[$i]->quantity;
            $quantity    = $items[$i]->quantity;

            $data = array(
                "item_name"   => $item_name,
                "item_number" => $item_number,
                "paid"        => $paid,
                "quantity"    => $quantity,
                "currency"    => 'usd',
                "buyer"       => '@',
                "player_name" => $username,
                "dateline"    => time(),
                "status"      => $session->id
            );

            $product = Products::where("id", $item_number)->first();

            if ($product == null) {
                $data['status'] = "Invalid product";
            } 

            $payment = new Payments;
            $payment->fill($data);
            $payment->save();
        }   
            if (STRIPE_DEBUG)
                error_log($session, 3, STRIPE_LOG);
            UsersCart::where("username", $username)->delete();
            $this->delayedRedirect($session->url, 1);
            $this->set("redirect", true);
            return;
        }

        $this->set("csrf_token", CSRF::token());
    }
    
    public function ipn_stripe() {
       // This is your Stripe CLI webhook secret for testing your endpoint locally.
        
        $payload = @file_get_contents('php://input');
        $sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];
        $event = null;
        
        try {
          $event = \Stripe\Webhook::constructEvent(
            $payload, $sig_header, ENDPOINT_KEY
          );
        } catch(\UnexpectedValueException $e) {
          // Invalid payload
            if (STRIPE_DEBUG) {
                error_log('invalid payload', 3, STRIPE_LOG);
            }
          http_response_code(400);
          exit();
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
          // Invalid signature
            if (STRIPE_DEBUG) {
                error_log('invalid signature request', 3, STRIPE_LOG);
            }
          http_response_code(400);
          exit();
        }
        
        if ($event->data->object->status == "complete") {
            $status = STRIPE_DEV ? 'test_mode' : 'completed';
            $buyer = $event->data->object->customer_details->email;
            $payment_id = $event->data->object->id;
            $con = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);
            
            $con->query("UPDATE payments SET buyer = '$buyer', status = '$status' WHERE status = '$payment_id'");
            if (STRIPE_DEBUG) {
                error_log('Payment complete: ' . $event, 3, STRIPE_LOG);
            }
            $con->close();
        }
    
    }
    
    public function ipn() {
        $raw_post_data  = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost         = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode ('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }
    
        $req = 'cmd=_notify-validate';
    
        if(function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
    
        foreach ($myPost as $key => $value) {
            $value = urlencode($value);
            
            $req .= "&$key=$value";
        }

        $paypal_url = "https://www.".(USE_SANDBOX ? "sandbox." : "")."paypal.com/cgi-bin/webscr";
        $ch = curl_init($paypal_url);

        if ($ch == FALSE) {
            return FALSE;
        }

        $headers = [
            'X-Apple-Tz: 0',
            'X-Apple-Store-Front: 143444,12',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
            'Accept-Encoding: gzip, deflate',
            'Accept-Language: en-US,en;q=0.5',
            'Cache-Control: no-cache',
            'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
            'Host: '.$_SERVER['HTTP_HOST'],
            'Referer: '.pp_config['return'],
            'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:28.0) Gecko/20100101 Firefox/28.0',
            'X-MicrosoftAjax: Delta=true'
        ];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $req);

        // if having SSL issues, set both of these to 0
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);

        if(DEBUG == true) {
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch, CURLINFO_HEADER_OUT, 1);
        }

        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));

        $res = curl_exec($ch);

        if (curl_errno($ch) != 0) {
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "Can't connect to PayPal to validate IPN message: " . curl_error($ch) . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
            exit;
        } else {
            if(DEBUG == true) {
                error_log(date('[Y-m-d H:i e] '). "HTTP request of validation request:". curl_getinfo($ch, CURLINFO_HEADER_OUT) ." for IPN payload: $req" . PHP_EOL, 3, LOG_FILE);
                error_log(date('[Y-m-d H:i e] '). "HTTP response of validation request: $res" . PHP_EOL, 3, LOG_FILE);
            }
            curl_close($ch);
        }

        $tokens   = explode("\r\n\r\n", trim($res));
        $res      = trim(end($tokens));
        $verified = strcmp($res, "VERIFIED") == 0;

        if (!$verified) {
            if(DEBUG == true)
                error_log(date('[Y-m-d H:i e] '). "Invalid IPN: $req" . PHP_EOL, 3, LOG_FILE);
            return;
        }

        $status     = $_POST['payment_status'];
        $currency   = $_POST['mc_currency'];
        $receiver   = $_POST['receiver_email'];
        $buyer      = $_POST['payer_email'];
        $buyer_name = $_POST['custom'];
        $discount   = $_POST['discount'];

        for ($i = 0; $i < 10; $i++) {
            if (!isset($_POST['item_name'.$i]))
                continue;
            $item_name   = $_POST['item_name'.$i];
            $item_number = $_POST['item_number'.$i];
            $paid        = $_POST['mc_gross_'.$i];
            $quantity    = $_POST['quantity'.$i];

            $data = array(
                "item_name"   => $item_name,
                "item_number" => $item_number,
                "status"      => $status,
                "paid"        => $paid,
                "quantity"    => $quantity,
                "currency"    => $currency,
                "buyer"       => $buyer,
                "player_name" => $buyer_name,
                "dateline"    => time(),
                "status"      => 'completed'
            );

            $product = Products::where("id", $item_number)->first();

            if ($product == null) {
                $data['status'] = "Invalid product";
            } else {
                $price = $product->price;

                if (number_format($paid / $quantity, 2) != number_format($price, 2))
                    $data['status'] = "invalid price";
                if ($item_name != $product->item_name)
                    $data['status'] = "invalid name";
            }
            
            $payment = new Payments;
            $payment->fill($data);
            $payment->save();
            if ($data['status'] != "completed") {
                error_log(date('[Y-m-d H:i e] '). " Invalid Payment: ".json_encode($data)." ". PHP_EOL, 3, LOG_FILE);
            } else {
                error_log("Payment status:  $status", 3, LOG_FILE);
            }
        }

        if(DEBUG == true) 
            error_log(date('[Y-m-d H:i e] '). "Verified IPN: $req ". PHP_EOL, 3, LOG_FILE);
    }

    public function beforeExecute() {
        parent::beforeExecute();

        if ($this->getActionName() == "ipn" || $this->getActionName() == "ipn_stripe") {
            $this->disableView(false);
        }
        

        return true;
    }

}