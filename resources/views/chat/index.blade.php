<x-app-layout>
    <x-slot name="header">
        <h2 style="font-size: 20px; font-weight: bold;">
            Chat
        </h2>
    </x-slot>

    <div style="background: #f3f4f6; min-height: 80vh; padding: 30px;">
        <div style="max-width: 1150px; margin: auto; display: flex; gap: 20px;">

            {{-- SIDEBAR --}}
            <div style="width: 32%; background: white; padding: 20px; border-radius: 6px;">
                <h3 style="font-size: 22px; font-weight: bold; margin-bottom: 20px;">
                    Daftar User
                </h3>

                @foreach ($users as $user)
                    <a href="{{ route('chat.index', ['user' => $user->id]) }}"
                       style="display: flex; justify-content: space-between; align-items: center; padding: 12px 8px; border-bottom: 1px solid #eee; text-decoration: none; color: black;">
                        <span>{{ $user->name }}</span>

                        @if ($user->last_seen && $user->last_seen->diffInMinutes(now()) < 5)
                            <span style="color: green; font-size: 13px;">online</span>
                        @else
                            <span style="color: gray; font-size: 13px;">offline</span>
                        @endif
                    </a>
                @endforeach

                <h3 style="font-size: 22px; font-weight: bold; margin-top: 30px; margin-bottom: 15px;">
                    Daftar Group
                </h3>

                @foreach ($groups as $group)
                    <a href="{{ route('chat.index', ['group' => $group->id]) }}"
                       style="display: block; padding: 12px 8px; border-bottom: 1px solid #eee; text-decoration: none; color: black;">
                        👥 {{ $group->name }}
                    </a>
                @endforeach

                <form action="{{ route('chat.group.create') }}" method="POST" style="margin-top: 20px;">
                    @csrf

                    <input
                        type="text"
                        name="name"
                        placeholder="Masukkan nama group"
                        required
                        style="width: 100%; border: 1px solid #ddd; padding: 12px; border-radius: 6px; margin-bottom: 10px;"
                    >

                    <button
                        type="submit"
                        style="width: 100%; background: #22c55e; color: white; padding: 12px; border-radius: 6px; font-weight: bold;"
                    >
                        Tambah Group
                    </button>
                </form>
            </div>

            {{-- CHAT AREA --}}
            <div style="width: 68%; background: white; padding: 20px; border-radius: 6px;">
                <h2 style="font-size: 26px; font-weight: bold; margin-bottom: 15px;">
                    {{ $chatTitle }}
                </h2>

                <div id="chat-box" style="height: 380px; border: 1px solid #e5e7eb; background: #f9fafb; padding: 15px; overflow-y: auto; margin-bottom: 15px;">
                    @if ($conversation)
                        @forelse ($messages as $message)
                            <div style="margin-bottom: 12px;">
                                <strong>{{ $message->user->name }}:</strong>
                                {{ $message->message }}
                            </div>
                        @empty
                            <p id="empty-message" style="color: #777;">Belum ada pesan.</p>
                        @endforelse
                    @else
                        <p style="color: #777;">Silakan pilih user atau group dulu.</p>
                    @endif
                </div>

                @if ($conversation)
                    <form id="chat-form" action="{{ route('chat.send') }}" method="POST" style="display: flex; gap: 10px;">
                        @csrf

                        <input type="hidden" name="conversation_id" value="{{ $conversation->id }}">

                        @if (request('user'))
                            <input type="hidden" name="user_id" value="{{ request('user') }}">
                        @endif

                        @if (request('group'))
                            <input type="hidden" name="group_id" value="{{ request('group') }}">
                        @endif

                        <input
                            id="message-input"
                            type="text"
                            name="message"
                            placeholder="Ketik pesan..."
                            required
                            autocomplete="off"
                            style="flex: 1; border: 1px solid #ddd; padding: 12px; border-radius: 6px;"
                        >

                        <button
                            type="submit"
                            style="background: #2563eb; color: white; padding: 12px 25px; border-radius: 6px; font-weight: bold;"
                        >
                            Kirim
                        </button>
                    </form>
                @else
                    <p style="color: #777;">Pilih chat untuk mulai mengirim pesan.</p>
                @endif
            </div>

        </div>
    </div>

    @if ($conversation)
        <script type="module">
            const chatBox = document.getElementById('chat-box');
            const chatForm = document.getElementById('chat-form');
            const messageInput = document.getElementById('message-input');

            function scrollChatToBottom() {
                if (chatBox) {
                    chatBox.scrollTop = chatBox.scrollHeight;
                }
            }

            function addMessageToChat(userName, messageText) {
                const emptyMessage = document.getElementById('empty-message');

                if (emptyMessage) {
                    emptyMessage.remove();
                }

                const messageElement = document.createElement('div');
                messageElement.style.marginBottom = '12px';
                messageElement.innerHTML = `<strong>${userName}:</strong> ${messageText}`;

                chatBox.appendChild(messageElement);
                scrollChatToBottom();
            }

            scrollChatToBottom();

            if (chatForm) {
                chatForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    const formData = new FormData(chatForm);
                    const messageText = messageInput.value.trim();

                    if (messageText === '') {
                        return;
                    }

                    fetch(chatForm.action, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': formData.get('_token'),
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            addMessageToChat(data.message.user.name, data.message.message);
                            messageInput.value = '';
                        }
                    })
                    .catch(error => {
                        console.log(error);
                        alert('Pesan gagal dikirim. Cek terminal Laravel.');
                    });
                });
            }

            setTimeout(function () {
                if (window.Echo) {
                    window.Echo.channel('chat.{{ $conversation->id }}')
                        .listen('.message.sent', function (event) {
                            if (event.message.user.id != {{ auth()->id() }}) {
                                addMessageToChat(event.message.user.name, event.message.message);
                            }
                        });
                } else {
                    console.log('Echo belum aktif');
                }
            }, 1000);
        </script>
    @endif
</x-app-layout>