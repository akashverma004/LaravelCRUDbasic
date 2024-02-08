<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add User Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-4">
                <h1>Update User</h1>
                <form action="{{route('update.user', $data->id)}}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="" class="form-label">Name</label>
                        <input type="text" class="form-control" value="{{ $data->name }}" name="username">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Email</label>
                        <input type="text" value="{{ $data->email }}" class="form-control" name="useremail">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">Age</label>
                        <input type="text" value="{{ $data->age }}" class="form-control" name="userage">
                    </div>
                    <div class="mb-3">
                        <label for="" class="form-label">City</label>
                        <input type="text" value="{{ $data->city }}" class="form-control" name="usercity">
                    </div>
                    <button type="submit" class="btn btn-primary">Update</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>