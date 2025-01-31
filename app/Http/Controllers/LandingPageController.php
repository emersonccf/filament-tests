<?php

namespace App\Http\Controllers;

use App\Services\AniversarianteService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    protected AniversarianteService $aniversarianteService;

    public function __construct(AniversarianteService $aniversarianteService)
    {
        $this->aniversarianteService = $aniversarianteService;
    }

    /**
     * Exibe a landing page Welcome
     *
     * @return View
     */
    public function index() : View
    {
        $aniversariantes = $this->aniversarianteService->getAniversariantesDoMes();
        $aniversariantesFormatados = $this->aniversarianteService->formatarAniversariantes($aniversariantes);

        return view('welcome', compact('aniversariantesFormatados'));
    }
}
