<?php
use Fox\Request;
use Illuminate\Database\Eloquent\Model as Model;

class Products extends Model {

    public $timestamps    = false;
    public $incrementing  = true;
    protected $primaryKey = 'id';

    protected $fillable = [
        'item_name',
        'item_id',
        'category',
        'price',
        'max_qty',
        'image_url',
        'discount',
        'summary',
        'description'
    ];

    public static function validate($request) {
        $name    = htmlspecialchars($request->getPost("item_name","string"));
        $id      = $request->getPost("item_id","int");
        $price   = $request->getPost("price");
        $cat_id  = $request->getPost("category","int");
        $image   = $request->getPost("image_url","string");
        $desc    = $request->getPost("description");
        $max_qty = $request->getPost("max_qty", "int");

        $error = null;

        if (strlen($name) < 3 || strlen($name) > 50) {
            $error = 'Name must be between 3 and 50 characters.';
        } else if (!is_numeric($id) || $id < 0) {
            $error = 'ID must be an integer greater than 0.';
        } else if (!is_numeric($price) || $price < 0) {
            $error = "Price must be greater than 0!";
        } else if (!is_numeric($price) || $price < 0) {
            $error = "Price must be greater than 0!";
        } else if (!Categories::where("id", $cat_id)->first()) {
            $error = "Invalid category Id.";
        } else if ($image != filter_var($image, FILTER_SANITIZE_URL)) {
            $error = "Image url contains invalid characters.";
        } else if ($max_qty < -1 || $max_qty > 2147483647) {
            $error = "Max quantity must be either -1 (infinite) or between 1 and ".PHP_INT_MAX;
        }

        return [
            'success' => $error == null,
            'error'   => $error,
            'data'    => [
                'item_name'   => $name,
                'item_id'     => $id,
                'price'       => $price,
                'category'    => $cat_id,
                'max_qty'     => $max_qty,
                'image_url'   => $image,
                'description' => $desc
            ]
        ];
    }
    
}