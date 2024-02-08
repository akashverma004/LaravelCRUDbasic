<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>All Users</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-6">
                <h1>All Users List</h1>
                <a href="/newuser" class="btn btn-success btn-sm mb-3">Add New</a>
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>NAME</th>
                            <th>EMAIL</th>
                            <th>CITY</th>
                            <th>AGE</th>
                            <th>VIEW</th>
                            <th>DELETE</th>  
                            <th>UPDATE</th>  
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $id => $user)
                            <tr>
                                <td>{{ $id + 1 }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->city }}</td>
                                <td>{{ $user->age }}</td>
                                <td><a href="{{ route('view.user', $user->id) }}" class="btn btn-primary btn-sm">View</a></td>
                                <td><a href="{{ route('delete.user', $user->id) }}" class="btn btn-danger btn-sm">Delete</a></td>
                                <td><a href="{{ route('update.page', $user->id) }}" class="btn btn-warning btn-sm">Update</a></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-6 mt-6">
                {{ $data->links() }}
            </div>
        </div>
    </div>
</body>
</html>