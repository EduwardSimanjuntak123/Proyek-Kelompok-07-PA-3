@extends('layouts.main')
@section('title', 'Chat Bot Otomatisasi')

@section('content')

    <style>
        /* CHAT CONTAINER */
        .chat-wrapper {
            height: 500px;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
            background: #f4f6f9;
        }

        /* MESSAGE ROW */
        .message {
            display: flex;
            margin-bottom: 15px;
            animation: fadeIn .3s ease;
        }

        .message.user {
            justify-content: flex-end;
        }

        /* AVATAR */
        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            margin-right: 10px;
        }

        .bot-avatar {
            background: #4C9BC8;
            color: white;
        }

        .user-avatar {
            background: #6c757d;
            color: white;
            margin-left: 10px;
        }

        /* CHAT BUBBLE */
        .bubble {
            padding: 12px 16px;
            border-radius: 15px;
            max-width: 60%;
            font-size: 14px;
        }

        .bot .bubble {
            background: white;
            border: 1px solid #ddd;
        }

        .user .bubble {
            background: #4C9BC8;
            color: white;
        }

        /* INPUT AREA */
        .chat-input {
            border-top: 1px solid #ddd;
            padding: 15px;
            background: white;
        }

        /* ANIMATION */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>

    <section class="section">
        <div class="section-body">

            <div class="card">

                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4>AI Chat Bot</h4>
                </div>

                <div class="card-body chat-wrapper">

                    <!-- CHAT AREA -->
                    <div class="chat-box" id="chatBox">

                        <!-- BOT MESSAGE -->
                        <div class="message bot">
                            <div class="avatar bot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>

                            <div class="bubble">
                                Halo 👋<br><br>
                                Saya adalah <b>Vokasi Tera AI Assistant</b>.<br>
                                Saya siap membantu Anda mengakses informasi akademik dengan lebih cepat.<br><br>

                                Anda dapat mengetik pertanyaan seperti:
                                <ul style="margin-top:8px; padding-left:18px;">
                                    <li>Informasi kelompok</li>
                                    <li>Data mahasiswa bimbingan</li>
                                    <li>Jadwal seminar</li>
                                    <li>Status pengajuan seminar</li>
                                </ul>

                                Silakan ketik pertanyaan Anda pada kolom chat di bawah.
                            </div>
                        </div>

                        <!-- USER MESSAGE -->
                        <div class="message user">

                            <div class="bubble">
                                Apa yang bisa Anda bantu?
                            </div>

                            <div class="avatar user-avatar">
                                <i class="fas fa-user"></i>
                            </div>

                        </div>

                        <!-- BOT MESSAGE -->
                        <div class="message bot">
                            <div class="avatar bot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>

                            <div class="bubble">
                                Saya dapat membantu memberikan informasi terkait sistem akademik
                                <b>Vokasi Tera</b>, seperti data kelompok, mahasiswa bimbingan,
                                jadwal seminar, dan informasi lainnya.<br><br>

                                Silakan ketik pertanyaan Anda, dan saya akan mencoba membantu.
                            </div>
                        </div>

                        <!-- INPUT -->
                        <div class="chat-input">
                            <div class="input-group">

                                <input type="text" id="messageInput" class="form-control" placeholder="Ketik pesan...">

                                <div class="input-group-append">
                                    <button class="btn btn-primary" onclick="sendMessage()">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>

                            </div>
                        </div>

                    </div>

                </div>

            </div>
    </section>

    <script>
        /* AUTO SCROLL */
        function scrollBottom() {
            let chatBox = document.getElementById("chatBox");
            chatBox.scrollTop = chatBox.scrollHeight;
        }

        scrollBottom();

        /* SEND MESSAGE UI */
        function sendMessage() {

            let input = document.getElementById("messageInput");
            let text = input.value.trim();

            if (text === "") return;

            let chatBox = document.getElementById("chatBox");

            let message = `
<div class="message user">
<div class="bubble">${text}</div>
<div class="avatar user-avatar">
<i class="fas fa-user"></i>
</div>
</div>
`;

            chatBox.innerHTML += message;

            input.value = "";

            scrollBottom();

        }
    </script>

@endsection
