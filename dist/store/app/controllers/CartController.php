<?php
if (!defined("site_title")) {
    header( 'HTTP/1.0 403 Forbidden', TRUE, 403);
    exit;
}

use Fox\CSRF;

class CartController extends Controller {

    public function index() {
        $store_name = $this->filter($this->cookies->get("store_username"));
        $cartItems  = UsersCart::where('username', $store_name)
            ->leftJoin("products", "users_cart.product_id", "=", "products.id")
            ->get();

        if ($this->cookies->has("discount")) {
            $code = $this->filter($this->cookies->get("discount"));
            $discount = DiscountCodes::where('code', $code)->first();

            if ($discount) {
                if ($discount['expires'] < time()) {
                    $this->cookies->delete("discount");
                } else {
                    $this->set("discount", $discount);
                }
            }
        }

        $this->set("cart_items", $cartItems->toArray());
        $this->set("store_name", $store_name);
        $this->set("csrf_token", CSRF::token());
        $this->setView("cart/items");
    }

    public function add() {
        if (!$this->request->hasPost("product")
                || !$this->cookies->has("store_username")) {
            return true;
        }

        $product_id = $this->request->getPost("product", "int");
        $store_name = $this->cookies->get("store_username");
        $quantity   = $this->request->getPost("quantity", "int");

        if (!is_numeric($quantity) || $quantity < 1 || $quantity > 2147483647) {
            $quantity = 1;
        }

        $product = Products::where('id', $product_id)->first();

        if (!$product) {
            return [
                'success' => false,
                'message' => 'Invalid Product ID: '.$product_id
            ];
        }

        $cart_item = UsersCart::where("username", $store_name)
            ->where("product_id", $product_id)
            ->first();

        if ($cart_item) {
            $curQty  = $cart_item->quantity;
            $newQty  = $curQty + $quantity;

            if ($product->max_qty > 0 && $newQty > $product->max_qty) {
                $newQty = $product->max_qty;
            }

            if ($newQty == 0) {
                return [
                    'success' => false,
                    'message' => 'You have reached the maximum limit for this item.'
                ];
            }

            $cart_item->quantity = $newQty;
            $cart_item->save();

            return [
                'success' => $updated,
                'message' => $cart_item['item_name'].' has been updated!'
            ];
        }

        if ($product->max_qty > 0) {
            if ($quantity > $product->max_qty) {
                $quantity = $product->max_qty;
            }
        }

        $cart_item = new UsersCart;
        
        $cart_item->fill([
            'username' => $store_name,
            'product_id' => $product->id,
            'quantity' => $quantity
        ]);

        $cart_item->save();

        return [
            'success' => true,
            'message' => $product['item_name'].' has been added to your cart.'
        ];
    }

    public function delete() {
        if (!$this->request->hasPost("product") || !$this->cookies->has("store_username")) {
            return [
                'success' => false,
                'message'   => 'uhhh'
            ];
        }

        $product_id = $this->request->getPost("product", "int");
        $store_name = $this->cookies->get("store_username");

        $deleted = UsersCart::where("username", $store_name)
            ->where("product_id", $product_id)
            ->get()->first()->delete();

        return [
            'success' => $deleted,
            'message' => $deleted ?
                'Your cart has been updated.':
                'Failed to update cart.'
        ];
    }

    public function beforeExecute() {
        $disable_view = ['delete', 'add', 'verify', 'process', 'stripe'];

        if (in_array($this->getActionName(), $disable_view)) {
            $this->disableView(true);
        }
        
        return parent::beforeExecute();
    }

}