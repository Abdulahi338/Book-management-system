<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="scripts.js"></script>
</head>
<body>
    <div class="container mt-5">
        <h1>Book Management</h1>
        <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#addBookModal">Add Book</button>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Author</th>
                    <th>Category</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="book-list">
                <!-- Books will be loaded here -->
            </tbody>
        </table>
    </div>

    <!-- Add Book Modal -->
    <div class="modal fade" id="addBookModal" tabindex="-1" aria-labelledby="addBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBookModalLabel">Add Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="add-book-form">
                        <div class="mb-3">
                            <label for="add-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="add-title" required>
                        </div>
                        <div class="mb-3">
                            <label for="add-author" class="form-label">Author</label>
                            <select class="form-select" id="add-author" required>
                                <!-- Options will be loaded here -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="add-category" class="form-label">Category</label>
                            <select class="form-select" id="add-category" required>
                                <!-- Options will be loaded here -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Book Modal -->
    <div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBookModalLabel">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="edit-book-form">
                        <input type="hidden" id="edit-book-id">
                        <div class="mb-3">
                            <label for="edit-title" class="form-label">Title</label>
                            <input type="text" class="form-control" id="edit-title" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit-author" class="form-label">Author</label>
                            <select class="form-select" id="edit-author" required>
                                <!-- Options will be loaded here -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-category" class="form-label">Category</label>
                            <select class="form-select" id="edit-category" required>
                                <!-- Options will be loaded here -->
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Book</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Load books
            function loadBooks() {
                $.get('api.php', { action: 'list_books' }, function(data) {
                    if (data.status === 'success') {
                        let booksHtml = '';
                        data.books.forEach(book => {
                            booksHtml += `<tr>
                                <td>${book.id}</td>
                                <td>${book.title}</td>
                                <td>${book.author}</td>
                                <td>${book.category}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm edit-book" data-id="${book.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-book" data-id="${book.id}">Delete</button>
                                </td>
                            </tr>`;
                        });
                        $('#book-list').html(booksHtml);
                    }
                });
            }

            // Load authors and categories
            function loadSelectOptions() {
                $.get('api.php', { action: 'list_authors' }, function(data) {
                    if (data.status === 'success') {
                        let options = '<option value="">Select Author</option>';
                        data.authors.forEach(author => {
                            options += `<option value="${author.id}">${author.name}</option>`;
                        });
                        $('#add-author, #edit-author').html(options);
                    }
                });

                $.get('api.php', { action: 'list_categories' }, function(data) {
                    if (data.status === 'success') {
                        let options = '<option value="">Select Category</option>';
                        data.categories.forEach(category => {
                            options += `<option value="${category.id}">${category.name}</option>`;
                        });
                        $('#add-category, #edit-category').html(options);
                    }
                });
            }

            // Initialize
            loadBooks();
            loadSelectOptions();

            // Add book
            $('#add-book-form').on('submit', function(e) {
                e.preventDefault();
                $.post('api.php', {
                    action: 'add_book',
                    title: $('#add-title').val(),
                    author_id: $('#add-author').val(),
                    category_id: $('#add-category').val()
                }, function(data) {
                    if (data.status === 'success') {
                        $('#addBookModal').modal('hide');
                        loadBooks();
                    } else {
                        alert(data.message);
                    }
                });
            });

            // Edit book
            $(document).on('click', '.edit-book', function() {
                const id = $(this).data('id');
                $.get('api.php', { action: 'list_books' }, function(data) {
                    if (data.status === 'success') {
                        const book = data.books.find(b => b.id == id);
                        if (book) {
                            $('#edit-book-id').val(book.id);
                            $('#edit-title').val(book.title);
                            $('#edit-author').val(book.author_id);
                            $('#edit-category').val(book.category_id);
                            $('#editBookModal').modal('show');
                        }
                    }
                });
            });

            $('#edit-book-form').on('submit', function(e) {
                e.preventDefault();
                $.post('api.php', {
                    action: 'update_book',
                    id: $('#edit-book-id').val(),
                    title: $('#edit-title').val(),
                    author_id: $('#edit-author').val(),
                    category_id: $('#edit-category').val()
                }, function(data) {
                    if (data.status === 'success') {
                        $('#editBookModal').modal('hide');
                        loadBooks();
                    } else {
                        alert(data.message);
                    }
                });
            });

            // Delete book
            $(document).on('click', '.delete-book', function() {
                const id = $(this).data('id');
                if (confirm('Are you sure you want to delete this book?')) {
                    $.post('api.php', {
                        action: 'delete_book',
                        id: id
                    }, function(data) {
                        if (data.status === 'success') {
                            loadBooks();
                        } else {
                            alert(data.message);
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
