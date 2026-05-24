<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>portfolio-No-007 Laravel GitHub API App</title>

    <style>
        body { font-family: sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; color: #333; background-color: #f9f9f9; }

        .container { background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }

        h2 { border-left: 5px solid #28a745; padding-left: 15px; margin-bottom: 25px; }

        .search-form { margin-bottom: 20px; }

        .search-form input { padding: 10px; width: 250px; border: 1px solid #ddd; border-radius: 4px; }

        .search-form button { padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; }

        .rate-info { font-size: 0.85em; color: #666; background: #eee; padding: 10px; border-radius: 5px; margin-bottom: 20px; }

        .profile { display: flex; align-items: center; gap: 20px; margin-bottom: 30px; padding: 15px; background: #f0fff4; border-radius: 8px; }

        .profile img { border-radius: 50%; border: 2px solid #fff; }

        .repo-item { border: 1px solid #eee; border-radius: 8px; padding: 20px; margin-bottom: 15px; background: #fff; }

        .repo-name { font-size: 1.1em; font-weight: bold; color: #28a745; text-decoration: none; }

        .commit-box { font-size: 0.85em; background: #fcfcfc; padding: 12px; margin-top: 15px; border-left: 4px solid #28a745; }

        .pagination { display: flex; align-items: center; justify-content: center; gap: 20px; margin-top: 30px; }

        .btn { padding: 8px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; font-size: 14px; }

        .page-num { font-weight: bold; color: #666; }
    </style>
</head>

<body>

<div class="container">

    <h2>Laravel GitHub API App</h2>

    <div class="search-form">
        <form method="GET">
            <input type="text" name="username" value="{{ $username }}" placeholder="GitHubユーザー名" required>
            <input type="hidden" name="page" value="1">
            <button type="submit">検索</button>
        </form>
    </div>

@if($rateLimit)
<div class="rate-info">
    API残り回数：{{ $rateLimit['rate']['remaining'] }} / {{ $rateLimit['rate']['limit'] }}
    <span style="margin-left: 10px; color: #28a745;">
        (キャッシュ機能：有効)
    </span>
</div>
@endif

    @if($userData && isset($userData['login']))
        <div class="profile">
            <img src="{{ $userData['avatar_url'] }}" width="70" height="70">
            <div>
                <h3>{{ $userData['login'] }}</h3>
                <p>{{ $userData['bio'] ?? '自己紹介なし' }}</p>
            </div>
        </div>
    @endif

    @if(is_array($repoData))

        <h4>リポジトリ一覧（スター順）</h4>

        @foreach($repoData as $repo)
            <div class="repo-item">

                <span>⭐ {{ $repo['stargazers_count'] }}</span>

                <a href="{{ $repo['html_url'] }}" target="_blank" class="repo-name">
                    {{ $repo['name'] }}
                </a>

                <p>{{ $repo['description'] ?? '説明なし' }}</p>

                <div class="commit-box">
                    <strong>最近のコミット</strong><br><br>

                    @if(isset($repo['commits']))
                        @foreach($repo['commits'] as $commit)
                            @if(isset($commit['commit']['message']))
                                ・ {{ $commit['commit']['message'] }}<br>
                            @endif
                        @endforeach
                    @endif
                </div>

            </div>
        @endforeach

    @endif

    @if($username)
        <div class="pagination">

            @if($page > 1)
                <a class="btn" href="/github?username={{ $username }}&page={{ $page - 1 }}">← 前へ</a>
            @endif

            <span class="page-num">Page {{ $page }}</span>

            @if(count($repoData) === 5)
                <a class="btn" href="/github?username={{ $username }}&page={{ $page + 1 }}">次へ →</a>
            @endif

        </div>
    @endif

</div>

</body>
</html>