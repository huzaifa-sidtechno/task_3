<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Item Management</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
<div class="container mt-5">
    <h2>Manage Items</h2>
    <form id="itemForm" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" class="form-control" id="title" name="title" required>
        </div>
        <div class="mb-3">
            <label for="image1" class="form-label">Image 1</label>
            <input type="file" class="form-control" id="image1" name="image1" required>
        </div>
        <div class="mb-3">
            <label for="image2" class="form-label">Image 2</label>
            <input type="file" class="form-control" id="image2" name="image2" required>
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <hr>
    <table id="itemsTable" class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Image 1</th>
                <th>Image 2</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Items will be loaded here dynamically -->
        </tbody>
    </table>
</div>
<script>

$(document).ready(function () {
    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    // Load initial data
    fetchItems();
    $('#itemForm').submit(function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        var id = $('#itemForm').data('id'); 
        formData.append('_method', id ? 'PUT' : 'POST'); 

        var url = id ? `items/${id}` : 'items'; 
        $.ajax({
            url: url,
            type: 'POST', 
            data: formData,
            contentType: false,
            processData: false,
            success: function (data) {
                alert(data.success);
                $('#itemForm')[0].reset();
                $('#image1, #image2').val(''); 
                $('#itemForm').removeData('id'); 
                fetchItems();
            },
            error: function (error) {
                console.error('Error:', error);
            }
        });
    });

    
    function fetchItems() {
        $.ajax({
            url: 'items/getData',
            type: 'GET',
            success: function (response) {
                var data = response.data;
                $('#itemsTable tbody').empty();
                $.each(data, function (index, item) {
                    var row = `<tr>
                        <td>${item.title}</td>
                        <td>${item.image1}</td>
                        <td>${item.image2}</td>
                        <td>
                            <button class="btn btn-success edit-item" data-id="${item.id}">Edit</button>
                            <button class="btn btn-danger delete-item" data-id="${item.id}">Delete</button>
                        </td>
                    </tr>`;
                    $('#itemsTable tbody').append(row);
                });
            },
            error: function (error) {
                console.error('Error fetching items:', error);
            }
        });
    }

    
    $('#itemsTable').on('click', '.edit-item', function () {
        var itemId = $(this).data('id');
        $.ajax({
            url: `items/${itemId}`,
            type: 'GET',
            success: function (data) {
                $('#title').val(data.title);
                
                $('#itemForm').data('id', itemId); 
            },
            error: function (error) {
                console.error('Error loading item:', error);
            }
        });
    });

   
    $('#itemsTable').on('click', '.delete-item', function () {
        if (confirm('Are you sure you want to delete this item?')) {
            var itemId = $(this).data('id');
            $.ajax({
                url: `items/${itemId}`,
                type: 'DELETE',
                success: function (data) {
                    alert(data.success);
                    fetchItems();
                },
                error: function (error) {
                    console.error('Error deleting item:', error);
                }
            });
        }
    });
});


</script>
</body>
</html>
