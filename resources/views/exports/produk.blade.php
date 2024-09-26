<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Produk</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Laporan Daftar Produk</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nama Produk</th>
                <th>Harga</th>
                <th>Stok</th>
                <th>Dibuat Pada</th>
                <th>Diupdate Pada</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $item)
                <tr>
                    <td>{{ $item['id'] }}</td> <!-- Menggunakan array untuk data yang disanitasi -->
                    <td>{{ $item['name'] }}</td>
                    <td>{{ $item['harga'] }}</td>
                    <td>{{ $item['stok'] }}</td>
                    <td>{{ $item['created_at'] }}</td>
                    <td>{{ $item['updated_at'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
