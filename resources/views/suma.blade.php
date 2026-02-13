<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Suma de 2 números</title>
</head>
<body>
    <h2>Sumar 2 numero</h2>
    <form action="/suma" method="post">
        @csrf
        <label for="num1">Número 1:</label>
        <input type="number" name="num1" id="num1" required>
        <br>
        @csrf
        <label for="num2">Número 2:</label>
        <input type="number" name="num2" id="num2" reqired>
        <br>
        <button tipe="submit">Sumar</button>
    </form>
    <br>
    @if(isset($resul))
    <h3>Resultado de la suma: {{$resul}}</h3>
    @endif
</body>
</html>
