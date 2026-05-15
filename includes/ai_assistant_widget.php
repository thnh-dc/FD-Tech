<!-- AI ASSISTANT -->
<div class="ai-welcome-popup" id="aiWelcomePopup">
    <div class="ai-welcome-header">
        <strong> FD Bot</strong>
        <button type="button" id="aiWelcomeClose">&times;</button>
    </div>

    <div class="ai-welcome-body">
        <?php if (isset($_SESSION['user_id'])): ?>
            Xin chào <?= htmlspecialchars($_SESSION['username'] ??'bạn') ?>! Mình là FD Bot, hỏi mình nếu bạn cần hỗ trợ nhé.
        <?php else: ?>
            Xin chào! Mình chưa thể hỗ trợ vì bạn chưa đăng nhập.
            <div class="ai-auth-links">
                Hãy <a href="../auth/login.php">đăng nhập</a> nhé !
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="ai-chat-box" id="aiChatBox">
    <div class="ai-chat-header">
        <div>
            <strong>FD Bot</strong>
            <p>Sẵn sàng hỗ trợ bạn !</p>
        </div>
        <button type="button" id="aiChatClose">&times;</button>
    </div>

    <div class="ai-chat-messages" id="aiChatMessages">
        <div class="ai-message ai-bot">
            Xin chào! Bạn cần mình hỗ trợ gì?
            <br>
            Ví dụ: “Tìm sản phẩm hoặc “Tra cứu đơn hàng”.
        </div>
    </div>

    <form class="ai-chat-form" id="aiChatForm">
        <input type="text" id="aiChatInput" placeholder="Bạn cần hỗ trợ..." autocomplete="off">
        <button type="submit">
            <i class="fa-solid fa-paper-plane"></i>
        </button>
    </form>
</div>

<button type="button" class="ai-floating-btn" id="aiFloatingBtn">
    <img src="../assets/images/ai-bot.png" alt="Trợ lí AI">
</button>

<script>
    window.FD_AI_CONFIG = {
        endpoint: '../user/ai_assistant.php'
    };
</script>

<script src="../assets/js/ai_assistant.js"></script>