<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

class AdminController extends Controller {

    public function index() {
        $chartData = $this->getChartData();
        $this->set("chart_labels", array_keys($chartData));
        $this->set("chart_data", array_values($chartData));
        $this->set("stats", $this->getStats());
    }
    
    private function getChartData() {
        $start  = strtotime(date('Y-01-01 00:00:00'));

        $payments = Payments::where("dateline", ">", $start)
            ->orderBy("dateline", "DESC")->get();

        $data = [];

        foreach ($payments as $payment) {
            $date = date("M-d", $payment->dateline);

            if (!isset($data[$date])) {
                $data[$date] = 0;
            }

            $data[$date] = number_format($data[$date] + $payment->paid, 2);
        }

        return $data;
    }

    private function getStats() {
        $data  = [];
        $start = strtotime(date("Y-m-01 00:00:00"));

        $data['totalEarned']     = Payments::sum("paid");
        $data['earnedMonthly']   = Payments::where("dateline", ">", $start)->sum("paid");
        $data['payments']        = Payments::count();
        $data['paymentsMonthly'] = Payments::where("dateline", ">", $start)->count();
        return $data;
    }

    public function requireLogin() {
        return true;
    }

    public function isAdminPanel() {
        return true;
    }

}