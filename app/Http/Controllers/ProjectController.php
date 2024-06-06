<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $merchant = Merchant::where('user_id', $user->id)->first();
        $projects = Project::where('merchant_id', $merchant->id)->get();

        return response()->json([
            'status' => '200',
            'data' => $projects
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $merchant = Merchant::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project = Project::create([
            'merchant_id' => $merchant->id,
            'name' => $validated['name'],
            'merchant_no'=>$merchant->merchant_no
        ]);

        return response()->json([
            'status' => '201',
            'data' => $project
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();
        $merchant = Merchant::where('user_id', $user->id)->first();
        $project = Project::where('id', $id)->where('merchant_id', $merchant->id)->first();

        if (!$project) {
            return response()->json([
                'status' => '404',
                'message' => 'Project not found',
            ], 404);
        }

        return response()->json([
            'status' => '200',
            'data' => $project
        ]);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $merchant = Merchant::where('user_id', $user->id)->first();
        $project = Project::where('id', $id)->where('merchant_id', $merchant->id)->first();

        if (!$project) {
            return response()->json([
                'status' => '404',
                'message' => 'Project not found',
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $project->update([
            'name' => $validated['name'],
        ]);

        return response()->json([
            'status' => '200',
            'data' => $project
        ]);
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $merchant = Merchant::where('user_id', $user->id)->first();
        $project = Project::where('id', $id)->where('merchant_id', $merchant->id)->first();

        if (!$project) {
            return response()->json([
                'status' => '404',
                'message' => 'Project not found',
            ], 404);
        }

        $project->delete();

        return response()->json([
            'status' => '200',
            'message' => 'Project deleted successfully'
        ]);
    }
}
