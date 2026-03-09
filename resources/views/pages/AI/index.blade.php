@extends('layouts.main')
@section('title', 'CHAT AGENT')

@section('content')

    <style>
        .chat-wrapper {
            height: 75vh;
            display: flex;
            flex-direction: column;
        }

        .chat-box {
            flex: 1;
            overflow-y: auto;
            padding: 25px;
            background: #f7f9fc;
            border-radius: 10px;
        }

        .message {
            display: flex;
            margin-bottom: 18px;
        }

        .message.bot {
            justify-content: flex-start;
        }

        .message.user {
            justify-content: flex-end;
        }

        .bubble {
            padding: 12px 18px;
            border-radius: 14px;
            max-width: 65%;
            font-size: 14px;
            line-height: 1.6;
        }

        .user .bubble {
            background: #5865f2;
            color: white;
        }

        .bot .bubble {
            background: white;
            border: 1px solid #eee;
        }

        .chat-input {
            display: flex;
            gap: 10px;
            padding: 15px;
            border-top: 1px solid #eee;
        }

        .chat-input input {
            flex: 1;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .chat-input input:focus {
            outline: none;
            border-color: #5865f2;
        }

        .chat-input button {
            background: #5865f2;
            color: white;
            border: none;
            padding: 0 22px;
            border-radius: 8px;
            font-weight: 500;
        }

        .typing {
            font-style: italic;
            color: #999;
        }

        /* ACTION MENU */

        .action-menu {
            display: flex;
            gap: 10px;
            padding: 15px;
            border-bottom: 1px solid #eee;
            flex-wrap: wrap;
        }

        .action-btn {
            background: #eef2ff;
            border: 1px solid #5865f2;
            color: #5865f2;
            padding: 8px 14px;
            border-radius: 8px;
            font-size: 13px;
            cursor: pointer;
            transition: 0.2s;
        }

        .action-btn:hover {
            background: #5865f2;
            color: white;
        }
    </style>

    <section class="section">

        <div class="section-header">
            <h1>🤖 AI Assistant VokasiTera</h1>
        </div>

        <div class="section-body">

            <div class="row">
                <div class="col-12">

                    <div class="card">

                        <div class="chat-wrapper">

                            <!-- ACTION MENU -->

                            <div class="action-menu">

                                <button class="action-btn"
                                    onclick="runAction('generate kelompok mahasiswa berdasarkan nilai')">
                                    👥 Generate Kelompok
                                </button>

                                <button class="action-btn" onclick="runAction('tentukan pembimbing mahasiswa')">
                                    🎓 Tentukan Pembimbing
                                </button>

                                <button class="action-btn" onclick="runAction('tentukan penguji sidang')">
                                    🧑‍🏫 Tentukan Penguji
                                </button>

                                <button class="action-btn" onclick="runAction('tampilkan mahasiswa bimbingan saya')">
                                    📋 Mahasiswa Bimbingan
                                </button>

                            </div>

                            <!-- CHAT BOX -->

                            <div id="chat-box" class="chat-box">

                                <div class="message bot">
                                    <div class="bubble">
                                        Halo 👋 saya <b>AI Assistant VokasiTera</b><br>
                                        Silakan tanyakan apa saja terkait Proyek Akhir.
                                    </div>
                                </div>

                            </div>

                            <!-- INPUT -->

                            <div class="chat-input">

                                <input type="text" id="message" placeholder="Tanyakan sesuatu..."
                                    onkeypress="if(event.key==='Enter'){sendMessage()}">

                                <button onclick="sendMessage()">
                                    Kirim
                                </button>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <!-- SESSION DATA -->

    <script>
        let userId = "{{ session('user_id') }}";
        let role = "{{ session('role') }}";
    </script>

    <script>
        function addMessage(text, type) {

            let chat = document.getElementById("chat-box");

            let html = `
<div class="message ${type}">
<div class="bubble">${text}</div>
</div>
`;

            chat.insertAdjacentHTML("beforeend", html);

            chat.scrollTop = chat.scrollHeight;

        }

        function runAction(prompt) {

            let input = document.getElementById("message");

            input.value = prompt;

            sendMessage();

        }

        function sendMessage() {

            let input = document.getElementById("message");

            let message = input.value;

            if (message.trim() === "") return;

            addMessage(message, "user");

            input.value = "";

            addMessage("<span class='typing'>AI sedang mengetik...</span>", "bot");

            fetch("{{ route('ai.chat') }}", {

                    method: "POST",

                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },

                    body: JSON.stringify({

                        user_id: userId,
                        role: role,
                        message: message

                    })

                })

                .then(res => res.json())

                .then(data => {

                    let chat = document.getElementById("chat-box");

                    chat.lastChild.remove();

                    addMessage(data.reply, "bot");

                });

        }
    </script>

@endsection
