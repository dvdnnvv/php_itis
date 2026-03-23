<?php
session_start();

$uri = $_SERVER['REQUEST_URI'];
$path = parse_url($uri, PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

if (!isset($_SESSION['tasks'])) {
    $_SESSION['tasks'] = [];
}

function getAllTasks() {
    return $_SESSION['tasks'] ?? [];
}

function getTaskById($id) {
    return $_SESSION['tasks'][$id] ?? null;
}

function saveTask($id, $data) {
    $_SESSION['tasks'][$id] = $data;
}

function deleteTask($id) {
    if (isset($_SESSION['tasks'][$id])) {
        $task = $_SESSION['tasks'][$id];
        if (!empty($task['image']) && file_exists($task['image'])) {
            unlink($task['image']);
        }
        unset($_SESSION['tasks'][$id]);
    }
}

function getNextId() {
    $tasks = getAllTasks();
    return empty($tasks) ? 1 : max(array_keys($tasks)) + 1;
}

function uploadImage($file, $oldImage = null) {
    if ($file['error'] === UPLOAD_ERR_NO_FILE) {
        return $oldImage;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    if ($file['size'] > 2 * 1024 * 1024) {
        return null;
    }
    
    $allowed = ['image/jpeg', 'image/png', 'image/gif'];
    $type = mime_content_type($file['tmp_name']);
    if (!in_array($type, $allowed)) {
        return null;
    }
    
    if ($oldImage && file_exists($oldImage)) {
        unlink($oldImage);
    }
    
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $ext;
    $path = 'uploads/' . $filename;
    
    move_uploaded_file($file['tmp_name'], $path);
    return $path;
}

function validateTask($data, $file) {
    $errors = [];
    
    if (empty($data['title'])) {
        $errors['title'] = 'Заголовок обязателен';
    } elseif (strlen($data['title']) < 3) {
        $errors['title'] = 'Заголовок должен быть минимум 3 символа';
    }
    
    if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors['image'] = 'Ошибка загрузки файла';
        } elseif ($file['size'] > 2 * 1024 * 1024) {
            $errors['image'] = 'Файл не должен превышать 2 МБ';
        } else {
            $type = mime_content_type($file['tmp_name']);
            if (!in_array($type, ['image/jpeg', 'image/png', 'image/gif'])) {
                $errors['image'] = 'Допустимы только JPG, PNG, GIF';
            }
        }
    }
    
    return $errors;
}

function showTaskList() {
    $tasks = getAllTasks();
    $success = $_SESSION['success'] ?? null;
    $error = $_SESSION['error'] ?? null;
    unset($_SESSION['success'], $_SESSION['error']);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Список задач</title>
    </head>
    <body>
        <h1>Список задач</h1>
        
        <?php if ($success): ?>
            <p style="color: green;"><?= htmlspecialchars($success) ?></p>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <p style="color: red;"><?= htmlspecialchars($error) ?></p>
        <?php endif; ?>
        
        <p><a href="/tasks/create">Создать задачу</a></p>
        
        <hr>
        
        <?php if (empty($tasks)): ?>
            <p>Нет задач</p>
        <?php else: ?>
            <?php foreach ($tasks as $task): ?>
                <div style="border: 1px solid #ccc; padding: 10px; margin: 10px 0;">
                    <h3><?= htmlspecialchars($task['title']) ?></h3>
                    
                    <?php if (!empty($task['description'])): ?>
                        <p><?= nl2br(htmlspecialchars($task['description'])) ?></p>
                    <?php endif; ?>
                    
                    <?php if (!empty($task['image']) && file_exists($task['image'])): ?>
                        <img src="/<?= htmlspecialchars($task['image']) ?>" style="max-width: 150px;">
                    <?php endif; ?>
                    
                    <p>
                        <a href="/tasks/edit/<?= $task['id'] ?>">Редактировать</a>
                        |
                        <form method="POST" action="/tasks/delete/<?= $task['id'] ?>" style="display: inline;">
                            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                            <button type="submit" onclick="return confirm('Удалить?')">Удалить</button>
                        </form>
                    </p>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </body>
    </html>
    <?php
}

