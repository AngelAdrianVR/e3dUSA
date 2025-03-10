<?php

namespace App\Http\Controllers;

use App\Events\RecordCreated;
use App\Events\RecordEdited;
use App\Http\Resources\QualityResource;
use App\Models\Quality;
use App\Models\Sale;
use Illuminate\Http\Request;

class QualityController extends Controller
{
    
    public function index()
    {
        $pre_qualities = Quality::with('supervisor:id,name')->latest()->get(['id', 'supervisor_id', 'status', 'created_at', 'production_id']);
        $qualities = $pre_qualities->map(function ($quality) {

            return [
                'id' => $quality->id,
                'links' => $quality->links,
                'meta' => $quality->meta,
                'supervisor' => [
                    'id' => $quality->supervisor->id,
                    'name' => $quality->supervisor->name
                ],
                'folio' => 'OP-' . str_pad($quality->production_id, 4, "0", STR_PAD_LEFT),
                'status' => $quality->status,
                'created_at' => $quality->created_at?->isoFormat('DD MMM, YYYY h:mm A'),
            ];
        });

        return inertia('Quality/Index', compact('qualities'));
    }

    
    public function create()
    {
        $pre_productions = Sale::with(['user:id,name', 'productions'])->whereHas('productions')->latest()->get(['id', 'user_id']);
        $productions = $pre_productions->map(function ($production) {

            $finishedCount = 0;
                foreach ($production->productions as $productionItem) {
                    if ($productionItem->finished_at != null) {
                        $finishedCount++;
                    }
                }

                // Calcular el porcentaje --------------------------------------------------------
                //-------------------------------------------------------------------------------
                $percentage = $finishedCount > 0 ? (100 / count($production->productions)) * $finishedCount : 0;

            return [
                'id' => $production->id,
                'folio' => 'OP-' . str_pad($production->id, 4, "0", STR_PAD_LEFT),
                'percentage' => $percentage
            ];
        });

        // return $pre_productions;

        return inertia('Quality/Create', compact('productions'));
    }

    
    public function store(Request $request)
    {
       $request->validate([
            'production_id' => 'required',
            'status' => 'required|string',
            'product_inspection.status' => 'nullable|string|max:20',
            'product_inspection.progress' => 'nullable|string|max:20',
            'product_inspection.Pieces' => 'nullable|numeric|min:0',
            'product_inspection.stop_explanation' => 'nullable|string|max:250',
            'product_inspection.problem_description' => 'nullable|string|max:250',
            'product_inspection.corrective_actions' => 'nullable|string|max:250',
            'product_inspection.notes' => 'nullable|string|max:250',
            'product_inspection.media' => 'nullable',
        ]);

       $quality = Quality::create($request->all() + ['supervisor_id' => auth()->id()]);

       //guarda la media
       $quality->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        event(new RecordCreated($quality));

        return to_route('qualities.index');
    }

    
    public function show($quality_id)
    {
        $quality = QualityResource::make(Quality::with(['supervisor:id,name', 'production.catalogProductCompanySales.catalogProductCompany.catalogProduct'])->find($quality_id));
        $pre_qualities = Quality::with(['supervisor:id,name'])->latest()->get(['id', 'production_id', 'supervisor_id']);
        $qualities = $pre_qualities->map(function ($quality) {

            return [
                'id' => $quality->id,
                'folio' => 'OP-' . str_pad($quality->production_id, 4, "0", STR_PAD_LEFT),
            ];
        });
        
        return inertia('Quality/Show', compact('quality', 'qualities'));
    }

    
    public function edit(Quality $quality)
    {
        $production = Sale::with('catalogProductCompanySales.catalogProductCompany.catalogProduct')->find($quality->production_id);
        $pre_productions = Sale::with(['user:id,name', 'productions'])->whereHas('productions')->latest()->get(['id', 'user_id']);
        $productions = $pre_productions->map(function ($production) {

            $finishedCount = 0;
                foreach ($production->productions as $productionItem) {
                    if ($productionItem->finished_at != null) {
                        $finishedCount++;
                    }
                }

                // Calcular el porcentaje --------------------------------------------------------
                //-------------------------------------------------------------------------------
                $percentage = $finishedCount > 0 ? (100 / count($production->productions)) * $finishedCount : 0;

            return [
                'id' => $production->id,
                'folio' => 'OP-' . str_pad($production->id, 4, "0", STR_PAD_LEFT),
                'percentage' => $percentage
            ];
        });

        // return $production;

        return inertia('Quality/Edit', compact('quality', 'productions', 'production'));
    }

    
    public function update(Request $request, Quality $quality)
    {
        $request->validate([
            'production_id' => 'required',
            'status' => 'required|string',
            'product_inspection.status' => 'nullable|string|max:20',
            'product_inspection.progress' => 'nullable|string|max:20',
            'product_inspection.Pieces' => 'nullable|numeric|min:0',
            'product_inspection.stop_explanation' => 'nullable|string|max:250',
            'product_inspection.problem_description' => 'nullable|string|max:250',
            'product_inspection.corrective_actions' => 'nullable|string|max:250',
            'product_inspection.notes' => 'nullable|string|max:250',
            'product_inspection.media' => 'nullable',
        ]);

       $quality->update($request->all());

        event(new RecordEdited($quality));

        return to_route('qualities.index');
    }


    public function updateWithMedia(Request $request, Quality $quality)
    {
        $request->validate([
            'production_id' => 'required',
            'status' => 'required|string',
            'product_inspection.status' => 'nullable|string|max:20',
            'product_inspection.progress' => 'nullable|string|max:20',
            'product_inspection.Pieces' => 'nullable|numeric|min:0',
            'product_inspection.stop_explanation' => 'nullable|string|max:250',
            'product_inspection.problem_description' => 'nullable|string|max:250',
            'product_inspection.corrective_actions' => 'nullable|string|max:250',
            'product_inspection.notes' => 'nullable|string|max:250',
            'product_inspection.media' => 'nullable',
        ]);

        $quality->update($request->all());

        // media
        $quality->clearMediaCollection();
        $quality->addAllMediaFromRequest()->each(fn ($file) => $file->toMediaCollection());

        // event(new RecordEdited($quality));

        return to_route('qualities.index');
    }

    
    public function destroy(Quality $quality)
    {
        $quality->delete();

    }


    public function getProduction($production_id)
    {
        $production = Sale::with('catalogProductCompanySales.catalogProductCompany.catalogProduct')->find($production_id);

        return response()->json(['item' => $production]);
    }

    public function getQuality($quality_id)
    {
        $quality = Quality::with('supervisor')->find($quality_id);

        return response()->json(['item' => $quality]);
    }
}
