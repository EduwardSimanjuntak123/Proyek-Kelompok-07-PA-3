<style>
    .agent-float-btn {
        position: fixed;
        bottom: 28px;
        right: 28px;
        z-index: 999;

        display: flex;
        align-items: center;
        justify-content: center;

        width: 56px;
        height: 56px;

        background: linear-gradient(135deg, #2F7DAE 0%, #4C9BC8 50%, #6DB8E0 100%);
        border-radius: 50%;
        text-decoration: none;
        box-shadow: 0 4px 20px rgba(76, 155, 200, 0.4);

        animation: agent-slide-up 0.5s cubic-bezier(.34, 1.56, .64, 1) both;
        transition: transform 0.25s cubic-bezier(.34, 1.56, .64, 1),
            box-shadow 0.25s ease;
    }

    .agent-float-btn:hover {
        transform: translateY(-3px) scale(1.08);
        box-shadow: 0 8px 28px rgba(76, 155, 200, 0.5);
        text-decoration: none;
    }

    .agent-float-btn:active {
        transform: scale(0.95);
        box-shadow: 0 2px 10px rgba(76, 155, 200, 0.3);
    }

    .agent-float-btn__icon {
        position: relative;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: -7px;
    }

    .agent-float-btn__icon img {
        width: 36px;
        height: 36px;
        object-fit: contain;
        /* Hapus filter — biarkan logo tampil dengan warna aslinya */
        filter: none;
        display: block;
    }

    .agent-float-btn__pulse {
        position: absolute;
        inset: 0;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.55);
        animation: agent-pulse 2s ease-out infinite;
        pointer-events: none;
    }

    /* .agent-float-btn__dot {
        position: absolute;
        bottom: 6px;
        right: 6px;
        width: 10px;
        height: 10px;
        background: #A8EDCC;
        border-radius: 50%;
        border: 2px solid white;
        animation: agent-blink 1.6s ease-in-out infinite;
        pointer-events: none;
    } */

    @keyframes agent-slide-up {
        from {
            transform: translateY(80px);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    @keyframes agent-pulse {
        0% {
            transform: scale(1);
            opacity: 0.7;
        }

        100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }

    @keyframes agent-blink {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(168, 237, 204, 0.7);
        }

        50% {
            box-shadow: 0 0 0 4px rgba(168, 237, 204, 0);
        }
    }

    @media (max-width: 480px) {
        .agent-float-btn {
            bottom: 20px;
            right: 16px;
            width: 50px;
            height: 50px;
        }

        .agent-float-btn__icon img {
            width: 30px;
            height: 30px;
        }
    }
</style>

<a href="{{ route('ai.kelompok') }}" class="agent-float-btn" aria-label="Buka VokasiTera Agent"
    title="Buka VokasiTera Agent">

    <span class="agent-float-btn__pulse"></span>
    <span class="agent-float-btn__dot"></span>

    <span class="agent-float-btn__icon">
        <img src="{{ asset('assets/img/logoagent.png') }}" alt="VokasiTera Agent">
    </span>

</a>
