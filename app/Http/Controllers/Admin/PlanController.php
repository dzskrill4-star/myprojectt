<?php

namespace App\Http\Controllers\Admin;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Lib\RequiredConfig;
use App\Models\Miner;
use App\Models\Order;
use App\Models\Plan;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PlanController extends Controller {

    public function index() {
        $pageTitle = "Mining Plans";
        $plans     = Plan::with('miner');

        if (request()->has('currency')) {
            $plans->whereHas('miner', fn($q) => $q->where('currency_code', request('currency')));
        }

        $plans     = $plans->searchable(['title', 'miner:name'])->orderBy('id', 'desc')->paginate(getPaginate());
        $miners    = Miner::orderBy('name')->get();
        return view('admin.plans.index', compact('pageTitle', 'plans', 'miners'));
    }

    public function store(Request $request) {
        $this->validation($request);
        $plan         = new Plan();
        $this->savePlan($plan, $request);
        $notify[] = ['success', 'Plan created successfully'];
        return back()->withNotify($notify);
    }

    public function update(Request $request, $id) {
        $this->validation($request);
        $plan         = Plan::findOrFail($id);
        $this->savePlan($plan, $request);
        $notify[] = ['success', 'Plan updated successfully'];
        return back()->withNotify($notify);
    }

    protected function validation($request) {
        $request->validate([
            'miner'              => 'required_sometimes|exists:miners,id',
            'title'              => 'required|string|max:255',
            'price'              => 'required|numeric',
            'return_per_day'     => 'required_if:return_type,1|numeric',
            'min_return_per_day' => 'required_if:return_type,2|numeric',
            'max_return_per_day' => 'required_if:return_type,2|numeric',
            'speed'              => 'required|numeric',
            'speed_unit'         => 'required|integer|between:0,8',
            'period'             => 'required|numeric|gt:0',
            'period_unit'        => 'required|integer|between:0,2',
            'description'        => 'nullable|string',
            'status'             => 'nullable|regex:(on)',
            'features'           => 'nullable|array',
            'features.*'         => 'string',
            'maintenance_cost'   => 'required|numeric|gte:0',
        ]);
    }

    protected function savePlan($plan, $request) {
        if (!$plan->id) {
            $plan->miner_id           = $request->miner;
        }
        $plan->title              = $request->title;
        $plan->price              = $request->price;
        $plan->min_return_per_day = $request->return_per_day ?? $request->min_return_per_day;
        $plan->max_return_per_day = $request->max_return_per_day ?? null;
        $plan->speed              = $request->speed;
        $plan->speed_unit         = $request->speed_unit;
        $plan->period             = $request->period;
        $plan->period_unit        = $request->period_unit;
        $plan->description        = $request->description;
        $plan->maintenance_cost   = $request->maintenance_cost;
        $plan->features           = $request->features ?? [];
        $plan->save();

        RequiredConfig::configured('mining_plans');
    }

    public function status($id) {
        return Plan::changeStatus($id);
    }

    public function overview($id) {
        $plan        = Plan::withCount([
            'orders as total_mining_tracks' => fn($q) => $q->approved(),
            'orders as active_mining_tracks' => fn($q) => $q->running(),
            'orders as completed_mining_tracks' => fn($q) => $q->completed(),
        ])
            ->withSum(['orders as total_ordered_amount' => function ($q) {
                $q->where('status', Status::ORDER_APPROVED);
            }], 'amount')
            ->findOrFail($id);

        $ordersQuery = $plan->orders();
        $currency  = $plan->miner->currency_code;

        $totalReturnAmount = Transaction::leftJoin('orders', 'transactions.order_id', 'orders.id')
            ->where('orders.plan_id', $id)
            ->where('currency', $currency)
            ->where('remark', 'return_amount')
            ->sum('transactions.amount');

        $totalMaintenanceAmount = Transaction::leftJoin('orders', 'transactions.order_id', 'orders.id')
            ->where('orders.plan_id', $id)
            ->where('currency', $currency)
            ->where('remark', 'maintenance_cost')
            ->sum('transactions.amount');

        $pageTitle = $plan->title . ' Analytics';
        return view('admin.plans.overview', compact('pageTitle', 'currency', 'plan', 'totalReturnAmount', 'totalMaintenanceAmount'));
    }

    public function orderAnalytics(Request $request) {
        $plan      = Plan::findOrFail($request->plan_id);
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format  = $diffInDays > 30 ? '%M-%Y' : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $orders = Order::approved()->where('plan_id', $plan->id)
            ->whereDate('created_at', '>=', $request->start_date)
            ->whereDate('created_at', '<=', $request->end_date)
            ->selectRaw('SUM(amount) AS amount')
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
        $plan      = Plan::with('orders')->find($request->plan_id);
        $diffInDays = Carbon::parse($request->start_date)->diffInDays(Carbon::parse($request->end_date));

        $groupBy = $diffInDays > 30 ? 'months' : 'days';
        $format  = $diffInDays > 30 ? '%M-%Y' : '%d-%M-%Y';

        if ($groupBy == 'days') {
            $dates = $this->getAllDates($request->start_date, $request->end_date);
        } else {
            $dates = $this->getAllMonths($request->start_date, $request->end_date);
        }

        $returns = Transaction::where('remark', 'return_amount')
            ->leftJoin('orders', 'transactions.order_id', 'orders.id')
            ->where('orders.plan_id', $plan->id)
            ->whereDate('transactions.created_at', '>=', $request->start_date)
            ->whereDate('transactions.created_at', '<=', $request->end_date)
            ->selectRaw('SUM(transactions.amount) AS amount')
            ->selectRaw("DATE_FORMAT(transactions.created_at, '{$format}') as created_on")
            ->groupBy('created_on')
            ->orderBy('transactions.created_at')
            ->get();

        $maintenanceCosts = Transaction::where('remark', 'maintenance_cost')
            ->leftJoin('orders', 'transactions.order_id', 'orders.id')
            ->where('orders.plan_id', $plan->id)
            ->whereDate('transactions.created_at', '>=', $request->start_date)
            ->whereDate('transactions.created_at', '<=', $request->end_date)
            ->selectRaw('SUM(transactions.amount) AS amount')
            ->selectRaw("DATE_FORMAT(transactions.created_at, '{$format}') as created_on")
            ->groupBy('created_on')
            ->orderBy('transactions.created_at')
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
