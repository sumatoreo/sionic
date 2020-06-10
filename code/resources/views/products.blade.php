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
<table class="table">
    <thead class="thead-dark">
    <tr>
        <th>ID</th>
        <th>Имя</th>
        <th>Код</th>
        <th>Вес</th>
        @foreach($cities as $city)
            <th class="text-nowrap">{{ $city->name }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    @foreach ($products as $product)
        <tr>
            <td>{{ $product->id }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->code }}</td>
            <td>{{ $product->weight }}</td>
            @foreach($product->datas as $city)
                <td class="text-nowrap">
                    <strong>Кол-во</strong>: {{ $city->count }}
                    <br>
                    <strong>Цена</strong>: {{ $city->price }}
                </td>
            @endforeach
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
