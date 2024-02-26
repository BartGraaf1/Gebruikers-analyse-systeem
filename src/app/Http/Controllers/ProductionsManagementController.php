<?php

namespace App\Http\Controllers;

use App\Models\Production; // Make sure to use your actual Production model
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProductionsManagementController extends Controller
{
    public function index()
    {
        // Retrieves all productions
        $productions = Production::paginate(10); // or any other query that ends with paginate()
        return view('production-management/productions-overview', compact('productions'));
    }

    public function create()
    {
        // Displays the form to create a new production
        return view('productions.create');
    }

    public function edit(Production $production)
    {
        // Displays the form to edit an existing production
        return view('productions.edit', compact('production'));
    }

    public function update(Request $request, Production $production)
    {
        // Validates and updates the production
        $validatedData = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'is_active' => 'required|boolean',
        ]);

        $production->update($validatedData);

        return redirect('/productions')->with('success', 'Production successfully updated.');
    }

    public function destroy(Production $production)
    {
        // Deletes the production
        $production->delete();

        return redirect('/productions')->with('success', 'Production successfully deleted.');
    }
}
