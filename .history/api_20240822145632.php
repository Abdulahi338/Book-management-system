<?php
include 'conn.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list_books':
        $books = [];
        $query = "SELECT books.id, books.title, authors.name AS author, categories.name AS category 
                  FROM books 
                  JOIN authors ON books.author_id = authors.id 
                  JOIN categories ON books.category_id = categories.id";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $books[] = $row;
            }
            echo json_encode(['status' => 'success', 'books' => $books]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error fetching books']);
        }
        break;

    case 'add_book':
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $category = $_POST['category'] ?? '';
        
        // Fetch author_id and category_id
        $author_query = $conn->prepare("SELECT id FROM authors WHERE name = ?");
        $author_query->bind_param('s', $author);
        $author_query->execute();
        $author_result = $author_query->get_result();
        $author_row = $author_result->fetch_assoc();
        $author_id = $author_row ? $author_row['id'] : null;

        $category_query = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $category_query->bind_param('s', $category);
        $category_query->execute();
        $category_result = $category_query->get_result();
        $category_row = $category_result->fetch_assoc();
        $category_id = $category_row ? $category_row['id'] : null;

        if ($title && $author_id && $category_id) {
            $query = $conn->prepare("INSERT INTO books (title, author_id, category_id) VALUES (?, ?, ?)");
            $query->bind_param('sii', $title, $author_id, $category_id);
            if ($query->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Book added successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error adding book']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        }
        break;

    case 'update_book':
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'] ?? '';
        $author = $_POST['author'] ?? '';
        $category = $_POST['category'] ?? '';
        
        // Fetch author_id and category_id
        $author_query = $conn->prepare("SELECT id FROM authors WHERE name = ?");
        $author_query->bind_param('s', $author);
        $author_query->execute();
        $author_result = $author_query->get_result();
        $author_row = $author_result->fetch_assoc();
        $author_id = $author_row ? $author_row['id'] : null;

        $category_query = $conn->prepare("SELECT id FROM categories WHERE name = ?");
        $category_query->bind_param('s', $category);
        $category_query->execute();
        $category_result = $category_query->get_result();
        $category_row = $category_result->fetch_assoc();
        $category_id = $category_row ? $category_row['id'] : null;

        if ($id && $title && $author_id && $category_id) {
            $query = $conn->prepare("UPDATE books SET title = ?, author_id = ?, category_id = ? WHERE id = ?");
            $query->bind_param('sii', $title, $author_id, $category_id, $id);
            if ($query->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Book updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error updating book']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
        }
        break;

    case 'delete_book':
        $id = $_POST['id'] ?? '';
        if ($id) {
            $query = $conn->prepare("DELETE FROM books WHERE id = ?");
            $query->bind_param('i', $id);
            if ($query->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Book deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error deleting book']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ID is required']);
        }
        break;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
        break;
}

$conn->close();
?>
