document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('activity-calendar');

    if (!calendarEl) {
        return;
    }

    const eventsData =
        document.getElementById('calendar-events-data');

    const events = eventsData
        ? JSON.parse(eventsData.textContent || '[]')
        : [];

    const calendarGrid =
        document.getElementById('calendar-grid');

    const monthTitle =
        document.getElementById('calendar-month-title');

    const selectedDateTitle =
        document.getElementById('selected-date-title');

    const activityList =
        document.getElementById('activity-list');

    const previousMonthButton =
        document.getElementById('previous-month');

    const nextMonthButton =
        document.getElementById('next-month');

    const createModal =
        document.getElementById('create-activity-modal');

    const detailModal =
        document.getElementById('detail-activity-modal');

    const editModal =
        document.getElementById('edit-activity-modal');

    const openCreateButton =
        document.getElementById('open-create-activity');

    const openEditFromDetailButton =
        document.getElementById('open-edit-from-detail');

    const deleteActivityForm =
        document.getElementById('delete-activity-form');

    const editActivityForm =
        document.getElementById('edit-activity-form');

    const createEndTime =
        document.getElementById('create-end-time');

    const createNoEndTime =
        document.getElementById('create-no-end-time');

    const editEndTime =
        document.getElementById('edit-end-time');

    const editNoEndTime =
        document.getElementById('edit-no-end-time');

    const monthNames = [
        'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    ];

    const dayNames = [
        'Minggu',
        'Senin',
        'Selasa',
        'Rabu',
        'Kamis',
        'Jumat',
        'Sabtu'
    ];

    const today = new Date();

    let currentMonth = today.getMonth();
    let currentYear = today.getFullYear();

    let selectedDate =
        formatDateKey(today);

    let selectedActivity = null;

    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    function formatDateKey(date) {
        const year =
            date.getFullYear();

        const month =
            String(date.getMonth() + 1)
                .padStart(2, '0');

        const day =
            String(date.getDate())
                .padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function parseDateKey(dateKey) {
        const parts =
            dateKey.split('-');

        return new Date(
            Number(parts[0]),
            Number(parts[1]) - 1,
            Number(parts[2])
        );
    }

    function formatSelectedDate(dateKey) {
        const date =
            parseDateKey(dateKey);

        return `${dayNames[date.getDay()]}, ${date.getDate()} ${monthNames[date.getMonth()]} ${date.getFullYear()}`;
    }

    function getEventsForDate(dateKey) {
        return events.filter(function (event) {
            if (!event.start) {
                return false;
            }

            return (
                event.start.substring(0, 10) ===
                dateKey
            );
        });
    }

    function escapeHtml(value) {
        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;
    }

    function escapeHtmlMultiline(value) {
        return escapeHtml(value)
            .replace(/\n/g, '<br>');
    }

    function capitalize(value) {
        if (!value) {
            return '';
        }

        return (
            value.charAt(0).toUpperCase() +
            value.slice(1)
        );
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

        modal.setAttribute(
            'aria-hidden',
            'false'
        );

        document.body.classList.add(
            'overflow-hidden'
        );
    }

    function closeModal(modal) {
        if (!modal) {
            return;
        }

        modal.classList.add('hidden');

        modal.setAttribute(
            'aria-hidden',
            'true'
        );

        const anyModalOpen = [
            createModal,
            detailModal,
            editModal
        ].some(function (item) {
            return (
                item &&
                !item.classList.contains('hidden')
            );
        });

        if (!anyModalOpen) {
            document.body.classList.remove(
                'overflow-hidden'
            );
        }
    }

    function closeAllModals() {
        [
            createModal,
            detailModal,
            editModal
        ].forEach(function (modal) {
            if (!modal) {
                return;
            }

            modal.classList.add('hidden');

            modal.setAttribute(
                'aria-hidden',
                'true'
            );
        });

        document.body.classList.remove(
            'overflow-hidden'
        );
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

        const title =
            document.getElementById(
                'create-title'
            );

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

        selectedActivity =
            activity;

        closeAllModals();

        const title =
            document.getElementById(
                'detail-activity-title'
            );

        const date =
            document.getElementById(
                'detail-date'
            );

        const time =
            document.getElementById(
                'detail-time'
            );

        const location =
            document.getElementById(
                'detail-location'
            );

        const pics =
            document.getElementById(
                'detail-pics'
            );

        const description =
            document.getElementById(
                'detail-description'
            );

        const creator =
            document.getElementById(
                'detail-creator'
            );

        if (title) {
            title.textContent =
                activity.title || '-';
        }

        if (date) {
            date.textContent =
                activity.date_label || '-';
        }

        if (time) {
            time.textContent =
                activity.time_label || '-';
        }

        if (location) {
            location.textContent =
                activity.location || '-';
        }

        if (description) {
            description.innerHTML =
                activity.description
                    ? escapeHtmlMultiline(
                        activity.description
                    )
                    : '-';
        }

        if (creator) {
            creator.textContent =
                activity.creator || '-';
        }

        if (pics) {
            pics.innerHTML = '';

            if (
                !activity.pics ||
                activity.pics.length === 0
            ) {
                const emptyPic =
                    document.createElement('p');

                emptyPic.className =
                    'text-sm text-[#24332A]';

                emptyPic.textContent =
                    '-';

                pics.appendChild(
                    emptyPic
                );
            } else {
                activity.pics.forEach(
                    function (pic) {
                        const item =
                            document.createElement('p');

                        item.className =
                            'text-sm text-[#24332A]';

                        item.textContent =
                            `${pic.name} (${capitalize(pic.role)})`;

                        pics.appendChild(
                            item
                        );
                    }
                );
            }
        }

        if (deleteActivityForm) {
            deleteActivityForm.action =
                `/kalender-kegiatan/${activity.id}`;
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

        selectedActivity =
            activity;

        closeAllModals();

        if (editActivityForm) {
            editActivityForm.action =
                `/kalender-kegiatan/${activity.id}`;
        }

        const title =
            document.getElementById(
                'edit-title'
            );

        const date =
            document.getElementById(
                'edit-activity-date'
            );

        const startTime =
            document.getElementById(
                'edit-start-time'
            );

        const endTime =
            document.getElementById(
                'edit-end-time'
            );

        const location =
            document.getElementById(
                'edit-location'
            );

        const description =
            document.getElementById(
                'edit-description'
            );

        if (title) {
            title.value =
                activity.title || '';
        }

        if (date) {
            date.value =
                activity.date || '';
        }

        if (startTime) {
            startTime.value =
                activity.start_time || '';
        }

        if (endTime) {
            endTime.value =
                activity.end_time || '';
        }

        if (location) {
            location.value =
                activity.location || '';
        }

        if (description) {
            description.value =
                activity.description || '';
        }

        const selectedPicIds =
            (activity.pics || [])
                .map(function (pic) {
                    return String(pic.id);
                });

        document
            .querySelectorAll(
                '[data-edit-pic]'
            )
            .forEach(function (checkbox) {
                checkbox.checked =
                    selectedPicIds.includes(
                        String(
                            checkbox.value
                        )
                    );
            });

        if (editNoEndTime) {
            editNoEndTime.checked =
                !activity.end_time;
        }

        updateEditEndTimeState();

        openModal(editModal);

        if (title) {
            title.focus();
        }
    }

    /*
    |--------------------------------------------------------------------------
    | End Time
    |--------------------------------------------------------------------------
    */

    function updateCreateEndTimeState() {
        if (
            !createEndTime ||
            !createNoEndTime
        ) {
            return;
        }

        if (createNoEndTime.checked) {
            createEndTime.value = '';

            createEndTime.disabled =
                true;

            createEndTime.classList.add(
                'bg-[#F3F5F3]',
                'text-[#9AA39D]'
            );
        } else {
            createEndTime.disabled =
                false;

            createEndTime.classList.remove(
                'bg-[#F3F5F3]',
                'text-[#9AA39D]'
            );
        }
    }

    function updateEditEndTimeState() {
        if (
            !editEndTime ||
            !editNoEndTime
        ) {
            return;
        }

        if (editNoEndTime.checked) {
            editEndTime.value = '';

            editEndTime.disabled =
                true;

            editEndTime.classList.add(
                'bg-[#F3F5F3]',
                'text-[#9AA39D]'
            );
        } else {
            editEndTime.disabled =
                false;

            editEndTime.classList.remove(
                'bg-[#F3F5F3]',
                'text-[#9AA39D]'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Calendar
    |--------------------------------------------------------------------------
    */

    function renderCalendar() {
        monthTitle.textContent =
            `${monthNames[currentMonth]} ${currentYear}`;

        calendarGrid.innerHTML = '';

        /*
        |--------------------------------------------------------------------------
        | Tinggi setiap minggu
        |--------------------------------------------------------------------------
        */

        calendarGrid.style.gridAutoRows =
            '64px';

        calendarGrid.style.alignItems =
            'start';

        calendarGrid.style.alignContent =
            'start';

        const firstDay =
            new Date(
                currentYear,
                currentMonth,
                1
            );

        const lastDay =
            new Date(
                currentYear,
                currentMonth + 1,
                0
            );

        const firstDayIndex =
            firstDay.getDay();

        const totalDays =
            lastDay.getDate();

        const previousMonthLastDay =
            new Date(
                currentYear,
                currentMonth,
                0
            ).getDate();

        /*
        |--------------------------------------------------------------------------
        | Tanggal bulan sebelumnya
        |--------------------------------------------------------------------------
        */

        for (
            let i = firstDayIndex - 1;
            i >= 0;
            i--
        ) {
            const day =
                previousMonthLastDay - i;

            const date =
                new Date(
                    currentYear,
                    currentMonth - 1,
                    day
                );

            createCalendarDay(
                date,
                true
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tanggal bulan aktif
        |--------------------------------------------------------------------------
        */

        for (
            let day = 1;
            day <= totalDays;
            day++
        ) {
            const date =
                new Date(
                    currentYear,
                    currentMonth,
                    day
                );

            createCalendarDay(
                date,
                false
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Tanggal bulan berikutnya
        |--------------------------------------------------------------------------
        */

        const totalCells =
            calendarGrid.children.length;

        const remainingCells =
            totalCells <= 35
                ? 35 - totalCells
                : 42 - totalCells;

        for (
            let day = 1;
            day <= remainingCells;
            day++
        ) {
            const date =
                new Date(
                    currentYear,
                    currentMonth + 1,
                    day
                );

            createCalendarDay(
                date,
                true
            );
        }
    }

    function createCalendarDay(
        date,
        isOtherMonth
    ) {
        const dateKey =
            formatDateKey(date);

        const eventsForDate =
            getEventsForDate(dateKey);

        const button =
            document.createElement('button');

        button.type = 'button';

        button.className = `
            flex
            h-16
            w-full
            items-start
            justify-center
            pt-1
            text-sm
            transition
        `;

        /*
        |--------------------------------------------------------------------------
        | Warna tanggal bulan lain
        |--------------------------------------------------------------------------
        |
        | Dibuat inline supaya pasti terlihat
        | redup walaupun Tailwind belum rebuild.
        |--------------------------------------------------------------------------
        */

        if (isOtherMonth) {
            button.style.color =
                '#B8C0BA';
        } else {
            button.style.color =
                '#24332A';

            button.classList.add(
                'hover:bg-[#EEF3EF]'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Wrapper angka + titik
        |--------------------------------------------------------------------------
        */

        const content =
            document.createElement('span');

        content.className = `
            flex
            w-8
            flex-col
            items-center
        `;

        /*
        |--------------------------------------------------------------------------
        | Angka
        |--------------------------------------------------------------------------
        */

        const number =
            document.createElement('span');

        number.textContent =
            date.getDate();

        number.className = `
            flex
            h-8
            w-8
            shrink-0
            items-center
            justify-center
            rounded-lg
            leading-none
        `;

        /*
        |--------------------------------------------------------------------------
        | Pastikan angka bulan lain ikut redup
        |--------------------------------------------------------------------------
        */

        if (isOtherMonth) {
            number.style.color =
                '#B8C0BA';
        }

        /*
        |--------------------------------------------------------------------------
        | Selected Date
        |--------------------------------------------------------------------------
        */

        if (dateKey === selectedDate) {
            number.classList.add(
                'bg-[#234936]',
                'font-medium',
                'text-white'
            );

            number.style.color =
                '#FFFFFF';
        }

        /*
        |--------------------------------------------------------------------------
        | Hari ini
        |--------------------------------------------------------------------------
        */

        if (
            dateKey === formatDateKey(today) &&
            dateKey !== selectedDate
        ) {
            number.classList.add(
                'ring-1',
                'ring-[#234936]'
            );
        }

        content.appendChild(
            number
        );

        /*
        |--------------------------------------------------------------------------
        | Activity Indicator
        |--------------------------------------------------------------------------
        */

        if (eventsForDate.length > 0) {
            const indicator =
                document.createElement('span');

            indicator.className = `
                mt-1
                h-1.5
                w-1.5
                shrink-0
                rounded-full
                bg-[#234936]
            `;

            /*
            |--------------------------------------------------------------------------
            | Dot bulan lain juga ikut diredupkan
            |--------------------------------------------------------------------------
            */

            if (isOtherMonth) {
                indicator.style.opacity =
                    '0.35';
            }

            content.appendChild(
                indicator
            );
        }

        button.appendChild(
            content
        );

        /*
        |--------------------------------------------------------------------------
        | Click
        |--------------------------------------------------------------------------
        |
        | Tanggal bulan sebelumnya/berikutnya
        | tetap bisa diklik untuk pindah bulan.
        |--------------------------------------------------------------------------
        */

        button.addEventListener(
            'click',
            function () {
                selectedDate =
                    dateKey;

                if (isOtherMonth) {
                    currentMonth =
                        date.getMonth();

                    currentYear =
                        date.getFullYear();
                }

                renderCalendar();

                renderActivities();
            }
        );

        calendarGrid.appendChild(
            button
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Activity List
    |--------------------------------------------------------------------------
    */

    function renderActivities() {
        selectedDateTitle.textContent =
            formatSelectedDate(
                selectedDate
            );

        const selectedActivities =
            getEventsForDate(
                selectedDate
            );

        activityList.innerHTML = '';

        if (
            selectedActivities.length === 0
        ) {
            const emptyState =
                document.createElement('div');

            emptyState.className = `
                flex
                min-h-[400px]
                items-start
                justify-start
                pt-2
            `;

            emptyState.innerHTML = `
                <p class="text-sm text-[#68746D]">
                    Tidak ada kegiatan pada tanggal ini.
                </p>
            `;

            activityList.appendChild(
                emptyState
            );

            return;
        }

        const list =
            document.createElement('div');

        list.className =
            'divide-y divide-[#E5E9E6]';

        selectedActivities.forEach(
            function (event) {
                const item =
                    document.createElement('div');

                item.className = `
                    group
                    flex
                    items-center
                    justify-between
                    gap-6
                    py-5
                    first:pt-1
                `;

                const location =
                    event.location ||
                    'Lokasi belum ditentukan';

                item.innerHTML = `
                    <div class="min-w-0">

                        <h3 class="truncate text-base font-semibold text-[#24332A]">
                            ${escapeHtml(event.title)}
                        </h3>

                        <div class="mt-2 flex flex-wrap items-center gap-x-5 gap-y-1">

                            <p class="text-sm text-[#68746D]">
                                ${escapeHtml(event.time_label || '-')}
                            </p>

                            <p class="text-sm text-[#68746D]">
                                ${escapeHtml(location)}
                            </p>

                        </div>

                    </div>

                    <button
                        type="button"
                        class="activity-detail-button shrink-0 text-sm font-medium text-[#234936] transition hover:text-[#315A45] hover:underline"
                    >
                        Lihat detail
                    </button>
                `;

                const detailButton =
                    item.querySelector(
                        '.activity-detail-button'
                    );

                detailButton.addEventListener(
                    'click',
                    function () {
                        openDetailModal(
                            event
                        );
                    }
                );

                list.appendChild(
                    item
                );
            }
        );

        activityList.appendChild(
            list
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Previous Month
    |--------------------------------------------------------------------------
    */

    if (previousMonthButton) {
        previousMonthButton.addEventListener(
            'click',
            function () {
                currentMonth--;

                if (currentMonth < 0) {
                    currentMonth = 11;
                    currentYear--;
                }

                selectedDate =
                    formatDateKey(
                        new Date(
                            currentYear,
                            currentMonth,
                            1
                        )
                    );

                renderCalendar();

                renderActivities();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Next Month
    |--------------------------------------------------------------------------
    */

    if (nextMonthButton) {
        nextMonthButton.addEventListener(
            'click',
            function () {
                currentMonth++;

                if (currentMonth > 11) {
                    currentMonth = 0;
                    currentYear++;
                }

                selectedDate =
                    formatDateKey(
                        new Date(
                            currentYear,
                            currentMonth,
                            1
                        )
                    );

                renderCalendar();

                renderActivities();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create
    |--------------------------------------------------------------------------
    */

    if (openCreateButton) {
        openCreateButton.addEventListener(
            'click',
            function () {
                openCreateModal();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit
    |--------------------------------------------------------------------------
    */

    if (openEditFromDetailButton) {
        openEditFromDetailButton.addEventListener(
            'click',
            function () {
                if (!selectedActivity) {
                    return;
                }

                openEditModal(
                    selectedActivity
                );
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Close Modal
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '[data-close-modal]'
        )
        .forEach(function (button) {
            button.addEventListener(
                'click',
                function () {
                    const modalName =
                        button.dataset.closeModal;

                    if (
                        modalName === 'create'
                    ) {
                        closeModal(
                            createModal
                        );
                    }

                    if (
                        modalName === 'detail'
                    ) {
                        closeModal(
                            detailModal
                        );
                    }

                    if (
                        modalName === 'edit'
                    ) {
                        closeModal(
                            editModal
                        );
                    }
                }
            );
        });

    /*
    |--------------------------------------------------------------------------
    | Modal Backdrop
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '[data-modal-backdrop]'
        )
        .forEach(function (backdrop) {
            backdrop.addEventListener(
                'click',
                function (event) {
                    if (
                        event.target !==
                        backdrop
                    ) {
                        return;
                    }

                    const modalName =
                        backdrop.dataset
                            .modalBackdrop;

                    if (
                        modalName === 'create'
                    ) {
                        closeModal(
                            createModal
                        );
                    }

                    if (
                        modalName === 'detail'
                    ) {
                        closeModal(
                            detailModal
                        );
                    }

                    if (
                        modalName === 'edit'
                    ) {
                        closeModal(
                            editModal
                        );
                    }
                }
            );
        });

    /*
    |--------------------------------------------------------------------------
    | Escape
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'keydown',
        function (event) {
            if (
                event.key !==
                'Escape'
            ) {
                return;
            }

            closeAllModals();
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Create End Time
    |--------------------------------------------------------------------------
    */

    if (createNoEndTime) {
        createNoEndTime.addEventListener(
            'change',
            function () {
                updateCreateEndTimeState();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Edit End Time
    |--------------------------------------------------------------------------
    */

    if (editNoEndTime) {
        editNoEndTime.addEventListener(
            'change',
            function () {
                updateEditEndTimeState();
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Validation State
    |--------------------------------------------------------------------------
    */

    const modalState =
        window.activityModalState || {};

    /*
    |--------------------------------------------------------------------------
    | Create Validation
    |--------------------------------------------------------------------------
    */

    if (
        modalState.hasOldInput &&
        modalState.openModal ===
            'create'
    ) {
        openCreateModal();

        updateCreateEndTimeState();
    }

    /*
    |--------------------------------------------------------------------------
    | Edit Validation
    |--------------------------------------------------------------------------
    */

    if (
        modalState.hasOldInput &&
        modalState.openModal ===
            'edit' &&
        modalState.editId
    ) {
        const activity =
            events.find(
                function (event) {
                    return (
                        String(event.id) ===
                        String(
                            modalState.editId
                        )
                    );
                }
            );

        if (activity) {
            openEditModal(
                activity
            );

            const old =
                modalState.old || {};

            const title =
                document.getElementById(
                    'edit-title'
                );

            const date =
                document.getElementById(
                    'edit-activity-date'
                );

            const startTime =
                document.getElementById(
                    'edit-start-time'
                );

            const endTime =
                document.getElementById(
                    'edit-end-time'
                );

            const location =
                document.getElementById(
                    'edit-location'
                );

            const description =
                document.getElementById(
                    'edit-description'
                );

            if (
                old.title !== null &&
                old.title !== undefined &&
                title
            ) {
                title.value =
                    old.title;
            }

            if (
                old.activity_date !== null &&
                old.activity_date !== undefined &&
                date
            ) {
                date.value =
                    old.activity_date;
            }

            if (
                old.start_time !== null &&
                old.start_time !== undefined &&
                startTime
            ) {
                startTime.value =
                    old.start_time;
            }

            if (
                old.end_time !== null &&
                old.end_time !== undefined &&
                endTime
            ) {
                endTime.value =
                    old.end_time;

                if (editNoEndTime) {
                    editNoEndTime.checked =
                        false;
                }
            } else {
                if (editNoEndTime) {
                    editNoEndTime.checked =
                        true;
                }
            }

            if (
                old.location !== null &&
                old.location !== undefined &&
                location
            ) {
                location.value =
                    old.location;
            }

            if (
                old.description !== null &&
                old.description !== undefined &&
                description
            ) {
                description.value =
                    old.description;
            }

            const oldPicIds =
                (old.pic_ids || [])
                    .map(function (id) {
                        return String(id);
                    });

            document
                .querySelectorAll(
                    '[data-edit-pic]'
                )
                .forEach(function (checkbox) {
                    checkbox.checked =
                        oldPicIds.includes(
                            String(
                                checkbox.value
                            )
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