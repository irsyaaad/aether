@extends('template.root')
@section('page-name', Request::segment(1))
@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row">
        <!-- User List -->
        <div class="col-md-4">
            <div id="user-list"
                style="display: flex; flex-direction: column; gap: 12px; max-height: 500px; overflow-y: auto; padding-right: 6px;">
            </div>
        </div>

        <!-- Chat Room -->
        <div class="col-md-8">
            <div id="admin-chat-messages"
                style="height: 600px; overflow-y: auto; background-color: #f8f9fa; border: 1px solid #dee2e6; padding: 12px; border-radius: 16px; display: flex; flex-direction: column;">
                <!-- Chat bubbles akan muncul di sini -->
            </div>

            <div style="display: flex; margin-top: 12px;">
                <input type="text" class="form-control" id="admin-chat-input" placeholder="Ketik balasan...">
                <button class="btn btn-primary" onclick="sendAdminReply()" style="margin-left: 8px;">Kirim</button>
            </div>
        </div>
    </div>
    <script>
        let selectedUserId = null;
        let refreshInterval = null;

        async function loadUserList() {
            const res = await fetch('/chat-users');
            const users = await res.json();
            console.log(users);
            const userList = document.getElementById('user-list');
            userList.innerHTML = '';

            users.forEach(u => {
                const card = document.createElement('div');
                card.style.padding = '10px 14px';
                card.style.border = '1px solid #dee2e6';
                card.style.borderRadius = '12px';
                card.style.boxShadow = '0 2px 6px rgba(0,0,0,0.05)';
                card.style.cursor = 'pointer';
                card.style.display = 'flex';
                card.style.alignItems = 'center';
                card.style.backgroundColor = selectedUserId === u.user_id ? '#e9f0ff' : '#fff';
                card.onclick = () => openChat(u.user_id);

                const icon = document.createElement('i');
                icon.className = 'fas fa-user-circle';
                icon.style.fontSize = '24px';
                icon.style.marginRight = '10px';
                icon.style.color = '#4e73df';

                const text = document.createElement('div');
                text.innerHTML = `${u.user.nm_user}<br>${u.user.email}`;

                card.appendChild(icon);
                card.appendChild(text);

                userList.appendChild(card);
            });
        }

        async function openChat(userId) {
            selectedUserId = userId;
            clearInterval(refreshInterval);
            await loadChatMessages();
            refreshInterval = setInterval(loadChatMessages, 3000);
        }

        async function loadChatMessages() {
            const res = await fetch(`/chats/${selectedUserId}`);
            const chats = await res.json();
            const chatBox = document.getElementById('admin-chat-messages');
            chatBox.innerHTML = '';

            if (chats.length === 0) {
                const notice = document.createElement('p');
                notice.textContent = "Belum ada pesan dari user ini.";
                notice.style.textAlign = 'center';
                notice.style.color = '#6c757d';
                notice.style.marginTop = '20px';
                chatBox.appendChild(notice);
            } else {
                chats.forEach(c => {
                    const bubble = document.createElement('div');
                    bubble.style.margin = '6px 0';
                    bubble.style.padding = '10px 14px';
                    bubble.style.borderRadius = '16px';
                    bubble.style.maxWidth = '75%';
                    bubble.style.wordWrap = 'break-word';
                    bubble.style.backgroundColor = c.sender === 'admin' ? '#4e73df' : '#e2e3e5';
                    bubble.style.color = c.sender === 'admin' ? 'white' : 'black';
                    bubble.style.alignSelf = c.sender === 'admin' ? 'flex-end' : 'flex-start';
                    bubble.textContent = c.message;
                    chatBox.appendChild(bubble);
                });
            }

            chatBox.scrollTop = chatBox.scrollHeight;
        }

        async function sendAdminReply() {
            const input = document.getElementById('admin-chat-input');
            const message = input.value.trim();
            if (!message) return;

            await fetch(`/chats/${selectedUserId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    message
                })
            });
            input.value = '';
            await loadChatMessages();
        }

        loadUserList();
    </script>
@endsection
