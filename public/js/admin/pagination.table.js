document.addEventListener('DOMContentLoaded', function() {

    const itemsPerPage = 7; // max filas
    const tableBody = document.getElementById('table-body');
    const pageContenido = document.querySelector('.pagination');
    const searchInput = document.getElementById('search-table');
    
    // Obtener todas las filas 
    const allRows = tableBody.querySelectorAll('tr');
    
    if (allRows.length === 0 || (allRows.length === 1 && allRows[0].querySelector('td[colspan]'))) {
        return;
    }
    
    let filteredRows = Array.from(allRows);
    
    let currentPage = 1;
    
    // filtrar las filas x search
    function filterRows() {
        const searchTerm = searchInput.value.toLowerCase();
    
        if (searchTerm === '') {
            filteredRows = Array.from(allRows);
        } else {
            filteredRows = Array.from(allRows).filter(row => {
                if (row.querySelector('td[colspan]')) {
                    return false;
                }
    
                // Search en todas ls celdas de la fila
                const cells = row.querySelectorAll('td');
                let found = false;
    
                cells.forEach(cell => {
                    if (!cell.querySelector('form') && !cell.querySelector('button')) {
                        if (cell.textContent.toLowerCase().includes(searchTerm)) {
                            found = true;
                        }
                    }
                });
    
                return found;
            });
        }
    
        currentPage = 1;
    
        displayRows();
    }
    
    // morar primera pagina de filas
    function displayRows() {
        allRows.forEach(row => {
            row.style.display = 'none';
        });
    
        // total de paginas
        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
    
        if (filteredRows.length === 0) {
            
            let noResultsRow = tableBody.querySelector('.no-results');
    
            if (!noResultsRow) {
                
                noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results';
                const noResultsCell = document.createElement('td');
                noResultsCell.colSpan = allRows[0].querySelectorAll('td').length || 6;
                noResultsCell.textContent = 'No se encontraron resultados para la búsqueda.';
                noResultsCell.className = 'text-center';
                noResultsRow.appendChild(noResultsCell);
                tableBody.appendChild(noResultsRow);
            }
    
            noResultsRow.style.display = '';
        } else {
            const noResultsRow = tableBody.querySelector('.no-results');
            if (noResultsRow) {
                noResultsRow.style.display = 'none';
            }
    
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = Math.min(startIndex + itemsPerPage, filteredRows.length);
    
            for (let i = startIndex; i < endIndex; i++) {
                filteredRows[i].style.display = '';
            }
        }
    
        // Actualizar la paginación
        updatePaginationControls();
    }
    
    function updatePaginationControls() {
        const totalPages = Math.ceil(filteredRows.length / itemsPerPage);
    
        pageContenido.innerHTML = '';
    
        if (filteredRows.length === 0 || totalPages <= 1) {
            pageContenido.style.display = 'none';
            return;
        }
    
        pageContenido.style.display = '';
    
        const prevItem = document.createElement('li');
        prevItem.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
        prevItem.style.cursor = 'not-allowed';
        const prevLink = document.createElement('a');
        prevLink.className = 'page-link';
        prevLink.href = '#';
        prevLink.textContent = '«';
        prevLink.addEventListener('click', function (e) {
            e.preventDefault();
            if (currentPage > 1) {
                currentPage--;
                displayRows();
            }
        });
        prevItem.appendChild(prevLink);
        pageContenido.appendChild(prevItem);
    
        // Max botones por paginacoin
        const maxPageButtons = 5;
        let startPage = Math.max(1, currentPage - Math.floor(maxPageButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxPageButtons - 1);
    
        if (endPage - startPage + 1 < maxPageButtons) {
            startPage = Math.max(1, endPage - maxPageButtons + 1);
        }
    
        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
            const pageLink = document.createElement('a');
            pageLink.className = 'page-link';
            pageLink.href = '#';
            pageLink.textContent = i;
            pageLink.addEventListener('click', function (e) {
                e.preventDefault();
                currentPage = i;
                displayRows();
            });
            pageItem.appendChild(pageLink);
            pageContenido.appendChild(pageItem);
        }
    
        // Siguiente botno
        const nextItem = document.createElement('li');
        nextItem.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
        nextItem.style.cursor = 'not-allowed';
        const nextLink = document.createElement('a');
        nextLink.className = 'page-link';
        nextLink.href = '#';
        nextLink.textContent = '»';
        nextLink.addEventListener('click', function (e) {
            e.preventDefault();
            if (currentPage < totalPages) {
                currentPage++;
                displayRows();
            }
        });
        nextItem.appendChild(nextLink);
        pageContenido.appendChild(nextItem);
    }
    
    searchInput.addEventListener('input', filterRows);
    
    displayRows();
});



// const inputSearchTable = document.getElementById('search-table');

// inputSearchTable.addEventListener('input', function() {
//     const searchTerm = inputSearchTable.value.toLowerCase();
//     const rows = document.querySelectorAll('#table-body tr');
    
//     rows.forEach(row => {
//         const cells = row.querySelectorAll('td');
//         let found = false;
        
//         cells.forEach(cell => {
//             if (cell.textContent.toLowerCase().includes(searchTerm)) {
//                 found = true;
//             }
//         });
        
//         if (found) {
//             row.style.display = '';
//         } else {
//             row.style.display = 'none';
//         }
//     });
    
//     // Reset pagination
//     currentPage = 1;
//     displayRows();
// });

