<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\HomeRepository;

class HomeController extends Controller
{
    protected $homeRepository;

    /**
     * Injection du HomeRepository
     */
    public function __construct(HomeRepository $homeRepository)
    {
        $this->homeRepository = $homeRepository;
    }

    /**
     * Affiche le dashboard avec les statistiques fournies par le repository
     */
    public function index()
    {
        $stats = $this->homeRepository->getDashboardStats();

        return view('home', compact('stats'));
    }
}
