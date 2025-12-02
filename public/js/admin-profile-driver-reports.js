
    // Function to sort table rows
    function sortTable(criteria) {
      const tbody = document.getElementById('driversTableBody');
      const rows = Array.from(tbody.querySelectorAll('tr'));
      
      rows.sort((a, b) => {
        const aValue = parseInt(a.querySelector(`td:nth-child(${criteria.includes('totalTrips') ? 3 : criteria.includes('acceptedTrips') ? 4 : 5})`).textContent);
        const bValue = parseInt(b.querySelector(`td:nth-child(${criteria.includes('totalTrips') ? 3 : criteria.includes('acceptedTrips') ? 4 : 5})`).textContent);
        
        if (criteria.includes('Desc')) {
          return bValue - aValue;
        } else {
          return aValue - bValue;
        }
      });

      tbody.innerHTML = '';
      rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1; // Update row number
        tbody.appendChild(row);
      });
    }

    // Event listeners
    document.getElementById('sortFilter').addEventListener('change', (e) => {
      sortTable(e.target.value);
    });

    // Add click event to all table cells
    document.querySelectorAll('.driver-reports-table td').forEach(cell => {
      cell.addEventListener('click', () => {
        const modal = document.getElementById('tripModal');
        modal.style.display = 'block';
      });
    });

    // Modal close functionality
    const modal = document.getElementById('tripModal');
    const closeBtn = document.getElementsByClassName('close')[0];

    closeBtn.addEventListener('click', () => {
      modal.style.display = 'none';
    });

    window.addEventListener('click', (e) => {
      if (e.target === modal) {
        modal.style.display = 'none';
      }
    });

    // Toggle trip details
    document.querySelectorAll(".passenger-trip-item").forEach(item => {
      item.addEventListener("click", () => {
        const content = item.nextElementSibling;

        document.querySelectorAll(".passenger-trip-content").forEach(c => {
          if (c !== content) c.classList.remove("active");
        });
        document.querySelectorAll(".passenger-trip-item").forEach(i => {
          if (i !== item) i.classList.remove("active");
        });

        content.classList.toggle("active");
        item.classList.toggle("active");
      });
    });