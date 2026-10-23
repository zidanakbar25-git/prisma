{{-- ========================================================= --}}
{{-- DIALOG KONFIRMASI (dipakai bersama oleh semua halaman) --}}
{{-- Simpan sebagai: resources/views/partials/confirm-dialog.blade.php --}}
{{-- ========================================================= --}}

<div
    id="confirm-dialog"
    class="fixed inset-0 z-[60] hidden overflow-y-auto"
    aria-hidden="true"
>

    <div
        id="confirm-backdrop"
        class="flex min-h-full items-center justify-center bg-black/40 px-4 py-8"
    >

        <div
            class="w-full max-w-md rounded-2xl border border-[#E5E9F0] bg-white p-6 shadow-xl"
            role="alertdialog"
            aria-modal="true"
            aria-labelledby="confirm-title"
            aria-describedby="confirm-message"
        >

            <div class="flex items-start gap-4">

                <div
                    id="confirm-icon"
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-full"
                ></div>

                <div class="min-w-0">

                    <h2
                        id="confirm-title"
                        class="text-lg font-semibold text-[#24332A]"
                    ></h2>

                    <p
                        id="confirm-message"
                        class="mt-1.5 text-sm leading-6 text-[#68746D]"
                    ></p>

                </div>

            </div>

            <div class="mt-6 flex items-center justify-end gap-3">

                <button
                    type="button"
                    id="confirm-cancel"
                    class="rounded-lg border border-[#E5E9F0] bg-white px-4 py-2.5 text-sm font-medium text-[#24332A] transition hover:bg-[#F1F5F9]"
                >
                    Batal
                </button>

                <button
                    type="button"
                    id="confirm-ok"
                    class="rounded-lg px-4 py-2.5 text-sm font-medium text-white transition"
                >
                    Hapus
                </button>

            </div>

        </div>

    </div>

</div>

<script>
    (function () {

        const root = document.getElementById('confirm-dialog');

        if (!root) {
            return;
        }

        const backdrop = document.getElementById('confirm-backdrop');
        const iconBox = document.getElementById('confirm-icon');
        const titleEl = document.getElementById('confirm-title');
        const messageEl = document.getElementById('confirm-message');
        const cancelBtn = document.getElementById('confirm-cancel');
        const okBtn = document.getElementById('confirm-ok');

        const svgAttr = 'width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24"';

        const tones = {
            danger: {
                iconBg: '#FEF2F2',
                iconColor: '#DC2626',
                button: '#DC2626',
                buttonHover: '#B91C1C',
                icon: `<svg ${svgAttr}><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>`
            },
            primary: {
                iconBg: '#E3EEE6',
                iconColor: '#234936',
                button: '#234936',
                buttonHover: '#315A45',
                icon: `<svg ${svgAttr}><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>`
            }
        };

        let resolver = null;
        let wasLocked = false;
        let lastFocus = null;

        // Posisikan di tengah area konten (tanpa sidebar)
        function center() {
            const anchor = document.querySelector('[data-page-anchor]');

            if (!anchor) {
                backdrop.style.paddingLeft = '';
                return;
            }

            const rect = anchor.getBoundingClientRect();
            const contentCenter = rect.left + rect.width / 2;

            backdrop.style.paddingLeft =
                Math.max(16, 2 * contentCenter - root.clientWidth + 16) + 'px';
        }

        function isOpen() {
            return !root.classList.contains('hidden');
        }

        function close(result) {
            root.classList.add('hidden');
            root.setAttribute('aria-hidden', 'true');

            // Jangan buka kunci scroll kalau popup lain masih terbuka
            if (!wasLocked) {
                document.body.classList.remove('overflow-hidden');
            }

            const done = resolver;

            resolver = null;

            if (done) {
                done(result);
            }

            if (lastFocus && typeof lastFocus.focus === 'function') {
                lastFocus.focus();
            }
        }

        function open(options) {
            options = options || {};

            return new Promise(function (resolve) {
                if (resolver) {
                    resolver(false);
                }

                resolver = resolve;
                lastFocus = document.activeElement;

                const tone = tones[options.tone] || tones.danger;

                iconBox.style.backgroundColor = tone.iconBg;
                iconBox.style.color = tone.iconColor;
                iconBox.innerHTML = tone.icon;

                titleEl.textContent = options.title || 'Konfirmasi';
                messageEl.textContent = options.message || 'Apakah Anda yakin?';
                okBtn.textContent = options.confirmLabel || 'Ya';

                okBtn.style.backgroundColor = tone.button;
                okBtn.onmouseenter = function () {
                    okBtn.style.backgroundColor = tone.buttonHover;
                };
                okBtn.onmouseleave = function () {
                    okBtn.style.backgroundColor = tone.button;
                };

                wasLocked = document.body.classList.contains('overflow-hidden');
                document.body.classList.add('overflow-hidden');

                root.classList.remove('hidden');
                root.setAttribute('aria-hidden', 'false');

                center();

                // Aksi yang berbahaya: fokus awal di "Batal"
                (options.tone === 'primary' ? okBtn : cancelBtn).focus();
            });
        }

        okBtn.addEventListener('click', function () {
            close(true);
        });

        cancelBtn.addEventListener('click', function () {
            close(false);
        });

        backdrop.addEventListener('click', function (event) {
            if (event.target === backdrop) {
                close(false);
            }
        });

        // Esc hanya menutup dialog ini, bukan popup di belakangnya
        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && isOpen()) {
                event.stopImmediatePropagation();
                close(false);
            }
        }, true);

        window.addEventListener('resize', function () {
            if (isOpen()) {
                center();
            }
        });

        // Bisa dipanggil dari JavaScript halaman:
        // window.openConfirm({ title, message, confirmLabel, tone }).then(ok => ...)
        window.openConfirm = open;

        // Form dengan atribut data-confirm-message otomatis memakai dialog ini
        document.addEventListener('submit', function (event) {
            const form = event.target;

            if (!(form instanceof HTMLFormElement) || !form.dataset.confirmMessage) {
                return;
            }

            event.preventDefault();

            open({
                title: form.dataset.confirmTitle,
                message: form.dataset.confirmMessage,
                confirmLabel: form.dataset.confirmButton,
                tone: form.dataset.confirmTone
            }).then(function (ok) {
                if (ok) {
                    form.submit();
                }
            });
        }, true);

    })();
</script>