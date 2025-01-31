<?php

namespace App\Services;

use Illuminate\Support\Facades\File;

class QuoteService
{
    protected mixed $quotes;

    public function __construct()
    {
        $this->loadQuotes();
    }

    protected function loadQuotes() : void
    {
        $path = database_path('data/quotes.json');
        $this->quotes = json_decode(File::get($path), true);
    }

    public function getRandomQuote() : mixed
    {
        return $this->quotes[array_rand($this->quotes)];
    }
}
