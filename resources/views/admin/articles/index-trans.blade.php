<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>All Articles Translations</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>#id</th>
                <th>#articleId</th>
                <th>slug</th>
                <th>language code</th>
                <th>title</th>
                <th>description</th>
                <th>meta title</th>
                <th>meta description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($articles_trans as $article_trans)
            <tr>
                <td>{{$article_trans['id']}}</td>
                <td>{{$article_trans['article_id']}}</td>
                <td>{{$article_trans['slug']}}</td>
                <td>{{$article_trans['language_code']}}</td>
                <td>{{$article_trans['title']}}</td>
                <td>{{$article_trans['description']}}</td>
                <td>{{$article_trans['meta_title']}}</td>
                <td>{{$article_trans['meta_description']}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <br />
</body>
</html>