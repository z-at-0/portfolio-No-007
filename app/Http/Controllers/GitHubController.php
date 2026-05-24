<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class GitHubController extends Controller
{
    public function index(Request $request)
    {
        $username = $request->query('username');
        $page = max(1, (int)$request->query('page', 1));

        $userData = null;
        $repoData = [];
        $rateLimit = null;

        if ($username) {

            $userData = Cache::remember(
                "github_user_{$username}",
                600,
                function () use ($username) {
                    return Http::withHeaders([
                        'User-Agent' => 'Laravel-GitHub-App'
                    ])->get("https://api.github.com/users/{$username}")->json();
                }
            );

            $perPage = 5;

            $repoData = Cache::remember(
                "github_repo_{$username}_{$page}",
                600,
                function () use ($username, $page, $perPage) {
                    return Http::withHeaders([
                        'User-Agent' => 'Laravel-GitHub-App'
                    ])->get("https://api.github.com/users/{$username}/repos", [
                        'per_page' => $perPage,
                        'page' => $page,
                        'sort' => 'updated'
                    ])->json();
                }
            );

            if (is_array($repoData)) {

                usort($repoData, function ($a, $b) {
                    return $b['stargazers_count'] <=> $a['stargazers_count'];
                });

                foreach ($repoData as &$repo) {
                    $repo['commits'] = Cache::remember(
                        "github_commit_{$username}_{$repo['name']}",
                        600,
                        function () use ($username, $repo) {
                            return Http::withHeaders([
                                'User-Agent' => 'Laravel-GitHub-App'
                            ])->get("https://api.github.com/repos/{$username}/{$repo['name']}/commits", [
                                'per_page' => 3
                            ])->json();
                        }
                    );
                }
            }

            $rateLimit = Http::withHeaders([
                'User-Agent' => 'Laravel-GitHub-App'
            ])->get("https://api.github.com/rate_limit")->json();
        }

        return view('github', [
            'username' => $username,
            'page' => $page,
            'userData' => $userData,
            'repoData' => $repoData,
            'rateLimit' => $rateLimit
        ]);
    }
}