function showTaskForm($task = null, $errors = [], $old = []) {
    $isEdit = $task !== null;
    $title = $task['title'] ?? $old['title'] ?? '';
    $description = $task['description'] ?? $old['description'] ?? '';
    $image = $task['image'] ?? null;
    $token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $token;
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title><?= $isEdit ? 'Редактировать' : 'Создать' ?> задачу</title>
    </head>
    <body>
        <h1><?= $isEdit ? 'Редактировать задачу' : 'Создать задачу' ?></h1>
        
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $token ?>">
            
            <p>
                <label>Заголовок:</label><br>
                <input type="text" name="title" value="<?= htmlspecialchars($title) ?>" size="50">
                <?php if (isset($errors['title'])): ?>
                    <br><span style="color: red;"><?= $errors['title'] ?></span>
                <?php endif; ?>
            </p>
            
            <p>
                <label>Описание:</label><br>
                <textarea name="description" rows="5" cols="50"><?= htmlspecialchars($description) ?></textarea>
            </p>
            
            <p>
                <label>Изображение:</label><br>
                <input type="file" name="image" accept="image/*">
                <?php if (isset($errors['image'])): ?>
                    <br><span style="color: red;"><?= $errors['image'] ?></span>
                <?php endif; ?>
            </p>
            
            <?php if ($image && file_exists($image)): ?>
                <p>
                    <label>Текущее изображение:</label><br>
                    <img src="/<?= $image ?>" style="max-width: 150px;">
                </p>
            <?php endif; ?>
            
            <p>
                <button type="submit">Сохранить</button>
                <a href="/">Отмена</a>
            </p>
        </form>
    </body>
    </html>
    <?php
}

if ($method === 'GET' && $path === '/') {
    showTaskList();
    exit;
}

if ($method === 'GET' && $path === '/tasks/create') {
    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);
    showTaskForm(null, $errors, $old);
    exit;
}

if ($method === 'POST' && $path === '/tasks/create') {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Ошибка токена';
        header('Location: /');
        exit;
    }
    
    $errors = validateTask($_POST, $_FILES['image'] ?? null);
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header('Location: /tasks/create');
        exit;
    }
    
    $imagePath = uploadImage($_FILES['image'] ?? null);
    $id = getNextId();
    
    saveTask($id, [
        'id' => $id,
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? '',
        'image' => $imagePath
    ]);
    
    $_SESSION['success'] = 'Задача создана';
    header('Location: /');
    exit;
}

if ($method === 'GET' && preg_match('#^/tasks/edit/(\d+)$#', $path, $matches)) {
    $id = (int)$matches[1];
    $task = getTaskById($id);
    
    if (!$task) {
        http_response_code(404);
        echo 'Задача не найдена';
        exit;
    }
    
    $errors = $_SESSION['errors'] ?? [];
    $old = $_SESSION['old'] ?? [];
    unset($_SESSION['errors'], $_SESSION['old']);
    showTaskForm($task, $errors, $old);
    exit;
}

if ($method === 'POST' && preg_match('#^/tasks/edit/(\d+)$#', $path, $matches)) {
    $id = (int)$matches[1];
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Ошибка токена';
        header('Location: /');
        exit;
    }
    
    $task = getTaskById($id);
    
    if (!$task) {
        $_SESSION['error'] = 'Задача не найдена';
        header('Location: /');
        exit;
    }
    
    $errors = validateTask($_POST, $_FILES['image'] ?? null);
    
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header('Location: /tasks/edit/' . $id);
        exit;
    }
    
    $imagePath = uploadImage($_FILES['image'] ?? null, $task['image']);
    
    saveTask($id, [
        'id' => $id,
        'title' => $_POST['title'],
        'description' => $_POST['description'] ?? '',
        'image' => $imagePath
    ]);
    
    $_SESSION['success'] = 'Задача обновлена';
    header('Location: /');
    exit;
}

if ($method === 'POST' && preg_match('#^/tasks/delete/(\d+)$#', $path, $matches)) {
    $id = (int)$matches[1];
    
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== ($_SESSION['csrf_token'] ?? '')) {
        $_SESSION['error'] = 'Ошибка токена';
        header('Location: /');
        exit;
    }
    
    deleteTask($id);
    $_SESSION['success'] = 'Задача удалена';
    header('Location: /');
    exit;
}

http_response_code(404);
echo '404 Страница не найдена';