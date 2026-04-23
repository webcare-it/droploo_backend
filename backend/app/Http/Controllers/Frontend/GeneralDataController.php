<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\GeneralSetting;
use App\Models\Setting;
use Illuminate\Http\Request;

class GeneralDataController extends Controller
{
    public function getSettings()
    {
        try {
            $settings = GeneralSetting::first();
    
            if (!$settings) {
                return response()->json([
                    'error' => true,
                    'message' => 'Settings not found',
                    'generalData' => null
                ], 404);
            }
    
            return response()->json([
                'error' => false,
                'message' => 'Settings retrieved successfully',
                'generalData' => $settings
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => 'An error occurred while retrieving settings',
                'generalData' => null
            ], 500);
        }
    }

    public function getCategories ()
    {
        try {
            $categories = Category::where('status', 1)->orderBy('priority', 'asc')->with('subcategories')->get();
    
            if ($categories->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Categories not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Categories retrieved successfully',
                'data' => $categories
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve categories. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }

    public function getSliders ()
    {
        try {
            $sliders = Setting::get();
    
            if ($sliders->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sliders not found',
                    'data' => null
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'sliders retrieved successfully',
                'data' => $sliders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sliders. ' . $e->getMessage(),
                'data' => null
            ], 500);
        }
    }
}
