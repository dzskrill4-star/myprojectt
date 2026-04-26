<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;

class P2PController extends Controller
{
    /**
     * Show P2P Marketplace
     */
    public function marketplace()
    {
        // Only allow user with id=24 to access
        if (auth()->user()->id != 24) {
            $notify[] = ['error', __('This area is under development')];
            return back()->withNotify($notify);
        }

        $pageTitle = 'P2P Marketplace';
        return view('templates.dark.user.p2p.index', compact('pageTitle'));
    }

    /**
     * Show Buy Orders
     */
    public function buyOrders()
    {
        $pageTitle = 'P2P Marketplace';
        return view('templates.dark.user.p2p.index', compact('pageTitle'));
    }

    /**
     * Show Sell Orders
     */
    public function sellOrders()
    {
        $pageTitle = 'P2P Marketplace';
        return view('templates.dark.user.p2p.index', compact('pageTitle'));
    }

    /**
     * Show Sellers and Buyers List
     */
    public function sellersBuyers()
    {
        // Only allow user with id=24 to access
        if (auth()->user()->id != 24) {
            $notify[] = ['error', __('This area is under development')];
            return back()->withNotify($notify);
        }

        $pageTitle = 'P2P Sellers & Buyers';
        return view('templates.dark.user.p2p.sellers-buyers-list', compact('pageTitle'));
    }

    /**
     * Show Deal Details
     */
    public function dealDetails($id)
    {
        // Only allow user with id=24 to access
        if (auth()->user()->id != 24) {
            $notify[] = ['error', __('This area is under development')];
            return back()->withNotify($notify);
        }

        $pageTitle = 'P2P Deal Details';
        return view('templates.dark.user.p2p.deal-details', compact('pageTitle'));
    }

    /**
     * Show Coming Soon Page
     */
    public function comingSoon()
    {
        $pageTitle = 'P2P (Coming Soon)';
        return view('templates.dark.user.p2p.coming-soon', compact('pageTitle'));
    }
}
