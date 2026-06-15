<style>
    .agent-float-btn {
        position: fixed;
        bottom: 28px;
        right: 28px;
        display: flex;
        align-items: center;
        background: #4C9BC8;
        border-radius: 50px;
        text-decoration: none;
        overflow: hidden;
        width: 68px;
        height: 68px;
        box-shadow: 0 4px 16px rgba(76, 155, 200, 0.35);
        transition: width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
            box-shadow 0.25s ease;
        z-index: 9999;
    }

    .agent-float-btn:hover {
        width: 210px;
        box-shadow: 0 6px 24px rgba(76, 155, 200, 0.5);
    }

    .agent-float-btn__icon {
        flex-shrink: 0;
        width: 62px;
        height: 62px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: -8px;
        /* geser logo ke kiri */
    }

    .agent-float-btn__icon img {
        width: 62px;
        /* sama persis dengan tinggi button */
        height: 62px;
        object-fit: cover;
        /* cover agar mengisi penuh tanpa distorsi */
        border-radius: 50px;
        /* sama dengan border-radius button */
        background: #fff;
        padding: 0;
        /* tidak perlu padding */
    }

    .agent-float-btn__label {
        white-space: nowrap;
        overflow: hidden;
        max-width: 0;
        opacity: 0;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        padding-right: 0;
        transition: max-width 0.35s cubic-bezier(0.4, 0, 0.2, 1),
            opacity 0.25s ease 0.1s,
            padding-right 0.3s ease;
    }

    .agent-float-btn:hover .agent-float-btn__label {
        max-width: 160px;
        opacity: 1;
        padding-right: 18px;
    }
</style>
<a href="{{ route('ai.kelompok') }}" class="agent-float-btn" aria-label="Buka VokasiTera Agent"
    title="Buka VokasiTera Agent">
    <span class="agent-float-btn__icon">
        <img src="{{ asset('assets/img/logoagent.png') }}" alt="VokasiTera Agent">
    </span>
    <span class="agent-float-btn__label">VokasiTera Agent</span>
</a>
