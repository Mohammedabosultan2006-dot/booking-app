<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Slot;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SlotController extends Controller
{

    public function index(Service $service)
    {
        $this->authorize('update', $service);

        $slots = $service->slots()->upcoming()->paginate(20);
        return view('provider.slots.index', compact('service', 'slots'));
    }

    public function store(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $data = $request->validate([
            'start_at' => 'required|date|after:now',
            'end_at'   => 'required|date|after:start_at',
        ]);

        // منع التداخل
        $overlap = $service->slots()
            ->where(fn($q) => $q
                ->whereBetween('start_at', [$data['start_at'], $data['end_at']])
                ->orWhereBetween('end_at', [$data['start_at'], $data['end_at']])
            )->exists();

        if ($overlap) {
            return back()->withErrors(['start_at' => 'يوجد تعارض مع وقت آخر.']);
        }

        $service->slots()->create($data);

        return back()->with('success', 'تمت إضافة الوقت المتاح.');
    }

    /** إضافة مجموعة أوقات تلقائياً */
    public function bulkStore(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $data = $request->validate([
            'date'       => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time'   => 'required|date_format:H:i|after:start_time',
            'break'      => 'required|integer|min:0|max:60', // استراحة بالدقائق
        ]);

        $start    = Carbon::parse($data['date'] . ' ' . $data['start_time']);
        $end      = Carbon::parse($data['date'] . ' ' . $data['end_time']);
        $duration = $service->duration_minutes;
        $break    = (int) $data['break'];
        $slots    = [];

        while ($start->copy()->addMinutes($duration)->lte($end)) {
            $slotEnd = $start->copy()->addMinutes($duration);
            $slots[] = [
                'service_id' => $service->id,
                'start_at'   => $start->toDateTimeString(),
                'end_at'     => $slotEnd->toDateTimeString(),
                'is_booked'  => false,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $start->addMinutes($duration + $break);
        }

        Slot::insert($slots);

        return back()->with('success', 'تمت إضافة ' . count($slots) . ' أوقات.');
    }

    public function destroy(Slot $slot)
    {
        $this->authorize('update', $slot->service);
        abort_if($slot->is_booked, 422, 'لا يمكن حذف وقت محجوز.');
        $slot->delete();
        return back()->with('success', 'تم حذف الوقت.');
    }
}
