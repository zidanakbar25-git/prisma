document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('activity-calendar');

    if (!calendarEl) {
        return;
    }

    const events = JSON.parse(
        calendarEl.dataset.events || '[]'
    );

    const calendarGrid = document.getElementById('calendar-grid');
    const monthTitle = document.getElementById('calendar-month-title');
    const selectedDateTitle = document.getElementById('selected-date-title');
    const activityList = document.getElementById('activity-list');

    const previousMonthButton = document.getElementById('previous-month');
    const nextMonthButton = document.getElementById('next-month');
    const todayButton = document.getElementById('today-button');

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

    let selectedDate = formatDateKey(today);


    /*
    |--------------------------------------------------------------------------
    | Helper
    |--------------------------------------------------------------------------
    */

    function formatDateKey(date) {

        const year = date.getFullYear();

        const month = String(
            date.getMonth() + 1
        ).padStart(2, '0');

        const day = String(
            date.getDate()
        ).padStart(2, '0');

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


    /*
    |--------------------------------------------------------------------------
    | Render Calendar
    |--------------------------------------------------------------------------
    */

    function renderCalendar() {

        monthTitle.textContent =
            `${monthNames[currentMonth]} ${currentYear}`;

        calendarGrid.innerHTML = '';

        const firstDay = new Date(
            currentYear,
            currentMonth,
            1
        );

        const lastDay = new Date(
            currentYear,
            currentMonth + 1,
            0
        );

        const firstDayIndex = firstDay.getDay();

        const totalDays = lastDay.getDate();


        /*
        |--------------------------------------------------------------------------
        | Previous month dates
        |--------------------------------------------------------------------------
        */

        const previousMonthLastDay = new Date(
            currentYear,
            currentMonth,
            0
        ).getDate();


        for (let i = firstDayIndex - 1; i >= 0; i--) {

            const day = previousMonthLastDay - i;

            const date = new Date(
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
        | Current month dates
        |--------------------------------------------------------------------------
        */

        for (let day = 1; day <= totalDays; day++) {

            const date = new Date(
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
        | Next month dates
        |--------------------------------------------------------------------------
        */

        const totalCells = calendarGrid.children.length;

        const remainingCells =
            totalCells <= 35
                ? 35 - totalCells
                : 42 - totalCells;


        for (let day = 1; day <= remainingCells; day++) {

            const date = new Date(
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


    /*
    |--------------------------------------------------------------------------
    | Create Calendar Day
    |--------------------------------------------------------------------------
    */

    function createCalendarDay(date, isOtherMonth) {

        const dateKey = formatDateKey(date);

        const eventsForDate =
            getEventsForDate(dateKey);

        const button =
            document.createElement('button');

        button.type = 'button';

        button.className = `
            relative
            flex
            items-center
            justify-center
            h-12
            w-full
            text-sm
            rounded-md
            transition
        `;


        /*
        |--------------------------------------------------------------------------
        | Other month
        |--------------------------------------------------------------------------
        */

        if (isOtherMonth) {

            button.classList.add(
                'text-[#B8C0BA]'
            );

        } else {

            button.classList.add(
                'text-[#24332A]',
                'hover:bg-[#EEF3EF]'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Selected date
        |--------------------------------------------------------------------------
        */

        if (dateKey === selectedDate) {

            button.classList.remove(
                'hover:bg-[#EEF3EF]'
            );

            button.classList.add(
                'bg-[#234936]',
                'text-white',
                'font-medium'
            );

        }


        /*
        |--------------------------------------------------------------------------
        | Today
        |--------------------------------------------------------------------------
        */

        if (
            dateKey === formatDateKey(today) &&
            dateKey !== selectedDate
        ) {

            button.classList.add(
                'ring-1',
                'ring-[#234936]'
            );

        }


        const number = document.createElement('span');

        number.textContent =
            date.getDate();

        button.appendChild(number);


        /*
        |--------------------------------------------------------------------------
        | Activity Indicator
        |--------------------------------------------------------------------------
        */

        if (eventsForDate.length > 0) {

            const indicator =
                document.createElement('span');

            indicator.className = `
                absolute
                bottom-1
                left-1/2
                -translate-x-1/2
                w-1.5
                h-1.5
                rounded-full
                ${dateKey === selectedDate
                    ? 'bg-white'
                    : 'bg-[#234936]'
                }
            `;

            button.appendChild(indicator);

        }


        button.addEventListener(
            'click',
            function () {

                selectedDate = dateKey;

                /*
                |--------------------------------------------------------------------------
                | Jika klik tanggal bulan lain
                |--------------------------------------------------------------------------
                */

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


        calendarGrid.appendChild(button);

    }


    /*
    |--------------------------------------------------------------------------
    | Render Activities
    |--------------------------------------------------------------------------
    */

    function renderActivities() {

        selectedDateTitle.textContent =
            formatSelectedDate(selectedDate);

        const selectedActivities =
            getEventsForDate(selectedDate);

        activityList.innerHTML = '';


        /*
        |--------------------------------------------------------------------------
        | No Activity
        |--------------------------------------------------------------------------
        */

        if (selectedActivities.length === 0) {

            const emptyState =
                document.createElement('div');

            emptyState.className = `
                flex
                items-center
                justify-center
                min-h-[320px]
                text-center
            `;

            emptyState.innerHTML = `
                <div>
                    <p class="text-sm font-medium text-[#24332A]">
                        Tidak ada kegiatan
                    </p>

                    <p class="mt-1 text-sm text-[#6B7280]">
                        Tidak ada agenda pada tanggal ini.
                    </p>
                </div>
            `;

            activityList.appendChild(
                emptyState
            );

            return;

        }


        /*
        |--------------------------------------------------------------------------
        | Activity Cards
        |--------------------------------------------------------------------------
        */

        selectedActivities.forEach(function (event) {

            const card =
                document.createElement('div');

            card.className = `
                border
                border-[#DCE4DE]
                bg-[#F8FAF8]
                rounded-lg
                p-5
                hover:border-[#B9C9BC]
                transition
            `;


            const startTime =
                event.start
                    ? event.start.substring(11, 16)
                    : '';

            let endTime = '';

            if (event.end) {

                endTime =
                    event.end.substring(11, 16);

            }


            let timeText = startTime;

            if (endTime) {

                timeText +=
                    ` - ${endTime}`;

            } else {

                timeText +=
                    ' - Selesai';

            }


            const location =
                event.extendedProps &&
                event.extendedProps.location
                    ? event.extendedProps.location
                    : '-';


            card.innerHTML = `

                <div>

                    <h3 class="text-base font-semibold text-[#24332A]">
                        ${escapeHtml(event.title)}
                    </h3>

                </div>


                <div class="mt-3 space-y-1">

                    <p class="text-sm text-[#6B7280]">
                        ${escapeHtml(timeText)}
                    </p>

                    <p class="text-sm text-[#6B7280]">
                        ${escapeHtml(location)}
                    </p>

                </div>


                <div class="mt-4 flex justify-end">

                    <a
                        href="${event.url}"
                        class="text-sm font-medium text-[#234936] hover:underline"
                    >
                        Lihat detail →
                    </a>

                </div>

            `;


            activityList.appendChild(
                card
            );

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const div =
            document.createElement('div');

        div.textContent =
            value ?? '';

        return div.innerHTML;

    }


    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

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


    todayButton.addEventListener(
        'click',
        function () {

            currentMonth =
                today.getMonth();

            currentYear =
                today.getFullYear();

            selectedDate =
                formatDateKey(today);

            renderCalendar();

            renderActivities();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Initial Render
    |--------------------------------------------------------------------------
    */

    renderCalendar();

    renderActivities();

});