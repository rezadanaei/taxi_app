<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Car;
use App\Models\CarType;

class CarController extends Controller
{
    /**
     * Display a listing of the cars.
     */
    public function index()
    {
        $cars = Car::with('carType')->get();
        return view('admin.cars.index', compact('cars'));
    }

    /**
     * Show the form for creating a new car.
     */
    public function create()
    {
        $carTypes = CarType::all();
        return view('admin.cars.create', compact('carTypes'));
    }

    /**
     * Store a newly created car in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'car_identifier' => 'required|string|unique:cars,car_identifier',
            'car_type_id'    => 'required|exists:car_types,id',
        ]);

        Car::create($request->only(['name', 'car_identifier', 'car_type_id']));

        return redirect()->route('admin.cars.index')->with('success', 'ماشین با موفقیت اضافه شد.');
    }

    /**
     * Display the specified car.
     */
    public function show(Car $car)
    {
        return view('admin.cars.show', compact('car'));
    }

    /**
     * Show the form for editing the specified car.
     */
    public function edit(Car $car)
    {
        $carTypes = CarType::all();
        return view('admin.cars.edit', compact('car', 'carTypes'));
    }

    /**
     * Update the specified car in storage.
     */
    public function update(Request $request, Car $car)
    {
        $request->validate([
            'name'           => 'required|string|max:255',
            'car_identifier' => 'required|string|unique:cars,car_identifier,' . $car->id,
            'car_type_id'    => 'required|exists:car_types,id',
        ]);

        $car->update($request->only(['name', 'car_identifier', 'car_type_id']));

        return redirect()->route('admin.cars.index')->with('success', 'ماشین با موفقیت بروزرسانی شد.');
    }

    /**
     * Remove the specified car from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();
        return redirect()->route('admin.cars.index')->with('success', 'ماشین با موفقیت حذف شد.');
    }
}
