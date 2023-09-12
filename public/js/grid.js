(() => {
    function showHours(e) {
        const clicked_hours_list = document.getElementById(`${e.target.id}-hours`);
        if (clicked_hours_list == null) { // User didn't click hours button
            return;
        }
        const hours = clicked_hours_list.querySelector('.closed');
        const button_text = document.getElementById(e.target.id);

        if (hours !== null) {
            setStacked(clicked_hours_list);

            hours.classList.remove('closed');
            hours.classList.add('open');
            hours.setAttribute('aria-hidden', 'false');
            button_text.textContent = 'View week';
        } else {
            const hide_hours = clicked_hours_list.querySelector('.open');
            hide_hours.classList.remove('open');
            hide_hours.classList.add('closed');
            hide_hours.setAttribute('aria-hidden', 'true');
            button_text.textContent = 'View all hours';

            setTimeout(setStacked, 1200, clicked_hours_list);
        }
    }

    function setStacked(clicked_hours_list) {
        clicked_hours_list.classList.toggle('hours-display-stacked');
        clicked_hours_list.classList.toggle('hours-display');
    }

    document.addEventListener('click', showHours);
})();