<?php

namespace App\Http\Controllers;

use App\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $people = Person::all();
        
        $res = [
            'status' => 'ok',
            'message' => 'lista de personas',
            'code' => 1000,
            'data' => $people
        ];

        return $res;
        // return 'people';
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // para insertar necesitamos un objeto json, y eso se logra con:
        $jsonPerson = $request->json()->all();
        
        //para visualizarlo en postman, tenemos que hacer un 'POST', opcion body, luego en Text>json agregamos los datos con el mismo formato
        //de lo campos
        //dd($jsonPerson);
        $person = new Person($jsonPerson);

        $person->save();

        $res = [
            'status' => 'ok',
            'message' => 'persona creada ',
            'code' => 1003,
            'data' => $person
        ];

        return $res;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $person = Person::find($id);

        if(isset($person)){
            $res = [
                'status' => 'ok',
                'message' => 'Obteniendo persona por id: ' . $id,
                'code' => 1001,
                'data' => $person
            ];
        } else {    
            $res = [
                'status' => 'error',
                'message' => 'No se encontro la persona con id: ' . $id,
                'code' => 1011,
                'data' => $person
            ];
        }
        return $res;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $person = Person::find($id);

        if(isset($person)){
            $person->update($request->json()->all());
            $res = [
                'status' => 'ok',
                'message' => 'persona con id: ' . $id . " actualizada",
                'code' => 1005,
            ];
        } else {
            $res = [
                'status' => 'error',
                'message' => 'persona con id: ' . $id . " no encontrada para actualizar.",
                'code' => 1015,
            ];
        }
        
        return $res;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $person = Person::find($id);

        if(isset($person)){
            $person->delete();
            $res = [
                'status' => 'ok',
                'message' => 'persona con id: ' . $id . " eliminada",
                'code' => 1004,
            ];
        } else {
            $res = [
                'status' => 'error',
                'message' => 'persona con id: ' . $id . " no encontrada.",
                'code' => 1014,
            ];
        }
        
        return $res;
    }
}
