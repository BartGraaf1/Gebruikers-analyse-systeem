<?php

namespace App\Http\Controllers;

use App\Models\Production; // Make sure to use your actual Production model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductionsManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');

        if ($search) {
            // If there is a search term, perform the search on title and description
            $productions = \App\Models\Production::query()
                ->where('title', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%")
                ->orWhere('created_at', 'LIKE', "%{$search}%")
                ->paginate(10)
                ->appends(request()->except('page')); // This will append all query parameters except 'page' to the pagination links
        } else {
            // Otherwise, just paginate all productions
            $productions = \App\Models\Production::paginate(10);
        }

        // Return the index view with the results
        return view('production-management/productions-overview', compact('productions'));
    }

    public function edit(Production $production)
    {
        // Displays the form to edit an existing production
        return view('production-management/production-edit', compact('production'));
    }

    public function update(Request $request, Production $production)
    {
        // Validates the incoming request except the 'is_active' field
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
        ]);

        // Manually add the 'is_active' field to the validated data.
        // Check if the checkbox was present in the request. If not present, default to false.
        $validatedData['is_active'] = $request->has('is_active');

        // Update the production with the validated data
        $production->update($validatedData);

        return redirect('/production-management')->with('success', 'Production successfully updated.');
    }
}
