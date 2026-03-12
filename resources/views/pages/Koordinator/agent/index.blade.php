@extends('layouts.main')
@section('title', 'AI Agent Kelompok')

@section('content')

    <style>
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

        .message {
            display: flex;
            margin-bottom: 15px;
        }

        .message.user {
            justify-content: flex-end;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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

        .chat-input {
            border-top: 1px solid #ddd;
            padding: 15px;
            background: white;
        }

        .quick-action {
            margin-top: 10px;
        }

        .quick-action button {
            margin-right: 8px;
        }
    </style>

    <section class="section">
        <div class="section-body">

            <div class="card">

                <div class="card-header">
                    <h4>AI Agent Pembentukan Kelompok</h4>
                </div>

                <div class="card-body chat-wrapper">

                    <div class="chat-box" id="chatBox">

                        <!-- BOT INTRO -->
                        <div class="message bot">

                            <div class="avatar bot-avatar">
                                <i class="fas fa-robot"></i>
                            </div>

                            <div class="bubble">

                                Halo 👋 <br><br>

                                Saya adalah <b>AI Agent Pembentuk Kelompok Mahasiswa</b>.<br><br>

                                Saya dapat membantu membuat kelompok mahasiswa secara otomatis
                                berdasarkan nilai akademik agar setiap kelompok seimbang.<br><br>

                                Silakan gunakan tombol di bawah atau ketik instruksi.

                                <div class="quick-action">

                                    <button class="btn btn-sm btn-primary" onclick="quickGenerate()">
                                        Generate Kelompok
                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                    <!-- INPUT -->
                    <div class="chat-input">

                        <div class="input-group">

                            <input type="text" id="messageInput" class="form-control"
                                placeholder="Contoh: Generate kelompok angkatan 2022 dengan 4 anggota">

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
        function scrollBottom() {

            let chatBox = document.getElementById("chatBox");

            chatBox.scrollTop = chatBox.scrollHeight;

        }

        function addBotMessage(text) {

            let chatBox = document.getElementById("chatBox");

            let message = `
<div class="message bot">
<div class="avatar bot-avatar">
<i class="fas fa-robot"></i>
</div>
<div class="bubble">${text}</div>
</div>
`;

            chatBox.innerHTML += message;

            scrollBottom();

        }

        function sendMessage() {

            let input = document.getElementById("messageInput");

            let text = input.value.trim();

            if (text === "") return;

            let chatBox = document.getElementById("chatBox");

            let userMessage = `
<div class="message user">
<div class="bubble">${text}</div>
<div class="avatar user-avatar">
<i class="fas fa-user"></i>
</div>
</div>
`;

            chatBox.innerHTML += userMessage;

            input.value = "";

            scrollBottom();

            /* RESPONSE SIMULATION */

            addBotMessage("AI sedang menganalisis mahasiswa... ⏳");

            setTimeout(() => {

                addBotMessage("Kelompok berhasil dibuat dan disimpan ke database ✅");

            }, 1500);

        }

        /* QUICK BUTTON */

        function quickGenerate() {

            addBotMessage("Silakan tentukan angkatan dan jumlah anggota per kelompok.");

        }
    </script>

@endsection
