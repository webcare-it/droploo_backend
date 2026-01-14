<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dropshipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DropshipperController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Check for existing order by invoice number
            $dropshipper = Dropshipper::where('dropshipper_id', $request->dropshipper_id)->first();

            if (!$dropshipper) {
                $dropshipper = new Dropshipper();
                $dropshipper->dropshipper_id = $request->dropshipper_id;
            }
            $dropshipper->dropshipper_id  = $dropshipper->dropshipper_id;
            $dropshipper->package_id      = $request->package_id;
            $dropshipper->name            = $request->name;
            $dropshipper->user_name       = $request->user_name ;
            $dropshipper->domain_name     = $request->domain_name;
            $dropshipper->phone           = $request->phone;
            $dropshipper->image           = $request->logo;
            $dropshipper->email           = $request->email;
            $dropshipper->password        = $request->password;
            $dropshipper->address         = $request->address;
            $dropshipper->app_key         = $request->app_key;
            $dropshipper->app_secret      = $request->app_secret;
            $dropshipper->is_approved      = 1;
            $dropshipper->save();

            return response()->json([
                'status'   => 'success',
                'message'  => $dropshipper->wasRecentlyCreated ? 'Dropshipper has been created' : 'Dropshipper has been updated',
                'dropshipper_id' => $dropshipper->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function updateLogo(Request $request)
    {
        try {
            // Check for existing order by invoice number
            $dropshipper = Dropshipper::where('dropshipper_id', $request->dropshipper_id)->first();
            $dropshipper->image           = $request->logo;
            $dropshipper->save();

            return response()->json([
                'status'   => 'success',
                'message'  => $dropshipper->wasRecentlyCreated ? 'Dropshipper profile has been created' : 'Dropshipper profile has been updated',
                'dropshipper_id' => $dropshipper->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        try {
            // Check for existing order by invoice number
            $dropshipper = Dropshipper::where('dropshipper_id', $request->dropshipper_id)->first();
            $dropshipper->name            = $request->name;
            $dropshipper->phone           = $request->phone;
            $dropshipper->save();

            return response()->json([
                'status'   => 'success',
                'message'  => $dropshipper->wasRecentlyCreated ? 'Dropshipper profile has been created' : 'Dropshipper profile has been updated',
                'dropshipper_id' => $dropshipper->id,
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ], 500);
        }
    }

    public function deleteByDomainName(Request $request)
    {
        try {
            $dropshipper = Dropshipper::where('domain_name', $request->domain_name)->first();

            if (!$dropshipper) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Dropshipper not found with the specified domain name',
                ], 404);
            }

            // Finally, delete the dropshipper
            $dropshipper->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Dropshipper deleted successfully',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
                'trace'   => $e->getTraceAsString(),
            ], 500);
        }
    }
}
