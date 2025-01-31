<?php

namespace App\Http\Controllers;

use App\Services\AniversarianteService;
use App\Services\QuoteService;
use Illuminate\View\View;

class LandingPageController extends Controller
{
    protected AniversarianteService $aniversarianteService;
    protected QuoteService $quoteService;

    public function __construct(AniversarianteService $aniversarianteService, QuoteService $quoteService)
    {
        $this->aniversarianteService = $aniversarianteService;
        $this->quoteService = $quoteService;
    }

    public function index() : View
    {
        $aniversariantes = $this->aniversarianteService->getAniversariantesDoMes();
        $aniversariantesFormatados = $this->aniversarianteService->formatarAniversariantes($aniversariantes);
        $quote = $this->quoteService->getRandomQuote();

        return view('welcome', compact('aniversariantesFormatados', 'quote'));
    }
}
