export class PLFormSort {
    constructor() {
    }

    initialSort() {
        const tables = document.querySelectorAll('.table');

        tables.forEach(table => {
            const sortButtons = [...table.querySelectorAll('.table__sort')];
            _.forEach(sortButtons, (button) => {
                button.addEventListener('click', () => {
                    const sortField = button.dataset.sortField;
                    const sortDirection = button.dataset.sortDirection || 'asc';

                    const tableRows = table.querySelectorAll('tbody tr');

                    const rowsArray = Array.from(tableRows);

                    rowsArray.sort((a, b) => {
                        let aValue, bValue;

                        aValue = a.querySelector(
                            `td[data-sort-field="${sortField}"]`)
                            ?.textContent.trim() || '';

                        bValue = b.querySelector(
                            `td[data-sort-field="${sortField}"]`)
                            ?.textContent.trim() || '';

                        // Обрабатываем пустые значения
                        if (aValue === '' && bValue === '') return 0;
                        if (aValue === '') return sortDirection === 'asc' ? 1 : -1;
                        if (bValue === '') return sortDirection === 'asc' ? -1 : 1;

                        if (aValue < bValue) return sortDirection === 'asc' ? -1 : 1;
                        if (aValue > bValue) return sortDirection === 'asc' ? 1 : -1;
                        return 0;
                    });

                    // Обновите порядок строк в таблице
                    const tableBody = table.querySelector('tbody');

                    rowsArray.forEach(row => {
                        tableBody.appendChild(row);
                    });

                    button.dataset.sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';

                    const wasActive = button.classList.contains('active')
                    sortButtons.forEach(btn => btn.classList.remove('active'));

                    if (!wasActive) {
                        button.classList.add('active')
                    }
                });
            });
        });
    }
}