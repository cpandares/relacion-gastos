<?php

namespace App\Http\Controllers;

use App\Http\Requests\RepositoryRequest;
use App\Models\Repository;
use Illuminate\Http\Request;

class RepositoryController extends Controller
{
    public function index(Repository $repository){

        $repositories = Repository::where('user_id', auth()->user()->id)->get();

        return view('repositories.index', compact('repositories'));
    }

    public function create(){
        return view('repositories.create');
    }


    public function store(RepositoryRequest $request)
    {
      
       $request->user()->repositories()->create($request->all());

       return redirect()->route('repositories.index');
    }

    

   
    public function show(Repository $repository)
    {
         /* Apply police RepositoryPolice */
         $this->authorize('author', $repository);

        return view('repositories.show', compact('repository'));
    }


    public function edit(Repository $repository)
    {
        /* Apply police PostPolice */
        $this->authorize('author', $repository);

        return view('repositories.edit', compact('repository'));
    }
    
    
    public function update(RepositoryRequest $request, Repository $repository)
    {
       

         /* Apply police RepositoryPolice */
         $this->authorize('author', $repository);

        $repository->update($request->all());

        return redirect()->route('repositories.edit', $repository);
    }

    public function destroy(Repository $repository){

         /* Apply police RepositoryPolice */
         $this->authorize('author', $repository);

        $repository->delete();

        return redirect()->route('repositories.index');
    }

}
