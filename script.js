document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('damForm');
    const submitBtn = document.getElementById('submitBtn');
    const errorMessage = document.getElementById('errorMessage');
    const manufacturingDate = document.getElementById('manufacturingDate');
    const factory = document.getElementById('factory');
    const factoryName = document.getElementById('factoryName');

    // Vérification de la présence des éléments nécessaires
    const elements = {
        form: form,
        submitBtn: submitBtn,
        errorMessage: errorMessage,
        manufacturingDate: manufacturingDate,
        factory: factory,
        factoryName: factoryName
    };

    const missingElements = Object.entries(elements)
        .filter(([key, element]) => !element)
        .map(([key]) => key);

    if (missingElements.length > 0) {
        return; // Arrêter l'exécution si des éléments sont manquants
    }

    function highlightCell(dateStr) {
        // Supprimer la surbrillance précédente
        document.querySelectorAll('.highlight-cell').forEach(cell => {
            cell.classList.remove('highlight-cell');
        });

        // Convertir la date du format dd/mm/yyyy en objet Date
        const [day, month, year] = dateStr.split('/').map(Number);
        
        // Trouver la ligne de l'année
        const rows = document.querySelectorAll('.calendar-table tbody tr');
        let targetRow = null;
        
        for (const row of rows) {
            const yearCell = row.querySelector('td:first-child');
            if (yearCell && yearCell.textContent.trim() === year.toString()) {
                targetRow = row;
                break;
            }
        }

        if (targetRow) {
            // Les cellules commencent à l'index 1 car l'index 0 est l'année
            const cells = targetRow.querySelectorAll('td');
            if (cells.length > month) {
                cells[month].classList.add('highlight-cell');
            }
        }
    }

    function handleSubmit(e) {
        if (e) {
            e.preventDefault();
        }
        
        const formData = new FormData(form);
        
        fetch('traitement.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                errorMessage.textContent = data.error;
                errorMessage.style.display = 'block';
            } else {
                errorMessage.style.display = 'none';
                manufacturingDate.textContent = data.manufacturingDate;
                factory.textContent = data.factory;
                factoryName.textContent = data.factoryName ? `(${data.factoryName})` : '';
                
                // Mettre en surbrillance la cellule correspondante
                if (data.manufacturingDate !== '-') {
                    highlightCell(data.manufacturingDate);
                }
            }
        })
        .catch(error => {
            if (errorMessage) {
                errorMessage.textContent = 'Une erreur est survenue lors de la communication avec le serveur.';
                errorMessage.style.display = 'block';
            }
        });
    }

    // Gestionnaire pour le bouton
    submitBtn.addEventListener('click', function(e) {
        handleSubmit(e);
    });

    // Gestionnaire pour la touche Entrée
    document.getElementById('damCode').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleSubmit();
        }
    });

    // Modal related elements
    const monthModal = document.getElementById('monthModal');
    const closeButton = document.querySelector('.close-button');
    const modalMonthYear = document.getElementById('modalMonthYear');
    const modalTableContainer = document.getElementById('modalTableContainer');
    const calendarTable = document.querySelector('.calendar-table');

    // Function to get days in a month, considering leap years
    function daysInMonth(year, month) {
        return new Date(year, month, 0).getDate();
    }

    // Function to generate the month table
    function generateMonthTable(year, month) {
        const numDays = daysInMonth(year, month);
        const firstDayOfMonth = new Date(year, month - 1, 1).getDay(); // 0 for Sunday, 1 for Monday, etc.
        const daysOfWeek = ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'];

        let tableHTML = '<table class="modal-table"><thead><tr>';
        daysOfWeek.forEach(day => {
            tableHTML += `<th>${day}</th>`;
        });
        tableHTML += '</tr></thead><tbody>';

        let day = 1;
        const damStartDate = new Date('1976-11-09');

        // Adjust start day to be Monday (1) instead of Sunday (0)
        let startOffset = firstDayOfMonth === 0 ? 6 : firstDayOfMonth - 1; // If Sunday (0), offset is 6. Otherwise, day number - 1.

        for (let i = 0; i < 6; i++) { // Max 6 weeks in a month view
            let rowHTML = '<tr>';
            for (let j = 0; j < 7; j++) { // 7 days a week
                if (i === 0 && j < startOffset) {
                    rowHTML += '<td></td>'; // Empty cells for days before the 1st
                } else if (day > numDays) {
                    rowHTML += '<td></td>'; // Empty cells for days after the last day of the month
                } else {
                    const currentMonthDate = new Date(year, month - 1, day);
                    const formattedDate = `${String(day).padStart(2, '0')}/${String(month).padStart(2, '0')}/${year}`;
                    let damCode = '-';

                    if (currentMonthDate >= damStartDate) {
                        const diffTime = Math.abs(currentMonthDate.getTime() - damStartDate.getTime());
                        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                        damCode = diffDays + 1;
                    }
                    rowHTML += `<td title="${formattedDate}"><span class="day-number">${day}</span><span class="dam-number">${damCode}</span></td>`;
                    day++;
                }
            }
            rowHTML += '</tr>';
            tableHTML += rowHTML;
            if (day > numDays) break; // Exit if all days have been added
        }

        tableHTML += '</tbody></table>';
        return tableHTML;
    }

    // Open modal event listener
    if (calendarTable) {
        calendarTable.addEventListener('click', function(event) {
            const targetCell = event.target.closest('td[data-year][data-month]');
            if (targetCell) {
                const year = parseInt(targetCell.dataset.year);
                const month = parseInt(targetCell.dataset.month);
                
                // Prevent opening modal for empty cells
                if (targetCell.textContent.trim() === '-') {
                    return;
                }

                const monthName = new Date(year, month - 1).toLocaleString('fr-FR', { month: 'long' });
                modalMonthYear.textContent = `${monthName.charAt(0).toUpperCase() + monthName.slice(1)} ${year}`;
                modalTableContainer.innerHTML = generateMonthTable(year, month);
                monthModal.style.display = 'flex'; // Use flex to center the modal
            }
        });
    }

    // Close modal events
    if (closeButton) {
        closeButton.addEventListener('click', function() {
            monthModal.style.display = 'none';
        });
    }

    window.addEventListener('click', function(event) {
        if (event.target === monthModal) {
            monthModal.style.display = 'none';
        }
    });

    // FAQ Accordion functionality
    const faqItems = document.querySelectorAll('.faq-item');

    faqItems.forEach(item => {
        const question = item.querySelector('h3');
        const answer = item.querySelector('.faq-answer');

        question.addEventListener('click', () => {
            // Close other open FAQ items
            faqItems.forEach(otherItem => {
                if (otherItem !== item && otherItem.classList.contains('active')) {
                    otherItem.classList.remove('active');
                    otherItem.querySelector('.faq-answer').style.maxHeight = 0;
                }
            });

            // Toggle current FAQ item
            item.classList.toggle('active');
            if (item.classList.contains('active')) {
                answer.style.maxHeight = answer.scrollHeight + "px";
            } else {
                answer.style.maxHeight = 0;
            }
        });
    });
}); 