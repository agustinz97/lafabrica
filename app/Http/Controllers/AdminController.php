<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Photo;
use App\Article;
use App\Project;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function newPhoto()
    {
        return view('admin.newPhoto');
    }

    public function news()
    {
        $news = Article::with('photo')
            ->latest()
            ->get();

        return view('admin.news')->with([
            'news' => $news,
        ]);
    }

    public function newNew($id = null)
    {
        $new = null;
        if ($id) {
            $new = Article::find($id);
        }

        return view('admin.newNew')->with([
            'article' => $new,
        ]);
    }

    public function projects()
    {
        $projects = Project::all();

        return view('admin.projects')->with([
            'projects' => $projects,
        ]);
    }

    public function project($id)
    {
        $project = Project::with('photos')->find($id);

        return view('admin.project')->with([
            'project' => $project,
        ]);
    }
}
