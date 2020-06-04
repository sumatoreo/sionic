<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css" integrity="sha384-9aIt2nRpC12Uk9gS9baDl411NQApFmC26EwAOH8WgZl5MYYxFfc+NcPb1dKGj7Sk" crossorigin="anonymous">
</head>
<body>
<table class="container">
    <thead>
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Код</th>
        <th>Вес</th>
        <th>Город</th>
        <th>Цена</th>
        <th>Количество</th>
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->code }}</td>
            <td>{{ $product->weight }}</td>
            <td>{{ $product->datas->city->name }}</td>
            <td>{{ $product->datas->price }}</td>
            <td>{{ $product->datas->count }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
<br>
<div class="mx-auto" style="width: 800px;">
{{ $products->links() }}
</div>
</body>
</html>
