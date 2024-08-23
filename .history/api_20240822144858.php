<?php
include 'conn.php';
header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'list_books':
        $books = [];
        $query = "SELECT * FROM books";
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
        if ($title && $author && $category) {
            $query = $conn->prepare("INSERT INTO books (title, author, category) VALUES (?, ?, ?)");
            $query->bind_param('sss', $title, $author, $category);
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
        if ($id && $title && $author && $category) {
            $query = $conn->prepare("UPDATE books SET title = ?, author = ?, category = ? WHERE id = ?");
            $query->bind_param('sssi', $title, $author, $category, $id);
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
