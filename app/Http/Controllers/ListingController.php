<?php

namespace App\Http\Controllers;

use App\Http\Requests\ListingUpdateRequest;
use App\Http\Requests\LstingCreteRequest;
use App\Models\Listing;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ListingController extends Controller
{
   public function index()
   {
       return view('listings.index', [
           'heading' => 'Latest listings',
           'listings' => Listing::latest()
               ->filter(\request(['tag', 'search']))
               ->paginate(6)
       ]);
   }

   public function show(Listing $listing)
   {
       return view('listings.show', [
           'listing' => $listing
       ]);
   }

   public function create()
   {
       return view('listings.create');
   }

   public function update(ListingUpdateRequest $request, Listing $listing)
   {
       //Make sure logged in user is owner
       if ($listing->user_id != auth()->id()) {
           abort(403, 'Unauthorized Action');
       }

        $formFields = $request->all();

       if ($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos','public');
       }
        $listing->update($formFields);

        return back()->with('message', 'Listing updated successfully!');
   }

   public function edit(Listing $listing)
   {
       return view('listings.edit', compact('listing'));
   }

    public function store(LstingCreteRequest $request)
    {
        $formFields = $request->all();

        if ($request->hasFile('logo')) {
            $formFields['logo'] = $request->file('logo')->store('logos','public');
        }

        $formFields['user_id'] = auth()->id();

        Listing::create($formFields);

        return redirect('/')->with('message', 'Listing created successfully!');
    }

    public function destroy(Listing $listing)
    {
        $listing->delete();
        return redirect('/')->with('message', 'Listing deleted Successfully');
    }

    public function manage()
    {
        return view('listings.manage', ['listings' => auth()->user()->listings()->get()]);
    }
}
