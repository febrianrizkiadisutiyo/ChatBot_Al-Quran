<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

{{--    Bootstrap Icon--}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.3/font/bootstrap-icons.css">

    <title>QuranType-Translator</title>
</head>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins&display=swap');

    body {
        font-family: 'Poppins', sans-serif;
        font-size: 12px;
    }

    input::placeholder {
        font-size: 10px;
    }
    a {
        text-decoration: none;
        color: black;
    }

    a:hover {
        color: black;
    }

    i {
        color: aqua;
    }

</style>
<body>
<div class="container mt-3 ">
    <h3 class="text-center">Daftar Penerjemah</h3>
    <div class="d-flex">
        @if($cek == true)
            <a href="{{ url()->previous() }}" class="mt-4 me-3"><h4><i class="bi bi-arrow-left-circle"></i></h4></a>
        @endif
        <div class="row g-3 align-items-center">
            <div class="col-auto">
                <form action="/daftarTranslator" method="GET">
                    <input placeholder="cari berdasarkan bahasa" type="search" id="search" name="search" class="mt-3 form-control form-control-sm" aria-describedby="searchHelp">
                </form>
            </div>
        </div>
    </div>

    <div class="table-responsive-sm text-center">
        <table class="table table-striped mt-4">
            <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">Id Penggunaan</th>
                <th scope="col">Nama Penerjemah</th>
                <th scope="col">Bahasa</th>
            </tr>
            </thead>
            <tbody>
            @foreach($daftar as $data)
                <tr>
                    <th scope="row">{{$loop->iteration}}</th>
                    <td>{{$data->id}}</td>
                    <td>{{$data->name}}</td>
                    <td>{{$data->language_name}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {!! $daftar->withQueryString()->links('pagination::bootstrap-5') !!}
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
</body>
</html>
