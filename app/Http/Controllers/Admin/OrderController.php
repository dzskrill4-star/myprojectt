<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\Order;

class OrderController extends Controller {
    public function index() {
        $pageTitle = 'All Orders';
        $orders = Order::where('status', '!=', Status::ORDER_UNPAID)->searchable(['trx', 'user:username', 'miner.plans:title', 'miner:name'])->filter(['miner_id', 'plan_id'])->with('deposit.gateway', 'miner', 'user')->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.order.index', compact('orders', 'pageTitle'));
    }
}
