<?php
require_once 'config.php';

header('Content-Type: text/html; charset=UTF-8');

$errors = [];
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form.html');
    exit;
}

$respondent_name = isset($_POST['respondent_name']) ? trim($_POST['respondent_name']) : '';
$service_rating = isset($_POST['service_rating']) ? (int)$_POST['service_rating'] : 0;
$recommendation_likelihood = isset($_POST['recommendation_likelihood']) ? (int)$_POST['recommendation_likelihood'] : 0;
$suggestions = isset($_POST['suggestions']) ? trim($_POST['suggestions']) : '';

if (empty($respondent_name)) {
    $errors[] = 'Имя респондента обязательно для заполнения';
} elseif (strlen($respondent_name) > 255) {
    $errors[] = 'Имя не должно превышать 255 символов';
}

if ($service_rating < 1 || $service_rating > 5) {
    $errors[] = 'Оценка сервиса должна быть от 1 до 5';
}

if ($recommendation_likelihood < 1 || $recommendation_likelihood > 10) {
    $errors[] = 'Оценка вероятности рекомендации должна быть от 1 до 10';
}

if (strlen($suggestions) > 1000) {
    $errors[] = 'Предложения не должны превышать 1000 символов';
}

if (empty($errors)) {
    $connection = getDatabaseConnection();
    
    if ($connection) {
        $stmt = $connection->prepare("INSERT INTO satisfaction_survey (respondent_name, service_rating, recommendation_likelihood, suggestions) VALUES (?, ?, ?, ?)");
        
        if ($stmt) {
            $stmt->bind_param("siis", $respondent_name, $service_rating, $recommendation_likelihood, $suggestions);
            
            if ($stmt->execute()) {
                $success_message = "Спасибо, {$respondent_name}! Ваш отзыв успешно сохранен.";
            } else {
                $errors[] = "Ошибка при сохранении данных: " . $stmt->error;
            }
            
            $stmt->close();
        } else {
            $errors[] = "Ошибка подготовки запроса: " . $connection->error;
        }
        
        closeDatabaseConnection($connection);
    } else {
        $errors[] = "Ошибка подключения к базе данных";
    }
}

function displayErrors($errors) {
    if (!empty($errors)) {
        echo '<div class="alert alert-danger" role="alert">';
        echo '<h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Обнаружены ошибки:</h4>';
        echo '<ul class="mb-0">';
        foreach ($errors as $error) {
            echo '<li>' . htmlspecialchars($error, ENT_QUOTES, 'UTF-8') . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }
}

function displaySuccess($message) {
    if (!empty($message)) {
        echo '<div class="alert alert-success" role="alert">';
        echo '<h4 class="alert-heading"><i class="fas fa-check-circle"></i> Успешно!</h4>';
        echo '<p class="mb-0">' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Результат обработки формы</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="styles.css?v=3" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="result-container">
            <div class="result-header">
                <h1><i class="fas fa-clipboard-check"></i> Результат обработки</h1>
                <p>Статус обработки вашего отзыва</p>
            </div>
            
            <?php if (!empty($errors)): ?>
                <?php displayErrors($errors); ?>
                
                <div class="text-center">
                    <a href="form.html" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Вернуться к форме
                    </a>
                </div>
            <?php else: ?>
                <?php displaySuccess($success_message); ?>
                
                
                <div class="data-summary">
                    <h5><i class="fas fa-info-circle"></i> Отправленные данные:</h5>
                    <div class="data-item">
                        <span class="data-label">Имя:</span>
                        <span class="data-value"><?php echo htmlspecialchars($respondent_name, ENT_QUOTES, 'UTF-8'); ?></span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Оценка сервиса:</span>
                        <span class="data-value">
                            <?php echo $service_rating; ?>/5 
                            <?php 
                            $stars = str_repeat('★', $service_rating) . str_repeat('☆', 5 - $service_rating);
                            echo $stars;
                            ?>
                        </span>
                    </div>
                    <div class="data-item">
                        <span class="data-label">Вероятность рекомендации:</span>
                        <span class="data-value"><?php echo $recommendation_likelihood; ?>/10</span>
                    </div>
                    <?php if (!empty($suggestions)): ?>
                    <div class="data-item">
                        <span class="data-label">Предложения:</span>
                        <span class="data-value"><?php echo nl2br(htmlspecialchars($suggestions, ENT_QUOTES, 'UTF-8')); ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="text-center">
                    <a href="form.html" class="btn-back">
                        <i class="fas fa-plus"></i> Заполнить еще один отзыв
                    </a>
                </div>
            <?php endif; ?>
            
            <div class="footer">
                <p>Спасибо за участие в опросе!</p>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
