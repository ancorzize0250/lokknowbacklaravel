<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pregunta;
use App\Models\Opcion;
use App\Models\RespuestaCorrecta;
use App\Models\RespuestaUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TestController extends Controller
{
    private $preguntasPorBloque = 10;

    public function handleTestRequest(Request $request)
    {
        if ($request->isMethod('get')) {
            return $this->getQuestions($request);
        }

        if ($request->isMethod('post')) {
            return $this->postAnswers($request);
        }

        return response()->json(['error' => 'Método no soportado'], 405);
    }

    /**
     * Obtiene el siguiente bloque de preguntas que no han sido respondidas.
     */
    private function getQuestions(Request $request)
    {
        $preguntas = Pregunta::with(['opciones', 'respuestaCorrecta'])
            ->where('respondida', false) // Filtrar por la nueva columna
            ->inRandomOrder() 
            ->take($this->preguntasPorBloque)
            ->get();

        $testData = $preguntas->map(function ($pregunta) {
            $opcionesTransformadas = $pregunta->opciones->mapWithKeys(function ($opcion) {
                return [$opcion->opcion => $opcion->descripcion_opcion];
            });

            return [
                'numero' => $pregunta->id,
                'pregunta' => $pregunta->pregunta,
                'opciones' => $opcionesTransformadas,
                'respuesta_correcta' => optional($pregunta->respuestaCorrecta)->opcion_correcta,
            ];
        });

        $preguntaInicial = $preguntas->first() ? $preguntas->first()->id : null;
        $preguntaFinal = $preguntas->last() ? $preguntas->last()->id : null;

        return response()->json([
            'pregunta_inicial' => (int) $preguntaInicial,
            'pregunta_final' => (int) $preguntaFinal,
            'test' => $testData,
        ]);
    }
    
    /**
     * Procesa las respuestas del usuario, valida si son correctas y guarda el resultado.
     * También actualiza el estado de las preguntas en la tabla 'preguntas'.
     */
    private function postAnswers(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pregunta_inicial' => 'required|integer',
            'pregunta_final' => 'required|integer',
            'respuestas' => 'required|array',
            'respuestas.*.id_pregunta' => 'required|integer|exists:preguntas,id',
            'respuestas.*.respuesta_usuario' => 'required|string|in:a,b,c,d',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $respuestas = $request->input('respuestas');

        DB::beginTransaction();

        try {
            foreach ($respuestas as $respuesta) {
                $preguntaId = $respuesta['id_pregunta'];
                
                // Obtener la respuesta correcta para validación
                $respuestaCorrecta = RespuestaCorrecta::where('id_pregunta', $preguntaId)->first();
                $esCorrecta = ($respuestaCorrecta && $respuestaCorrecta->opcion_correcta === $respuesta['respuesta_usuario']);

                // 1. Guardar la respuesta del usuario con el resultado
                RespuestaUsuario::create([
                    'id_pregunta' => $preguntaId,
                    'respuesta_usuario' => $respuesta['respuesta_usuario'],
                    'correcta' => $esCorrecta,
                ]);

                // 2. Marcar la pregunta como respondida en la tabla 'preguntas'
                Pregunta::where('id', $preguntaId)->update(['respondida' => true]);
            }

            DB::commit();

            return response()->json(['message' => 'Respuestas guardadas con éxito.']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al guardar las respuestas.',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function registerQuestions(Request $request)
    {
        $validator = Validator::make($request->json()->all(), [
            '*.pregunta' => 'required|string',
            '*.opciones' => 'required|array|size:4',
            '*.opciones.a' => 'required|string',
            '*.opciones.b' => 'required|string',
            '*.opciones.c' => 'required|string',
            '*.opciones.d' => 'required|string',
            '*.respuesta_correcta' => 'required|string|in:a,b,c,d',
        ], [
            '*.pregunta.required' => 'El campo pregunta es obligatorio.',
            '*.opciones.required' => 'El campo opciones es obligatorio y debe ser un array con 4 elementos.',
            '*.respuesta_correcta.required' => 'El campo respuesta_correcta es obligatorio.',
            '*.respuesta_correcta.in' => 'La respuesta correcta debe ser una de las siguientes opciones: a, b, c, d.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        DB::beginTransaction();

        try {
            foreach ($request->json()->all() as $preguntaData) {
                $pregunta = Pregunta::create(['pregunta' => $preguntaData['pregunta']]);
                $preguntaId = $pregunta->id;
                foreach ($preguntaData['opciones'] as $opcionKey => $opcionValue) {
                    Opcion::create([
                        'id_pregunta' => $preguntaId,
                        'opcion' => $opcionKey,
                        'descripcion_opcion' => $opcionValue,
                    ]);
                }
                RespuestaCorrecta::create([
                    'id_pregunta' => $preguntaId,
                    'opcion_correcta' => $preguntaData['respuesta_correcta'],
                ]);
            }
            DB::commit();
            return response()->json([
                'message' => 'Preguntas registradas masivamente con éxito.',
                'questions_count' => count($request->json()->all())
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Ocurrió un error al registrar las preguntas.',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}