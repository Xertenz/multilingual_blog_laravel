<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Categories</title>
    @vite(['resources/css/app.css'])
</head>
<body>
    <table>
        <thead>
            <tr>
                <td>#id</td>
                <td>featured_img</td>
                <td>sign</td>
                <td>actions</td>
            </tr>
        </thead>
        <tbody>
            @foreach ($cats as $cat)
            <tr>
                <td>{{$cat->id}}</td>
                <td></td>
                <td>{{$cat->sign}}</td>
                <td>
                    <a href="">
                        <button>Update</button>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot></tfoot>
    </table>
</body>
</html>