<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Miner;
use App\Models\Order;
use App\Models\Transaction;
use App\Rules\FileTypeValidate;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CryptocurrencyController extends Controller {
    public function index() {
        $pageTitle = "All Cryptocurrencies";
        $miners    = Miner::searchable(['currency_code', 'name'])->with('plans')->orderBy('id', 'desc')->paginate(getPaginate());

        return view('admin.cryptocurrency.index', compact('pageTitle', 'miners'));
    }

    public function store(Request $request, $id = 0) {
        $imageValidation = 'required';

        if ($id) {
            $imageValidation = 'nullable';
        }

        $validateRule = [
            'name'          => 'required|string|max:255|unique:miners,name,' . $id,
            'currency_code' => 'required|string|max:40|unique:miners,currency_code,' . $id,
            'coin_image'    => [$imageValidation, 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'rate'          => 'required|numeric|gt:0'
        ];

        $request->validate($validateRule);

        $miner        = Miner::find($id);
        $notification = 'Miner updated successfully';

        if (!$miner) {
            $miner        = new Miner();
            $notification = 'Miner added successfully';
        }

        if ($request->hasFile('coin_image')) {
            try {
                $miner->coin_image = fileUploader($request->coin_image, getFilePath('miner'), getFileSize('miner'), $miner?->coin_image);
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the miner coin image'];
                return back()->withNotify($notify);
            }
        }

        $miner->name      = $request->name;
        $miner->currency_code = $request->currency_code;
        $miner->rate = $request->rate;
        $miner->save();

        RequiredConfig::configured('miners');

        $notify[] = ['success', $notification];
        return back()->withNotify($notify);
    }

    public function overview($id) {
        $miner     = Miner::withCount([
            'orders as total_mining_tracks' => fn($q) => $q->approved(),
            'orders as active_mining_tracks' => fn($q) => $q->running(),
            'orders as completed_mining_tracks' => fn($q) => $q->completed(),
        ])
            ->withSum(['orders as total_ordered_amount' => function ($q) {
                $q->where('status', Status::ORDER_APPROVED);
            }], 'amount')
            ->with('plans')
            ->findOrFail($id);

        $totalReturnAmount = Transaction::where('currency', $miner->currency_code)->where('remark', 'return_amount')->sum('amount');
        $totalMaintenanceAmount = Transaction::where('currency', $miner->currency_code)->where('remark', 'maintenance_cost')->sum('amount');
        $currency  = $miner->currency_code;
        $pageTitle = $miner->name . ' Analytics';
        return view('admin.cryptocurrency.overview', compact('pageTitle', 'currency', 'miner', 'totalMaintenanceAmount', 'totalReturnAmount'));
    }

    public function orderAnalytics(Request $request) {
        $miner      = Miner::with('orders')->findOrFail($request->miner_id);
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format  = $diffInDays > 30 ? '%M-%Y' : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $orders = $miner->orders()->approved()->where('miner_id', $miner->id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date);

        if ($request->plan_id) {
            $orders->where('plan_id', $request->plan_id);
        }

        $orders = $orders->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'orders'    => trimTrailingZeros($orders->where('created_on', $date)->first()?->amount ?? 0),
            ];
        }

        $data = collect($data);
        $report['created_on'] = $data->pluck('created_on');
        $report['data']       = [
            [
                'name' => 'Order Amount',
                'data' => $data->pluck('orders'),
            ]
        ];

        return response()->json($report);
    }

    public function returnAnalytics(Request $request) {
        $miner      = Miner::with('orders')->find($request->miner_id);
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format  = $diffInDays > 30 ? '%M-%Y' : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $returns = Transaction::where('currency', $miner->currency_code)
            ->where('remark', 'return_amount')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $maintenanceCosts = Transaction::where('currency', $miner->currency_code)
            ->where('remark', 'maintenance_cost')
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
            ->selectRaw("DATE_FORMAT(created_at, '{$format}') as created_on")
            ->latest()
            ->groupBy('created_on')
            ->get();

        $data = [];

        foreach ($dates as $date) {
            $data[] = [
                'created_on' => $date,
                'amounts'     => trimTrailingZeros($returns->where('created_on', $date)->first()?->amount ?? 0),
                'maintenance' => trimTrailingZeros($maintenanceCosts->where('created_on', $date)->first()?->amount ?? 0),
            ];
        }

        $data = collect($data);

        $report['created_on'] = $data->pluck('created_on');
        $report['data']       = [
            [
                'name' => 'Return Amount',
                'data' => $data->pluck('amounts'),
            ],
            [
                'name' => 'Maintenance Cost',
                'data' => $data->pluck('maintenance'),
            ]
        ];

        return response()->json($report);
    }

    public function ordersByPlan(Request $request) {
        $orders = Order::where('orders.miner_id', $request->miner_id)
            ->selectRaw('SUM(orders.amount) AS total_order_amount, plans.title AS plan_title')
            ->rightJoin('plans', 'orders.plan_id', 'plans.id')
            ->groupBy('plan_id')->get()->filter(function ($row) {
                return is_numeric($row->total_order_amount);
            })
            ->sortByDesc('total_order_amount')
            ->values();

        return response()->json([
            'series' =>  $orders->pluck('total_order_amount')->map(fn($v) => (float) $v),
            'labels' => $orders->map(function ($order) {
                return $order->plan_title;
            }),
        ]);
    }

    private function getAllDates($startDate, $endDate) {
        $dates       = [];
        $currentDate = new \DateTime($startDate);
        $endDate     = new \DateTime($endDate);

        while ($currentDate <= $endDate) {
            $dates[] = $currentDate->format('d-F-Y');
            $currentDate->modify('+1 day');
        }

        return $dates;
    }

    private function getAllMonths($startDate, $endDate) {
        if ($endDate > now()) {
            $endDate = now()->format('Y-m-d');
        }

        $startDate = new \DateTime($startDate);
        $endDate   = new \DateTime($endDate);

        $months = [];

        while ($startDate <= $endDate) {
            $months[] = $startDate->format('F-Y');
            $startDate->modify('+1 month');
        }

        return $months;
    }
}
