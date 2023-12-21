<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}
use Illuminate\Database\Eloquent\Model as Model;

class Payments extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        "item_name",
        "item_number",
        "status",
        "paid",
        "quantity",
        "currency",
        "buyer",
        "dateline",
        "player_name",
        "status"
    ];

}