<?php

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Show all categories with real article counts.
     */
    public function index(): View
    {
        $categories = Article::published()
            ->select('category_slug', \DB::raw('count(*) as count'))
            ->groupBy('category_slug')
            ->orderBy('category_slug')
            ->pluck('count', 'category_slug')
            ->toArray();

        $categoryIcons = [
            'technology' => 'fa-microchip',
            'education' => 'fa-graduation-cap',
            'travel' => 'fa-compass',
            'food' => 'fa-utensils',
            'business' => 'fa-chart-line',
            'sports' => 'fa-medal',
            'health' => 'fa-heart-pulse',
            'lifestyle' => 'fa-couch',
            'finance' => 'fa-wallet',
            'science' => 'fa-atom',
            'art' => 'fa-palette',
            'culture' => 'fa-landmark',
        ];

        $categoryDescriptions = [
            'technology' => 'Breakthroughs in AI, cloud architectures, and modern software development.',
            'education' => 'Transformative pedagogy, lifelong learning strategies, and digital classroom evolution.',
            'travel' => 'Slow travel itineraries, hidden remote destinations, and mindful cultural expeditions.',
            'food' => 'Culinary traditions, farm-to-table sustainability, and artisanal gastronomy.',
            'business' => 'Strategic leadership, venture dynamics, and resilient operational execution.',
            'sports' => 'Athletic biomechanics, tactical sports analytics, and legendary endurance journeys.',
            'health' => 'Evidence-based wellness, preventative neuroscience, and holistic mental and physical health.',
            'lifestyle' => 'Intentional living, workspace architecture, mindfulness practices, and daily creative rituals.',
            'finance' => 'Macroeconomic trends, ethical investing, personal wealth structuring, and decentralized protocols.',
            'science' => 'Astrophysics, clean energy transitions, synthetic biology, and empirical frontiers.',
            'art' => 'Visual culture, gallery retrospectives, generative aesthetics, and contemporary creative movements.',
            'culture' => 'Literary critiques, sociology of digital spaces, and philosophical inquiries into modern life.',
        ];

        return view('pages.categories', compact('categories', 'categoryIcons', 'categoryDescriptions'));
    }
}
