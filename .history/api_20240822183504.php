<?php
session_start();
include 'conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$role = $_SESSION['role'];
// Function to check if the user is an admin
function isUserAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

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

    case 'list_authors':
        $authors = [];
        $query = "SELECT id, name FROM authors";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $authors[] = $row;
            }
            echo json_encode(['status' => 'success', 'authors' => $authors]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error fetching authors']);
        }
        break;

    case 'list_categories':
        $categories = [];
        $query = "SELECT id, name FROM categories";
        $result = $conn->query($query);
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            echo json_encode(['status' => 'success', 'categories' => $categories]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error fetching categories']);
        }
        break;

    case 'add_book':
        if (!isUserAdmin()) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit();
        }
        $title = $_POST['title'] ?? '';
        $author_id = $_POST['author_id'] ?? '';
        $category_id = $_POST['category_id'] ?? '';

        if ($title && $author_id && $category_id) {
            $query = $conn->prepare("INSERT INTO books (title, author_id, category_id) VALUES ('$title','$author_id', )");
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
        if (!isUserAdmin()) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit();
        }
        $id = $_POST['id'] ?? '';
        $title = $_POST['title'] ?? '';
        $author_id = $_POST['author_id'] ?? '';
        $category_id = $_POST['category_id'] ?? '';

        if ($id && $title && $author_id && $category_id) {
            $query = $conn->prepare("UPDATE books SET title = ?, author_id = ?, category_id = ? WHERE id = ?");
            $query->bind_param('siii', $title, $author_id, $category_id, $id);
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
        if (!isUserAdmin()) {
            echo json_encode(['status' => 'error', 'message' => 'Access denied']);
            exit();
        }
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
