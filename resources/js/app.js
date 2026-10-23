document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('activity-calendar');

    if (!calendarEl) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Elements & Data
    |--------------------------------------------------------------------------
    */

    const eventsData = document.getElementById('calendar-events-data');

    const events = eventsData
        ? JSON.parse(eventsData.textContent || '[]')
        : [];

    const calendarGrid = document.getElementById('calendar-grid');
    const monthTitle = document.getElementById('calendar-month-title');
    const selectedDateTitle = document.getElementById('selected-date-title');
    const activityList = document.getElementById('activity-list');
    const previousMonthButton = document.getElementById('previous-month');
    const nextMonthButton = document.getElementById('next-month');

    const createModal = document.getElementById('create-activity-modal');
    const detailModal = document.getElementById('detail-activity-modal');
    const editModal = document.getElementById('edit-activity-modal');

    const openCreateButton = document.getElementById('open-create-activity');
    const openEditFromDetailButton = document.getElementById('open-edit-from-detail');

    const deleteActivityForm = document.getElementById('delete-activity-form');
    const editActivityForm = document.getElementById('edit-activity-form');

    const createEndTime = document.getElementById('create-end-time');
    const createNoEndTime = document.getElementById('create-no-end-time');
    const editEndTime = document.getElementById('edit-end-time');
    const editNoEndTime = document.getElementById('edit-no-end-time');

    const viewTabs = document.querySelectorAll('[data-view-tab]');
    const listView = document.getElementById('list-view');
    const monthList = document.getElementById('month-list');
    const listMonthTitle = document.getElementById('list-month-title');
    const listMonthCount = document.getElementById('list-month-count');
    const listPreviousButton = document.getElementById('list-previous-month');
    const listNextButton = document.getElementById('list-next-month');
    const listTodayButton = document.getElementById('list-today-month');

    const historyView = document.getElementById('history-view');
    const historyList = document.getElementById('history-list');
    const historyMonth = document.getElementById('history-month');
    const historyStart = document.getElementById('history-start');
    const historyEnd = document.getElementById('history-end');
    const historyReset = document.getElementById('history-reset');
    const historyExport = document.getElementById('history-export');

    const exportModal = document.getElementById('export-activity-modal');
    const openExportButton = document.getElementById('open-export-modal');
    const exportForm = document.getElementById('export-form');
    const exportMonthSection = document.getElementById('export-month-section');
    const exportRangeSection = document.getElementById('export-range-section');
    const exportError = document.getElementById('export-error');

    // Hanya kabag yang punya modal edit + form hapus (dirender Blade).
    const canManage = !!(editModal && deleteActivityForm);

    const monthNames = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];

    const dayNames = [
        'Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'
    ];

    const iconAttr = 'width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24" style="flex-shrink:0"';

    const icons = {
        clock: `<svg ${iconAttr}><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>`,
        pin: `<svg ${iconAttr}><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0z"/><circle cx="12" cy="10" r="3"/></svg>`,
        edit: `<svg ${iconAttr}><path d="M12 20h9M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/></svg>`,
        trash: `<svg ${iconAttr}><path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/></svg>`
    };

    const today = new Date();

    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();
    let selectedDate = formatDateKey(today);
    let selectedActivity = null;

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    function formatDateKey(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function parseDateKey(dateKey) {
        const parts = dateKey.split('-');

        return new Date(
            Number(parts[0]),
            Number(parts[1]) - 1,
            Number(parts[2])
        );
    }

    function formatSelectedDate(dateKey) {
        const date = parseDateKey(dateKey);

        return `${dayNames[date.getDay()]}, ${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
    }

    function getEventsForDate(dateKey) {
        return events.filter(function (event) {
            if (!event.start) {
                return false;
            }

            return event.start.substring(0, 10) === dateKey;
        });
    }

    function escapeHtml(value) {
        const div = document.createElement('div');

        div.textContent = value ?? '';

        return div.innerHTML;
    }

    function escapeHtmlMultiline(value) {
        return escapeHtml(value).replace(/\n/g, '<br>');
    }

    function capitalize(value) {
        if (!value) {
            return '';
        }

        return value.charAt(0).toUpperCase() + value.slice(1);
    }

    /*
    |--------------------------------------------------------------------------
    | Modal
    |--------------------------------------------------------------------------
    */

    function openModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.remove('hidden');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');

        refreshSelectAllButtons();
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');

        const anyModalOpen = [createModal, detailModal, editModal, exportModal].some(
            function (item) {
                return item && !item.classList.contains('hidden');
            }
        );

        if (!anyModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function closeAllModals() {
        [createModal, detailModal, editModal, exportModal].forEach(function (modal) {
            if (!modal) {
                return;
            }

            modal.classList.add('hidden');
            modal.setAttribute('aria-hidden', 'true');
        });

        document.body.classList.remove('overflow-hidden');
    }

    function getModalByName(name) {
        if (name === 'create') {
            return createModal;
        }

        if (name === 'detail') {
            return detailModal;
        }

        if (name === 'edit') {
            return editModal;
        }

        if (name === 'export') {
            return exportModal;
        }

        return null;
    }

    /*
    |--------------------------------------------------------------------------
    | Create Activity
    |--------------------------------------------------------------------------
    */

    function openCreateModal() {
        if (!createModal) {
            return;
        }

        closeAllModals();
        openModal(createModal);

        const title = document.getElementById('create-title');

        if (title) {
            title.focus();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Detail Activity
    |--------------------------------------------------------------------------
    */

    function openDetailModal(activity) {
        if (!detailModal || !activity) {
            return;
        }

        selectedActivity = activity;

        closeAllModals();

        const title = document.getElementById('detail-activity-title');
        const date = document.getElementById('detail-date');
        const time = document.getElementById('detail-time');
        const location = document.getElementById('detail-location');
        const pics = document.getElementById('detail-pics');
        const description = document.getElementById('detail-description');
        const creator = document.getElementById('detail-creator');

        if (title) {
            title.textContent = activity.title || '-';
        }

        if (date) {
            date.textContent = activity.date_label || '-';
        }

        if (time) {
            time.textContent = activity.time_label
                ? `${activity.time_label} WIB`
                : '-';
        }

        if (location) {
            location.textContent = activity.location || '-';
        }

        if (description) {
            description.innerHTML = activity.description
                ? escapeHtmlMultiline(activity.description)
                : '-';
        }

        if (creator) {
            creator.textContent = activity.creator || '-';
        }

        if (pics) {
            pics.innerHTML = '';

            if (!activity.pics || activity.pics.length === 0) {
                const emptyPic = document.createElement('p');

                emptyPic.className = 'text-sm text-[#68746D]';
                emptyPic.textContent = '-';

                pics.appendChild(emptyPic);
            } else {
                activity.pics.forEach(function (pic) {
                    const chip = document.createElement('span');

                    chip.textContent = `${pic.name} · ${capitalize(pic.role)}`;

                    chip.style.display = 'inline-flex';
                    chip.style.alignItems = 'center';
                    chip.style.padding = '6px 14px';
                    chip.style.borderRadius = '9999px';
                    chip.style.backgroundColor = '#EEF3EF';
                    chip.style.color = '#24332A';
                    chip.style.fontSize = '13px';
                    chip.style.fontWeight = '500';

                    pics.appendChild(chip);
                });
            }
        }

        if (deleteActivityForm) {
            deleteActivityForm.action = `/kalender-kegiatan/${activity.id}`;
            deleteActivityForm.dataset.confirmMessage = deleteMessage(activity);
        }

        openModal(detailModal);
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Activity
    |--------------------------------------------------------------------------
    */

    function openEditModal(activity) {
        if (!editModal || !activity) {
            return;
        }

        selectedActivity = activity;

        closeAllModals();

        if (editActivityForm) {
            editActivityForm.action = `/kalender-kegiatan/${activity.id}`;
        }

        const title = document.getElementById('edit-title');
        const date = document.getElementById('edit-activity-date');
        const startTime = document.getElementById('edit-start-time');
        const endTime = document.getElementById('edit-end-time');
        const location = document.getElementById('edit-location');
        const description = document.getElementById('edit-description');

        if (title) {
            title.value = activity.title || '';
        }

        if (date) {
            date.value = activity.date || '';
        }

        if (startTime) {
            startTime.value = activity.start_time || '';
        }

        if (endTime) {
            endTime.value = activity.end_time || '';
        }

        if (location) {
            location.value = activity.location || '';
        }

        if (description) {
            description.value = activity.description || '';
        }

        const selectedPicIds = (activity.pics || []).map(function (pic) {
            return String(pic.id);
        });

        document
            .querySelectorAll('[data-edit-pic]')
            .forEach(function (checkbox) {
                checkbox.checked = selectedPicIds.includes(
                    String(checkbox.value)
                );
            });

        if (editNoEndTime) {
            editNoEndTime.checked = !activity.end_time;
        }

        updateEditEndTimeState();

        openModal(editModal);

        if (title) {
            title.focus();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Delete Activity
    |--------------------------------------------------------------------------
    */

    function deleteMessage(activity) {
        return `Kegiatan “${activity.title}” akan dihapus dan tidak dapat dikembalikan.`;
    }

    function deleteActivity(activity) {
        if (!deleteActivityForm || !activity) {
            return;
        }

        window.openConfirm({
            title: 'Hapus kegiatan?',
            message: deleteMessage(activity),
            confirmLabel: 'Hapus',
            tone: 'danger'
        }).then(function (ok) {
            if (!ok) {
                return;
            }

            deleteActivityForm.action = `/kalender-kegiatan/${activity.id}`;

            // submit() tidak memicu event submit, jadi tidak ada konfirmasi ganda.
            deleteActivityForm.submit();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | End Time
    |--------------------------------------------------------------------------
    */

    function setEndTimeState(input, checkbox) {
        if (!input || !checkbox) {
            return;
        }

        if (checkbox.checked) {
            input.value = '';
            input.disabled = true;
            input.classList.add('bg-[#F1F5F9]', 'text-[#94A3B8]');
        } else {
            input.disabled = false;
            input.classList.remove('bg-[#F1F5F9]', 'text-[#94A3B8]');
        }
    }

    function updateCreateEndTimeState() {
        setEndTimeState(createEndTime, createNoEndTime);
    }

    function updateEditEndTimeState() {
        setEndTimeState(editEndTime, editNoEndTime);
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    function renderCalendar() {
        // Judul bulan format Inggris, mis. "October 2026"
        monthTitle.textContent = new Date(currentYear, currentMonth, 1)
            .toLocaleDateString('en-US', { month: 'long', year: 'numeric' });

        calendarGrid.innerHTML = '';
        calendarGrid.style.gridAutoRows = '52px';
        calendarGrid.style.alignItems = 'start';
        calendarGrid.style.alignContent = 'start';

        const firstDay = new Date(currentYear, currentMonth, 1);
        const lastDay = new Date(currentYear, currentMonth + 1, 0);

        const firstDayIndex = firstDay.getDay();
        const totalDays = lastDay.getDate();

        const previousMonthLastDay = new Date(
            currentYear,
            currentMonth,
            0
        ).getDate();

        // Tanggal bulan sebelumnya
        for (let i = firstDayIndex - 1; i >= 0; i--) {
            const day = previousMonthLastDay - i;

            createCalendarDay(
                new Date(currentYear, currentMonth - 1, day),
                true
            );
        }

        // Tanggal bulan aktif
        for (let day = 1; day <= totalDays; day++) {
            createCalendarDay(
                new Date(currentYear, currentMonth, day),
                false
            );
        }

        // Tanggal bulan berikutnya
        const totalCells = calendarGrid.children.length;

        const remainingCells =
            totalCells <= 35 ? 35 - totalCells : 42 - totalCells;

        for (let day = 1; day <= remainingCells; day++) {
            createCalendarDay(
                new Date(currentYear, currentMonth + 1, day),
                true
            );
        }
    }

    function createCalendarDay(date, isOtherMonth) {
        const dateKey = formatDateKey(date);
        const eventsForDate = getEventsForDate(dateKey);

        const isSelected = dateKey === selectedDate;
        const isToday = dateKey === formatDateKey(today);

        const hasUnread = eventsForDate.some(function (event) {
            return event.is_unread;
        });

        const button = document.createElement('button');

        button.type = 'button';
        button.className = 'flex w-full items-start justify-center';
        button.style.height = '52px';

        const box = document.createElement('span');

        box.className = 'relative flex items-center justify-center text-sm transition';
        box.style.width = '44px';
        box.style.height = '44px';
        box.style.borderRadius = '12px';
        box.textContent = date.getDate();

        if (isSelected) {
            box.style.backgroundColor = '#234936';
            box.style.color = '#FFFFFF';
            box.style.fontWeight = '500';
        } else if (isToday) {
            box.style.backgroundColor = '#DCEBE1';
            box.style.color = '#24332A';
        } else if (isOtherMonth) {
            box.style.color = '#B8C0BA';
        } else {
            box.style.color = '#24332A';
            box.classList.add('hover:bg-white');
        }

        // Titik: merah = ada yang belum dibaca, hijau = semua sudah dibaca
        if (eventsForDate.length > 0) {
            const dot = document.createElement('span');

            dot.style.position = 'absolute';
            dot.style.bottom = '5px';
            dot.style.left = '50%';
            dot.style.transform = 'translateX(-50%)';
            dot.style.width = '6px';
            dot.style.height = '6px';
            dot.style.borderRadius = '9999px';

            if (hasUnread) {
                dot.style.backgroundColor = '#DC2626';
            } else {
                dot.style.backgroundColor = isSelected ? '#FFFFFF' : '#234936';
            }

            if (isOtherMonth) {
                dot.style.opacity = '0.35';
            }

            box.appendChild(dot);
        }

        button.appendChild(box);

        button.addEventListener('click', function () {
            selectedDate = dateKey;

            if (isOtherMonth) {
                currentMonth = date.getMonth();
                currentYear = date.getFullYear();
            }

            renderCalendar();
            renderActivities();
        });

        calendarGrid.appendChild(button);
    }

    /*
    |--------------------------------------------------------------------------
    | Activity Card (dipakai oleh tampilan kalender dan tampilan daftar)
    |--------------------------------------------------------------------------
    */

    function buildActivityCard(event) {
        const metaItemStyle = 'display:flex;align-items:center;gap:6px;';

        // Warna kartu: abu-abu kehijauan
        const cardBg = '#E8EEEA';
        const cardBorder = '#D2DCD5';
        const cardBgHover = '#D9E4DC';
        const cardBorderHover = '#234936';

        const squareButtonStyle =
            'display:flex;align-items:center;justify-content:center;' +
            'width:36px;height:36px;flex:none;border-radius:8px;' +
            'border:1px solid #CBD6CE;background:#FFFFFF;' +
            'box-shadow:0 1px 2px rgba(0,0,0,0.06);cursor:pointer;';

        const item = document.createElement('div');

        item.className = 'rounded-xl';

        // Seluruh kartu bisa diklik untuk membuka detail
        item.style.cursor = 'pointer';
        item.style.padding = '14px 18px';
        item.style.backgroundColor = cardBg;
        item.style.border = `1px solid ${cardBorder}`;
        item.style.transition =
            'background-color 150ms ease, border-color 150ms ease';

        // Hover: lebih gelap + garis hijau, kembali normal saat kursor keluar
        item.addEventListener('mouseenter', function () {
            item.style.backgroundColor = cardBgHover;
            item.style.borderColor = cardBorderHover;
        });

        item.addEventListener('mouseleave', function () {
            item.style.backgroundColor = cardBg;
            item.style.borderColor = cardBorder;
        });

        item.addEventListener('click', function () {
            openDetailModal(event);
        });

        const locationHtml = event.location
            ? `<span style="${metaItemStyle}">${icons.pin}${escapeHtml(event.location)}</span>`
            : '';

        const badgeHtml = event.is_unread
            ? '<span style="flex:none;border-radius:9999px;background:#DC2626;color:#FFFFFF;padding:1px 10px;font-size:12px;font-weight:600;">Baru</span>'
            : '';

        const actionsHtml = canManage
            ? `
                <button type="button" aria-label="Edit"
                    class="activity-edit-button"
                    style="${squareButtonStyle}color:#24332A;">
                    ${icons.edit}
                </button>
                <button type="button" aria-label="Hapus"
                    class="activity-delete-button"
                    style="${squareButtonStyle}color:#DC2626;">
                    ${icons.trash}
                </button>
            `
            : '';

        item.innerHTML = `
            <div style="display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px 16px;">

                <div style="min-width:0;flex:1 1 220px;">

                    <div style="display:flex;align-items:center;gap:8px;">
                        <h3 style="min-width:0;font-size:16px;font-weight:600;color:#24332A;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                            ${escapeHtml(event.title)}
                        </h3>
                        ${badgeHtml}
                    </div>

                    <div style="display:flex;flex-wrap:wrap;align-items:center;gap:4px 16px;margin-top:4px;font-size:13px;color:#5C6B62;">
                        <span style="${metaItemStyle}">
                            ${icons.clock}${escapeHtml(event.time_label || '-')}
                        </span>
                        ${locationHtml}
                    </div>

                </div>

                <div style="display:flex;align-items:center;gap:8px;flex:none;">
                    ${actionsHtml}
                    <span style="margin-left:6px;font-size:13px;font-weight:600;color:#24332A;white-space:nowrap;">
                        Lihat detail ›
                    </span>
                </div>

            </div>
        `;

        if (canManage) {
            item
                .querySelector('.activity-edit-button')
                .addEventListener('click', function (e) {
                    e.stopPropagation(); // jangan ikut membuka detail
                    openEditModal(event);
                });

            item
                .querySelector('.activity-delete-button')
                .addEventListener('click', function (e) {
                    e.stopPropagation(); // jangan ikut membuka detail
                    deleteActivity(event);
                });
        }

        return item;
    }

    /*
    |--------------------------------------------------------------------------
    | Activity List (tampilan kalender: kegiatan pada tanggal terpilih)
    |--------------------------------------------------------------------------
    */

    function renderActivities() {
        selectedDateTitle.textContent = formatSelectedDate(selectedDate);

        const selectedActivities = getEventsForDate(selectedDate);

        activityList.innerHTML = '';

        if (selectedActivities.length === 0) {
            activityList.innerHTML = `
                <p class="text-sm text-[#68746D]">
                    Tidak ada kegiatan pada tanggal ini.
                </p>
            `;

            return;
        }

        selectedActivities.forEach(function (event) {
            activityList.appendChild(buildActivityCard(event));
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Daftar Kegiatan Bulanan (tampilan daftar, tanpa kalender)
    |--------------------------------------------------------------------------
    */

    function getEventsForMonth(year, month) {
        const prefix = `${year}-${String(month + 1).padStart(2, '0')}`;

        return events
            .filter(function (event) {
                return event.start && event.start.substring(0, 7) === prefix;
            })
            .sort(function (a, b) {
                // Format ISO (YYYY-MM-DDTHH:MM:SS) bisa diurutkan sebagai teks
                return a.start.localeCompare(b.start);
            });
    }

    function renderMonthList() {
        if (!monthList) {
            return;
        }

        const monthEvents = getEventsForMonth(currentYear, currentMonth);

        if (listMonthTitle) {
            listMonthTitle.textContent =
                `${monthNames[currentMonth]} ${currentYear}`;
        }

        if (listMonthCount) {
            listMonthCount.textContent = monthEvents.length > 0
                ? `${monthEvents.length} kegiatan bulan ini`
                : 'Belum ada kegiatan bulan ini';
        }

        monthList.innerHTML = '';

        if (monthEvents.length === 0) {
            monthList.innerHTML = `
                <p class="text-sm text-[#68746D]">
                    Tidak ada kegiatan pada bulan ini.
                </p>
            `;

            return;
        }

        let lastDateKey = null;

        monthEvents.forEach(function (event) {
            const dateKey = event.start.substring(0, 10);

            // Judul tanggal setiap kali tanggalnya berganti
            if (dateKey !== lastDateKey) {
                const heading = document.createElement('h3');

                heading.textContent = formatSelectedDate(dateKey);
                heading.style.fontSize = '14px';
                heading.style.fontWeight = '600';
                heading.style.color = '#68746D';
                heading.style.marginTop = lastDateKey === null ? '0' : '24px';
                heading.style.marginBottom = '12px';

                monthList.appendChild(heading);

                lastDateKey = dateKey;
            }

            const card = buildActivityCard(event);

            card.style.marginBottom = '12px';

            monthList.appendChild(card);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Riwayat (kegiatan yang sudah melewati hari ini, zona waktu WIB)
    |--------------------------------------------------------------------------
    */

    function getTodayKeyJakarta() {
        // en-CA menghasilkan format YYYY-MM-DD
        return new Intl.DateTimeFormat('en-CA', {
            timeZone: 'Asia/Jakarta',
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        }).format(new Date());
    }

    function getPastEvents() {
        const todayKey = getTodayKeyJakarta();

        return events
            .filter(function (event) {
                return (
                    event.start &&
                    event.start.substring(0, 10) < todayKey
                );
            })
            .sort(function (a, b) {
                // Terbaru di atas
                return b.start.localeCompare(a.start);
            });
    }

    function populateHistoryMonths(pastEvents) {
        if (!historyMonth) {
            return;
        }

        const previous = historyMonth.value;
        const months = [];

        pastEvents.forEach(function (event) {
            const key = event.start.substring(0, 7);

            if (!months.includes(key)) {
                months.push(key);
            }
        });

        historyMonth.innerHTML = '';

        const all = document.createElement('option');

        all.value = '';
        all.textContent = 'Semua bulan';
        historyMonth.appendChild(all);

        months.forEach(function (key) {
            const parts = key.split('-');
            const option = document.createElement('option');

            option.value = key;
            option.textContent =
                `${monthNames[Number(parts[1]) - 1]} ${parts[0]}`;

            historyMonth.appendChild(option);
        });

        historyMonth.value = months.includes(previous) ? previous : '';
    }

    function buildHistoryCard(event) {
        const cardBg = '#E8EEEA';
        const cardBorder = '#D2DCD5';
        const cardBgHover = '#D9E4DC';
        const cardBorderHover = '#234936';

        const dateKey = event.start.substring(0, 10);

        const item = document.createElement('div');

        item.className = 'rounded-xl';
        item.style.cursor = 'pointer';
        item.style.display = 'flex';
        item.style.flexDirection = 'column';
        item.style.padding = '20px';
        item.style.backgroundColor = cardBg;
        item.style.border = `1px solid ${cardBorder}`;
        item.style.transition =
            'background-color 150ms ease, border-color 150ms ease';

        item.addEventListener('mouseenter', function () {
            item.style.backgroundColor = cardBgHover;
            item.style.borderColor = cardBorderHover;
        });

        item.addEventListener('mouseleave', function () {
            item.style.backgroundColor = cardBg;
            item.style.borderColor = cardBorder;
        });

        item.addEventListener('click', function () {
            openDetailModal(event);
        });

        const locationHtml = event.location
            ? `
                <p style="display:flex;align-items:flex-start;gap:8px;margin-top:10px;font-size:14px;line-height:1.4;color:#5C6B62;">
                    <span style="margin-top:1px;">${icons.pin}</span>
                    <span style="min-width:0;">${escapeHtml(event.location)}</span>
                </p>
            `
            : '';

        item.innerHTML = `
            <p style="font-size:13px;font-weight:600;letter-spacing:0.02em;color:#234936;">
                ${escapeHtml(formatSelectedDate(dateKey).toUpperCase())}
            </p>

            <h3 style="margin-top:14px;font-size:18px;font-weight:600;line-height:1.35;color:#24332A;">
                ${escapeHtml(event.title)}
            </h3>

            ${locationHtml}

            <div style="margin-top:auto;padding-top:16px;text-align:right;font-size:14px;font-weight:600;color:#24332A;">
                Lihat detail ›
            </div>
        `;

        return item;
    }

    function renderHistory() {
        if (!historyList) {
            return;
        }

        const pastEvents = getPastEvents();

        populateHistoryMonths(pastEvents);

        const month = historyMonth ? historyMonth.value : '';
        const start = historyStart ? historyStart.value : '';
        const end = historyEnd ? historyEnd.value : '';

        const filtered = pastEvents.filter(function (event) {
            const dateKey = event.start.substring(0, 10);

            if (month && event.start.substring(0, 7) !== month) {
                return false;
            }

            if (start && dateKey < start) {
                return false;
            }

            if (end && dateKey > end) {
                return false;
            }

            return true;
        });

        historyList.innerHTML = '';

        if (filtered.length === 0) {
            const message = pastEvents.length === 0
                ? 'Belum ada riwayat kegiatan.'
                : 'Tidak ada kegiatan pada filter ini.';

            historyList.innerHTML = `
                <p class="text-sm text-[#68746D]">${message}</p>
            `;

            return;
        }

        const grid = document.createElement('div');

        grid.style.display = 'grid';
        grid.style.gridTemplateColumns =
            'repeat(auto-fill, minmax(340px, 1fr))';
        grid.style.gap = '16px';

        filtered.forEach(function (event) {
            grid.appendChild(buildHistoryCard(event));
        });

        historyList.appendChild(grid);
    }

    // Pilih bulan dan rentang tanggal saling menggantikan
    if (historyMonth) {
        historyMonth.addEventListener('change', function () {
            if (historyStart) {
                historyStart.value = '';
            }

            if (historyEnd) {
                historyEnd.value = '';
            }

            renderHistory();
        });
    }

    [historyStart, historyEnd].forEach(function (input) {
        if (!input) {
            return;
        }

        input.addEventListener('change', function () {
            if (historyMonth) {
                historyMonth.value = '';
            }

            renderHistory();
        });
    });

    if (historyReset) {
        historyReset.addEventListener('click', function () {
            if (historyMonth) {
                historyMonth.value = '';
            }

            if (historyStart) {
                historyStart.value = '';
            }

            if (historyEnd) {
                historyEnd.value = '';
            }

            renderHistory();
        });
    }

    // Ekspor PDF: isi popup export sesuai filter yang sedang dipakai
    if (historyExport) {
        historyExport.addEventListener('click', function () {
            const start = historyStart ? historyStart.value : '';
            const end = historyEnd ? historyEnd.value : '';
            const month = historyMonth ? historyMonth.value : '';

            const setType = function (value) {
                const radio = exportForm
                    ? exportForm.querySelector(
                        `input[name="filter_type"][value="${value}"]`
                    )
                    : null;

                if (radio) {
                    radio.checked = true;
                }
            };

            if (start && end) {
                const startInput = document.getElementById('export-start-date');
                const endInput = document.getElementById('export-end-date');

                setType('range');

                if (startInput) {
                    startInput.value = start;
                }

                if (endInput) {
                    endInput.value = end;
                }
            } else if (month) {
                const parts = month.split('-');

                setType('month');

                if (exportYearSelect) {
                    exportYearSelect.value = parts[0];
                }

                if (exportMonthSelect) {
                    exportMonthSelect.value = parts[1];
                }

                syncExportMonth();
            }

            openExportModal();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Ganti Tampilan (Kalender / Daftar)
    |--------------------------------------------------------------------------
    */

    let currentView = 'calendar';

    function setView(view) {
        currentView = view;

        calendarEl.style.display = view === 'calendar' ? '' : 'none';

        if (listView) {
            listView.style.display = view === 'list' ? 'block' : 'none';
        }

        if (historyView) {
            historyView.style.display = view === 'history' ? 'block' : 'none';
        }

        viewTabs.forEach(function (tab) {
            const active = tab.dataset.viewTab === view;

            tab.style.backgroundColor = active ? '#FFFFFF' : 'transparent';
            tab.style.boxShadow = active ? '0 1px 2px rgba(0,0,0,0.08)' : 'none';
            tab.style.fontWeight = active ? '600' : '400';
            tab.style.color = active ? '#24332A' : '#68746D';
        });

        if (view === 'list') {
            renderMonthList();
        }

        if (view === 'history') {
            renderHistory();
        }
    }

    viewTabs.forEach(function (tab) {
        tab.addEventListener('click', function () {
            setView(tab.dataset.viewTab);
        });
    });

    if (listTodayButton) {
        listTodayButton.addEventListener('click', function () {
            currentMonth = today.getMonth();
            currentYear = today.getFullYear();
            selectedDate = formatDateKey(today);

            renderCalendar();
            renderActivities();
            renderMonthList();
        });
    }

    if (listPreviousButton) {
        listPreviousButton.addEventListener('click', function () {
            goToMonth(-1);
        });
    }

    if (listNextButton) {
        listNextButton.addEventListener('click', function () {
            goToMonth(1);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Export PDF (popup)
    |--------------------------------------------------------------------------
    */

    function updateExportSections() {
        if (!exportForm) {
            return;
        }

        const checked = exportForm.querySelector(
            'input[name="filter_type"]:checked'
        );

        const type = checked ? checked.value : 'month';

        if (exportMonthSection) {
            exportMonthSection.classList.toggle('hidden', type !== 'month');
        }

        if (exportRangeSection) {
            exportRangeSection.classList.toggle('hidden', type !== 'range');
        }

        // Tandai kartu pilihan yang aktif
        exportForm
            .querySelectorAll('[data-export-option]')
            .forEach(function (label) {
                const input = label.querySelector('input[type="radio"]');
                const active = input && input.checked;

                label.style.borderColor = active ? '#234936' : '';
                label.style.backgroundColor = active ? '#F1F6F2' : '';
            });
    }

    function showExportError(message) {
        if (!exportError) {
            return;
        }

        exportError.textContent = message;
        exportError.classList.remove('hidden');
    }

    function clearExportError() {
        if (!exportError) {
            return;
        }

        exportError.textContent = '';
        exportError.classList.add('hidden');
    }

    function openExportModal() {
        if (!exportModal) {
            return;
        }

        closeAllModals();
        clearExportError();
        updateExportSections();
        openModal(exportModal);
    }

    // Pilih bulan: dua dropdown (bulan + tahun) disatukan ke input hidden "YYYY-MM"
    const exportMonthSelect = document.getElementById('export-month-select');
    const exportYearSelect = document.getElementById('export-year-select');
    const exportMonthInput = document.getElementById('export-month');

    function syncExportMonth() {
        if (!exportMonthSelect || !exportYearSelect || !exportMonthInput) {
            return;
        }

        exportMonthInput.value =
            `${exportYearSelect.value}-${exportMonthSelect.value}`;
    }

    if (exportMonthSelect) {
        exportMonthSelect.addEventListener('change', syncExportMonth);
    }

    if (exportYearSelect) {
        exportYearSelect.addEventListener('change', syncExportMonth);
    }

    if (openExportButton) {
        openExportButton.addEventListener('click', openExportModal);
    }

    if (exportForm) {
        exportForm
            .querySelectorAll('input[name="filter_type"]')
            .forEach(function (radio) {
                radio.addEventListener('change', function () {
                    clearExportError();
                    updateExportSections();
                });
            });

        exportForm.addEventListener('submit', function (event) {
            clearExportError();

            const checked = exportForm.querySelector(
                'input[name="filter_type"]:checked'
            );

            const type = checked ? checked.value : 'month';

            if (type === 'month') {
                const month = document.getElementById('export-month');

                if (!month || !month.value) {
                    event.preventDefault();
                    showExportError('Bulan wajib dipilih.');
                    return;
                }
            } else {
                const start = document.getElementById('export-start-date');
                const end = document.getElementById('export-end-date');

                if (!start || !end || !start.value || !end.value) {
                    event.preventDefault();
                    showExportError('Tanggal mulai dan tanggal selesai wajib diisi.');
                    return;
                }

                if (end.value < start.value) {
                    event.preventDefault();
                    showExportError('Tanggal selesai harus sama atau setelah tanggal mulai.');
                    return;
                }
            }

            // File PDF diunduh tanpa pindah halaman, jadi tutup popup setelahnya.
            setTimeout(function () {
                closeModal(exportModal);
            }, 400);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Pilih Semua PIC
    |--------------------------------------------------------------------------
    */

    const picSelectors = {
        create: '[data-create-pic]',
        edit: '[data-edit-pic]'
    };

    function refreshSelectAllButtons() {
        document
            .querySelectorAll('[data-select-all]')
            .forEach(function (button) {
                const selector = picSelectors[button.dataset.selectAll];

                if (!selector) {
                    return;
                }

                const boxes = Array.from(document.querySelectorAll(selector));
                const allChecked =
                    boxes.length > 0 &&
                    boxes.every(function (box) {
                        return box.checked;
                    });

                button.textContent = allChecked
                    ? 'Batal pilih semua'
                    : 'Pilih semua';
            });
    }

    document
        .querySelectorAll('[data-select-all]')
        .forEach(function (button) {
            const selector = picSelectors[button.dataset.selectAll];

            if (!selector) {
                return;
            }

            button.addEventListener('click', function () {
                const boxes = Array.from(document.querySelectorAll(selector));
                const allChecked =
                    boxes.length > 0 &&
                    boxes.every(function (box) {
                        return box.checked;
                    });

                boxes.forEach(function (box) {
                    box.checked = !allChecked;
                });

                refreshSelectAllButtons();
            });
        });

    // Centang manual juga memperbarui label tombol
    Object.keys(picSelectors).forEach(function (key) {
        document
            .querySelectorAll(picSelectors[key])
            .forEach(function (box) {
                box.addEventListener('change', refreshSelectAllButtons);
            });
    });

    /*
    |--------------------------------------------------------------------------
    | Month Navigation
    |--------------------------------------------------------------------------
    */

    function goToMonth(offset) {
        currentMonth += offset;

        if (currentMonth < 0) {
            currentMonth = 11;
            currentYear--;
        }

        if (currentMonth > 11) {
            currentMonth = 0;
            currentYear++;
        }

        selectedDate = formatDateKey(
            new Date(currentYear, currentMonth, 1)
        );

        renderCalendar();
        renderActivities();
        renderMonthList();
    }

    if (previousMonthButton) {
        previousMonthButton.addEventListener('click', function () {
            goToMonth(-1);
        });
    }

    if (nextMonthButton) {
        nextMonthButton.addEventListener('click', function () {
            goToMonth(1);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Open Create / Edit
    |--------------------------------------------------------------------------
    */

    if (openCreateButton) {
        openCreateButton.addEventListener('click', function () {
            openCreateModal();
        });
    }

    if (openEditFromDetailButton) {
        openEditFromDetailButton.addEventListener('click', function () {
            if (!selectedActivity) {
                return;
            }

            openEditModal(selectedActivity);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal (button, backdrop, Escape)
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('[data-close-modal]')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(getModalByName(button.dataset.closeModal));
            });
        });

    document
        .querySelectorAll('[data-modal-backdrop]')
        .forEach(function (backdrop) {
            backdrop.addEventListener('click', function (event) {
                if (event.target !== backdrop) {
                    return;
                }

                closeModal(getModalByName(backdrop.dataset.modalBackdrop));
            });
        });

    document.addEventListener('keydown', function (event) {
        if (event.key !== 'Escape') {
            return;
        }

        closeAllModals();
    });

    /*
    |--------------------------------------------------------------------------
    | End Time Checkbox
    |--------------------------------------------------------------------------
    */

    if (createNoEndTime) {
        createNoEndTime.addEventListener('change', updateCreateEndTimeState);
    }

    if (editNoEndTime) {
        editNoEndTime.addEventListener('change', updateEditEndTimeState);
    }

    /*
    |--------------------------------------------------------------------------
    | Validation State (setelah submit gagal)
    |--------------------------------------------------------------------------
    */

    const modalState = window.activityModalState || {};

    if (modalState.openModal === 'export') {
        openExportModal();
    }

    if (modalState.hasOldInput && modalState.openModal === 'create') {
        openCreateModal();
        updateCreateEndTimeState();
    }

    if (
        modalState.hasOldInput &&
        modalState.openModal === 'edit' &&
        modalState.editId
    ) {
        const activity = events.find(function (event) {
            return String(event.id) === String(modalState.editId);
        });

        if (activity) {
            openEditModal(activity);

            const old = modalState.old || {};
            const hasValue = function (value) {
                return value !== null && value !== undefined;
            };

            const fields = {
                'edit-title': old.title,
                'edit-activity-date': old.activity_date,
                'edit-start-time': old.start_time,
                'edit-location': old.location,
                'edit-description': old.description
            };

            Object.keys(fields).forEach(function (id) {
                const input = document.getElementById(id);

                if (input && hasValue(fields[id])) {
                    input.value = fields[id];
                }
            });

            if (editEndTime && hasValue(old.end_time)) {
                editEndTime.value = old.end_time;

                if (editNoEndTime) {
                    editNoEndTime.checked = false;
                }
            } else if (editNoEndTime) {
                editNoEndTime.checked = true;
            }

            const oldPicIds = (old.pic_ids || []).map(function (id) {
                return String(id);
            });

            document
                .querySelectorAll('[data-edit-pic]')
                .forEach(function (checkbox) {
                    checkbox.checked = oldPicIds.includes(
                        String(checkbox.value)
                    );
                });

            updateEditEndTimeState();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Initial State
    |--------------------------------------------------------------------------
    */

    updateCreateEndTimeState();
    updateEditEndTimeState();
    refreshSelectAllButtons();

    renderCalendar();
    renderActivities();
    setView('calendar');
});