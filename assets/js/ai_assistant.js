document.addEventListener('DOMContentLoaded', function () {
    const floatingBtn = document.getElementById('aiFloatingBtn');
    const chatBox = document.getElementById('aiChatBox');
    const chatClose = document.getElementById('aiChatClose');
    const welcomePopup = document.getElementById('aiWelcomePopup');
    const welcomeClose = document.getElementById('aiWelcomeClose');
    const chatForm = document.getElementById('aiChatForm');
    const chatInput = document.getElementById('aiChatInput');
    const chatMessages = document.getElementById('aiChatMessages');

    floatingBtn.addEventListener('click', function () {
        chatBox.classList.add('active');
        welcomePopup.style.display = 'none';
        chatInput.focus();
    });

    chatClose.addEventListener('click', function () {
        chatBox.classList.remove('active');
    });

    welcomeClose.addEventListener('click', function () {
        welcomePopup.style.display = 'none';
    });

    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();

        const message = chatInput.value.trim();

        if (message === '') {
            return;
        }

        appendMessage(message, 'user');
        chatInput.value = '';

        appendMessage('Đang suy nghĩ...', 'bot');

        fetch(window.FD_AI_CONFIG.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(response => response.json())
        .then(data => {
            const loadingMessages = chatMessages.querySelectorAll('.ai-bot');
            const lastBotMessage = loadingMessages[loadingMessages.length - 1];

            if (lastBotMessage && lastBotMessage.innerHTML === 'Đang suy nghĩ...') {
                lastBotMessage.innerHTML = data.reply;
            } else {
                appendMessage(data.reply, 'bot');
            }
        })
        .catch(() => {
            appendMessage('Xin lỗi, hiện tại mình chưa thể phản hồi. Bạn thử lại sau nhé.', 'bot');
        });
    });

    function appendMessage(text, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'ai-message ai-' + sender;
        messageDiv.innerHTML = text;

        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
});