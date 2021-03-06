<?php

namespace App\Http\Controllers;

use Request;
use App\Tournament;
use App\Equipe;
use App\Match;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;

class TreeTournamentController extends Controller
{
    /**
    *   Affichage de l'arbre du tournoi
    *
    *   paramètre : id du tournoi
    *   return : les équipes et id tournoi
    **/
    public function showTreeTournament($id)
    {
        $tournament = Tournament::with('equipes')->find($id);

        $qualifiedTeam = $this->searchTeamQualified($tournament);

        return view('Pages.treeTournament')->with('qualifiedTeam', $qualifiedTeam)->with('tournamentId', $id);
    }

    /* Attribution des points dans chaque groupe */
    private function searchTeamQualified($tournament)
    {
        $games = Match::where('tournament_id', '=', $tournament->id)->get();
        $nbGroup = count($this->createGroups($tournament->equipes, $tournament));

        $classement = array();

        for($i = 0; $i < $nbGroup; $i++)
        {
            array_push($classement, array());
        }

        foreach($games as $game)
        {
            $pointWonTeam1 = $this->pointWon($game['score1'], $game['score2']);
            $pointWonTeam2 = $this->pointWon($game['score2'], $game['score1']);
            $groupe = $game['groupe']-1;

            $classement = $this->createPointTeam($game['equipe1'], $classement, $groupe, $pointWonTeam1);
            $classement = $this->createPointTeam($game['equipe2'], $classement, $groupe, $pointWonTeam2);
        }

        $classementSorted = array();

        foreach ($classement as $group) 
        {
            arsort($group);
            array_push($classementSorted, $group);
        }

        return $this->takeTeamQualified($tournament, $classementSorted, $nbGroup);
    }

    /* Récupération des équipes qualifiés pour le tour suivant */
    private function takeTeamQualified($tournament, $classement, $nbGroupe)
    {
        $qualifiedTeam = array();
        $arrayNbTeamQualif = array('0' => 32, '1' => 16, '2' => 8, '3' => 4, '4' => 2); // Type de final
        $nbTeamQualif = $arrayNbTeamQualif[$tournament->typeFinales];

        $nbTeamQualifPerGroup = $nbTeamQualif / $nbGroupe;

        if($nbTeamQualifPerGroup > 0)
        {
            foreach ($classement as $groupe) 
            {
                $i = 0;
                foreach($groupe as $team => $point)
                {
                    array_push($qualifiedTeam, $team);
                    $i++;
                    if($i >= $nbTeamQualifPerGroup) break;
                }
            }
        }

        /* 
            Inversion des matchs, les équipes du même groupe 
            ne joue pas l'une contre l'autre
        */
        if(count($qualifiedTeam) >= 4)
        {
            for($i = 0; $i < count($qualifiedTeam); $i+=4)
            {
                $tmp = $qualifiedTeam[$i];
                $qualifiedTeam[$i] = $qualifiedTeam[$i+2];
                $qualifiedTeam[$i+2] = $tmp;
            }
        }

        return $qualifiedTeam;
    }

    /* Points par match gagné, perdu et égalité */
    private function createPointTeam($team, $classement, $groupe, $pointWon)
    {
        if(!array_key_exists($team, $classement[$groupe]))
        {
            $classement[$groupe][$team] = $pointWon;
        }
        else
        {
            $classement[$groupe][$team] += $pointWon;
        }

        return $classement;
    }

    private function pointWon($score1, $score2)
    {
        if($score1 > $score2) return 3;
        else if($score1 < $score2) return 0;
        else return 1;
    }

    /* Création des groupes */
    private function createGroups($teams, $tournament)
    {
        // create an array of teams
        $members = array();
        $i = 0;
        foreach ($teams as $team) {
            $members[$i] =  $team['nom'];
            $i++;
        }

        // Split our array of team on a array of groupes of teams
        $groupes = array_chunk($members, ceil($tournament->nbEquipe / $tournament->nbGroupe));

        return $groupes;
    }
}
