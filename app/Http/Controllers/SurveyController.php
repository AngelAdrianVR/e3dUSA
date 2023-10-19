<?php

namespace App\Http\Controllers;

use App\Models\Oportunity;
use App\Models\Survey;
use Illuminate\Http\Request;

class SurveyController extends Controller
{
    
    public function index()
    {
        //
    }

    
    public function create($oportunity_id)
    {
        $survey = null;
        $survey = Survey::where('oportunity_id', $oportunity_id)->first(); //busca en bbdd si existe alguna ecuesta con el id de oportunidad de la url
        $is_sent = false;

        if ($survey === null) { //si no existe entonces la variable se pone en false (0) y se manda a la vista para evaluarse
            $is_sent = 0;
        } else {
            $is_sent = true;
        }

        // return $is_sent;

        return inertia('Survey/Create',compact('oportunity_id', 'is_sent'));
    }

    
    public function store(Request $request, $oportunity_id)
    {
        $request->validate([
            'p1' => 'required|numeric|min:0|max:10',
            'p2' => 'required|boolean',
            'p3' => 'required|boolean',
            'p4' => 'required|boolean',
            'p5' => 'required|string',
        ]);

        Survey::create($request->all() + ['oportunity_id' => $oportunity_id]); //recibe el id de la oportunidad por medio de la url generada
    }

    
    public function show(Survey $survey)
    {
        //
    }

    
    public function edit(Survey $survey)
    {
        //
    }

    
    public function update(Request $request, Survey $survey)
    {
        //
    }

    
    public function destroy(Survey $survey)
    {
        //
    }
}
