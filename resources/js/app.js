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
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');
        modal.setAttribute('aria-hidden', 'true');

        const anyModalOpen = [createModal, detailModal, editModal].some(
            function (item) {
                return item && !item.classList.contains('hidden');
            }
        );

        if (!anyModalOpen) {
            document.body.classList.remove('overflow-hidden');
        }
    }

    function closeAllModals() {
        [createModal, detailModal, editModal].forEach(function (modal) {
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

    function deleteActivity(activity) {
        if (!deleteActivityForm || !activity) {
            return;
        }

        if (!confirm('Apakah Anda yakin ingin menghapus kegiatan ini?')) {
            return;
        }

        deleteActivityForm.action = `/kalender-kegiatan/${activity.id}`;

        // submit() tidak memicu onsubmit, jadi tidak ada konfirmasi ganda.
        deleteActivityForm.submit();
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
    | Activity List
    |--------------------------------------------------------------------------
    */

    function renderActivities() {
        selectedDateTitle.textContent = formatSelectedDate(selectedDate);

        const selectedActivities = getEventsForDate(selectedDate);

        activityList.innerHTML = '';

        if (selectedActivities.length === 0) {
            activityList.innerHTML = `
                <p class="text-sm text-[#64748B]">
                    Tidak ada kegiatan pada tanggal ini.
                </p>
            `;

            return;
        }

        selectedActivities.forEach(function (event) {
            const item = document.createElement('div');

            item.className =
                'rounded-xl border border-[#E5E9F0] bg-[#F8FAFC] p-5';

            // Hover: berubah hijau, kembali normal saat kursor keluar
            item.style.transition = 'background-color 150ms ease, border-color 150ms ease';

            item.addEventListener('mouseenter', function () {
                item.style.backgroundColor = '#E3EEE6';
                item.style.borderColor = '#234936';
            });

            item.addEventListener('mouseleave', function () {
                item.style.backgroundColor = '';
                item.style.borderColor = '';
            });

            const metaItemStyle = 'display:flex;align-items:center;gap:6px;';

            const locationHtml = event.location
                ? `<span style="${metaItemStyle}">${icons.pin}${escapeHtml(event.location)}</span>`
                : '';

            const squareButtonStyle =
                'display:flex;align-items:center;justify-content:center;' +
                'width:40px;height:40px;flex:none;border-radius:8px;' +
                'border:1px solid #E5E9F0;background:#FFFFFF;' +
                'box-shadow:0 1px 2px rgba(0,0,0,0.06);cursor:pointer;';

            const badgeHtml = event.is_unread
                ? '<span class="shrink-0 rounded-full bg-[#DC2626] px-3 py-0.5 text-xs font-semibold text-white">Baru</span>'
                : '';

            const actionsHtml = canManage
                ? `
                 <div style="display:flex;gap:8px;margin-top:12px;">
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
            </div>
    `
                : '';

            item.innerHTML = `
                <div class="flex items-start justify-between gap-3">
                    <h3 class="min-w-0 truncate text-lg font-semibold text-[#111827]">
                        ${escapeHtml(event.title)}
                    </h3>
                    ${badgeHtml}
                </div>

                <div style="display:flex;flex-wrap:wrap;align-items:center;gap:6px 16px;margin-top:8px;font-size:14px;color:#68746D;">
                    <span style="${metaItemStyle}">
                        ${icons.clock}${escapeHtml(event.time_label || '-')}
                    </span>
                    ${locationHtml}
                </div>

                <div class="mt-3 flex justify-end">
                    <button type="button"
                        class="activity-detail-button flex items-center gap-1 text-sm font-semibold text-[#111827] transition hover:text-[#1B2F5B]">
                        Lihat detail <span>›</span>
                    </button>
                </div>

                ${actionsHtml}
            `;

            item
                .querySelector('.activity-detail-button')
                .addEventListener('click', function () {
                    openDetailModal(event);
                });

            if (canManage) {
                item
                    .querySelector('.activity-edit-button')
                    .addEventListener('click', function () {
                        openEditModal(event);
                    });

                item
                    .querySelector('.activity-delete-button')
                    .addEventListener('click', function () {
                        deleteActivity(event);
                    });
            }

            activityList.appendChild(item);
        });
    }

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

    renderCalendar();
    renderActivities();
});