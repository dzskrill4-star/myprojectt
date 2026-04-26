<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;

class MiningTracksController extends Controller {
    public function allTracks() {
        $pageTitle = 'All Mining Tracks';
        $orders    = $this->miningTracks();
        return view('admin.mining_tracks', compact('orders', 'pageTitle'));
    }

    public function completedTracks() {
        $pageTitle = 'Completed Mining Tracks';
        $orders    = $this->miningTracks('completed');
        return view('admin.mining_tracks', compact('orders', 'pageTitle'));
    }

    public function activeTracks() {
        $pageTitle = 'Active Mining Tracks';
        $orders    = $this->miningTracks('running');
        return view('admin.mining_tracks', compact('orders', 'pageTitle'));
    }

    protected function miningTracks($scope = null) {
        $orders = Order::approved();

        if ($scope) {
            $orders = Order::$scope();
        }

        return $orders->leftJoin('transactions', 'transactions.order_id', '=', 'orders.id')
            ->leftJoin('miners', 'orders.miner_id', '=', 'miners.id')
            ->approved()
            ->orderByDesc('orders.id')
            ->selectRaw('orders.*, miners.currency_code as currency_code')
            ->selectRaw("
                SUM(
                    CASE
                        WHEN transactions.remark = 'return_amount' THEN transactions.amount
                        WHEN transactions.remark = 'maintenance_cost' THEN -1 * transactions.amount
                        ELSE 0
                    END
                ) as total_return_amount
            ")
            ->groupBy('orders.id')->searchable(['trx', 'user:username', 'miner.plans:title', 'miner:name'])
            ->filter(['miner_id', 'plan_id'])
            ->with('deposit.gateway', 'user')
            ->orderBy('id', 'desc')
            ->paginate(getPaginate());
    }
}
