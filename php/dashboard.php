<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>StudyMateAI</title>

<link rel="stylesheet" href="css/dashboard.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/styles/github-dark.min.css">

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.11.1/highlight.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>

<body>

<div class="container">

    <!-- Sidebar -->

    <div class="sidebar">

        <h2>🎓 StudyMateAI</h2>

        <button id="newChat">
            <i class="fa fa-plus"></i>
            New Chat
        </button>

        <div class="history">

    <h3>Recent Chats</h3>

    <ul id="historyList">

    </ul>

</div>

        <div class="profile">

            <p>

                <i class="fa fa-user"></i>

                <?php echo htmlspecialchars($_SESSION['user_name']); ?>

            </p>

            <a href="logout.php">

                Logout

            </a>

        </div>

    </div>

    <!-- Chat Area -->
     

    <div class="chat-container">

        <div class="chat-header">

            <h2>StudyMateAI</h2>

            <span>Your AI Study Assistant</span>

        </div>

        <div id="chatBox" class="chat-box">

            <div class="bot-message">

                👋 Hello!

                Ask me anything :


            </div>

        </div>

        <div class="chat-input">

            <textarea
id="message"
rows="2"
placeholder="Ask StudyMateAI anything..."></textarea>

            <button id="sendBtn">

                <i class="fa fa-paper-plane"></i>

            </button>

        </div>

    </div>

</div>
<button id="exportPdf">
Export PDF
</button>
<button id="themeBtn">
🌙
</button>
<div class="feedback">

<button>👍</button>

<button>👎</button>

</div>

<script src="js/script.js"></script>

</body>
</html>