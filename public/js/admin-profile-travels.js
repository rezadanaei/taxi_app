// Admin Profile Travels
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

// Status Filter Functionality
const statusButtons = document.querySelectorAll(".status-btn");
const tripItems = document.querySelectorAll(".passenger-current-trip li:not(.no-results)");
const noResults = document.querySelector(".no-results");

function updateNoResultsMessage() {
  const visibleItems = Array.from(tripItems).filter(item => item.style.display !== "none");
  noResults.style.display = visibleItems.length === 0 ? "block" : "none";
}

statusButtons.forEach(button => {
  button.addEventListener("click", () => {
    const status = button.getAttribute("data-status");

    // Update active button
    statusButtons.forEach(btn => btn.classList.remove("active"));
    button.classList.add("active");

    // Filter trips
    tripItems.forEach(item => {
      const tripStatusElement = item.querySelector(".trip-state");
      if (!tripStatusElement) {
        item.style.display = "none";
        return;
      }
      const tripStatus = tripStatusElement.textContent;
      if (status === "همه سفرها" || tripStatus === status) {
        item.style.display = "block";
      } else {
        item.style.display = "none";
      }
    });

    updateNoResultsMessage();
  });
});

// Search Functionality
const searchBox = document.querySelector(".search-box");

searchBox.addEventListener("input", () => {
  const searchTerm = searchBox.value.trim().toLowerCase();

  tripItems.forEach(item => {
    const tripIdElement = item.querySelector(".trip-id");
    if (!tripIdElement) {
      item.style.display = "none";
      return;
    }
    const tripId = tripIdElement.textContent.replace("کد سفر: ", "").trim().toLowerCase();
    if (tripId.includes(searchTerm)) {
      item.style.display = "block";
    } else {
      item.style.display = "none";
    }
  });

  updateNoResultsMessage();
});

// Sort Functionality
const sortDropdown = document.querySelector(".sort-dropdown");

sortDropdown.addEventListener("change", () => {
  const sortValue = sortDropdown.value;
  const tripList = document.querySelector(".passenger-current-trip ul");
  const itemsArray = Array.from(tripItems);

  itemsArray.sort((a, b) => {
    const dateAElement = a.querySelector(".trip-date");
    const dateBElement = b.querySelector(".trip-date");
    const idAElement = a.querySelector(".trip-id");
    const idBElement = b.querySelector(".trip-id");
    const priceAElements = a.querySelectorAll(".trip-time");
    const priceBElements = b.querySelectorAll(".trip-time");

    if (!dateAElement || !dateBElement || !idAElement || !idBElement) {
      return 0;
    }

    const dateA = dateAElement.textContent.replace("تاریخ: ", "").trim();
    const dateB = dateBElement.textContent.replace("تاریخ: ", "").trim();
    const idA = parseInt(idAElement.textContent.replace("کد سفر: ", "").trim());
    const idB = parseInt(idBElement.textContent.replace("کد سفر: ", "").trim());

    let priceA = 0, priceB = 0;
    priceAElements.forEach(el => {
      if (el.textContent.includes("هزینه سفر")) {
        priceA = parseInt(el.textContent.replace("هزینه سفر: ", "").replace(" تومان", "").trim());
      }
    });
    priceBElements.forEach(el => {
      if (el.textContent.includes("هزینه سفر")) {
        priceB = parseInt(el.textContent.replace("هزینه سفر: ", "").replace(" تومان", "").trim());
      }
    });

    if (sortValue === "date-desc") {
      return new Date(convertPersianDate(dateB)) - new Date(convertPersianDate(dateA));
    } else if (sortValue === "date-asc") {
      return new Date(convertPersianDate(dateA)) - new Date(convertPersianDate(dateB));
    } else if (sortValue === "id-desc") {
      return idB - idA;
    } else if (sortValue === "id-asc") {
      return idA - idB;
    } else if (sortValue === "price-desc") {
      return priceB - priceA;
    } else if (sortValue === "price-asc") {
      return priceA - priceB;
    }
  });

  // Re-append sorted items
  itemsArray.forEach(item => tripList.appendChild(item));
});

// Convert Persian date to Gregorian for sorting
function convertPersianDate(persianDate) {
  // Example: "08 مرداد 1404" -> "1404/05/08"
  const months = {
    "فروردین": "01", "اردیبهشت": "02", "خرداد": "03", "تیر": "04",
    "مرداد": "05", "شهریور": "06", "مهر": "07", "آبان": "08",
    "آذر": "09", "دی": "10", "بهمن": "11", "اسفند": "12"
  };
  const [day, month, year] = persianDate.split(" ");
  return `${year}/${months[month]}/${day}`;
}

// document.querySelectorAll('#AddDriverTrip').forEach(button => {
//     button.addEventListener('click', () => {
//         const tripId = button.dataset.tripid;
//         document.getElementById('selectedTripId').value = tripId;
//         document.getElementById('AddDriverTripPopup').style.display = 'block';
//       });
// });

// close popup 
const adtp = document.getElementById('AddDriverTripPopup');

window.addEventListener('click', (e) => {
  if (e.target === adtp) {
    adtp.style.display = 'none';
  }
});

// Admin Profile Travels end