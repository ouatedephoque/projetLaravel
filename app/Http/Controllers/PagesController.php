<?php

namespace App\Http\Controllers;

use Request;
use App\Tournament;
use App\Equipe;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class PagesController extends Controller
{
    public function index()
    {
    	return view('Pages.index');
    }

    public function showForm()
    {
    	$nom = Request::get('nom');

        return redirect('createTournament')->with('nom', $nom);
    }

    public function showListAllTournament()
    {
        $tournaments = Tournament::all();
        return view('Pages.listAllTournament')->with('tournaments', $tournaments);
    }

    public function showActualTournament($id)
    {
        $tournament = Tournament::find($id);
        return view('Pages.manageTournament')->with('tournament', $tournament);
    }

    public function creationGroupsTournament($id)
    {
        /*$tournament = Tournament::with('equipes')->find($id);
        return view('Pages.createGroup', compact('tournament'));*/
    }

    public function showListAllTeams($id)
    {
        $equipes = Equipe::where('tournament_id', '=', $id)->get();
        return view('Pages.listAllTeams')->with('equipes', $equipes);
    }
}
