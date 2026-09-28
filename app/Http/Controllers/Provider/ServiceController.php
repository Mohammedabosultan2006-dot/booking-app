<?php

namespace App\Http\Controllers\Provider;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{

    public function index()
    {
        $services = auth()->user()->services()->withCount('slots', 'reviews')->latest()->paginate(10);
        return view('provider.services.index', compact('services'));
    }

    public function create()
    {
        return view('provider.services.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'price'            => 'required|numeric|min:0',
            'location'         => 'nullable|string|max:200',
            'image'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        auth()->user()->services()->create($data);

        return redirect()->route('provider.services.index')
            ->with('success', 'تمت إضافة الخدمة بنجاح.');
    }

    public function edit(Service $service)
    {
        $this->authorize('update', $service);
        return view('provider.services.edit', compact('service'));
    }

    public function update(Request $request, Service $service)
    {
        $this->authorize('update', $service);

        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'description'      => 'nullable|string|max:1000',
            'duration_minutes' => 'required|integer|min:15|max:480',
            'price'            => 'required|numeric|min:0',
            'location'         => 'nullable|string|max:200',
            'is_active'        => 'boolean',
            'image'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('services', 'public');
        }

        $service->update($data);

        return redirect()->route('provider.services.index')
            ->with('success', 'تم تحديث الخدمة.');
    }

    public function destroy(Service $service)
    {
        $this->authorize('delete', $service);
        $service->delete();
        return back()->with('success', 'تم حذف الخدمة.');
    }
}
