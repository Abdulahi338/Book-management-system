$(document).ready(function() {
    // Load books
    function loadBooks() {
        $.get('api.php?action=list_books', function(data) {
            if (data.status === 'success') {
                let rows = '';
                $.each(data.books, function(index, book) {
                    rows += `<tr>
                                <td>${book.id}</td>
                                <td>${book.title}</td>
                                <td>${book.author}</td>
                                <td>${book.category}</td>
                                <td>
                                    <button class="btn btn-warning btn-sm me-2 edit-book" data-id="${book.id}">Edit</button>
                                    <button class="btn btn-danger btn-sm delete-book" data-id="${book.id}">Delete</button>
                                </td>
                             </tr>`;
                });
                $('#book-list').html(rows);
            } else {
                alert(data.message);
            }
        });
    }

    loadBooks();

    // Add book
    $('#add-book-form').submit(function(e) {
        e.preventDefault();
        $.post('api.php?action=add_book', {
            title: $('#add-title').val(),
            author: $('#add-author').val(),
            category: $('#add-category').val()
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
    $('#book-list').on('click', '.edit-book', function() {
        let id = $(this).data('id');
        $.get('api.php?action=list_books', function(data) {
            if (data.status === 'success') {
                let book = data.books.find(b => b.id == id);
                if (book) {
                    $('#edit-book-id').val(book.id);
                    $('#edit-title').val(book.title);
                    $('#edit-author').val(book.author);
                    $('#edit-category').val(book.category);
                    $('#editBookModal').modal('show');
                }
            }
        });
    });

    $('#edit-book-form').submit(function(e) {
        e.preventDefault();
        $.post('api.php?action=update_book', {
            id: $('#edit-book-id').val(),
            title: $('#edit-title').val(),
            author: $('#edit-author').val(),
            category: $('#edit-category').val()
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
    $('#book-list').on('click', '.delete-book', function() {
        if (confirm('Are you sure you want to delete this book?')) {
            let id = $(this).data('id');
            $.post('api.php?action=delete_book', { id: id }, function(data) {
                if (data.status === 'success') {
                    loadBooks();
                } else {
                    alert(data.message);
                }
            });
        }
    });
});
