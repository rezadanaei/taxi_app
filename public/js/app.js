// Read zones from meta tag
document.addEventListener("DOMContentLoaded", () => {
    const metaTag = document.querySelector('meta[name="zones"]');
    if (!metaTag) return;

    const content = metaTag.getAttribute('content')
        .replace(/&quot;/g, '"'); 

    const zonesData = JSON.parse(content);

    zonesData.forEach(zone => {
        addSpecialArea(
            parseFloat(zone.latitude),
            parseFloat(zone.longitude),
            parseFloat(zone.radius_km)
        );
    });


    metaTag.remove();
});
// Special Locations
const specialAreas = [];
// Function to add Special locations
function addSpecialArea(lat, lng, radiusKm) {
    specialAreas.push({
        center: { lat, lng },
        radius: radiusKm * 1000,
        multiplier: 1.7
    });
}
// Swiper
var carChoseSwiper = new Swiper(".carChoseSwiper", {
  slidesPerView: 1.8,
  spaceBetween: 14,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  breakpoints: {
    768: {
      slidesPerView: 3,
    },
    1024: {
      slidesPerView: 4,
    },
  },
});

// User Current Trip
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

// Header height
const controlsHeight = document.querySelector("#controls");
const containerAlert = document.querySelector(".margin-bottom");

function updateContainerMargin() {
    let headerHeight = controlsHeight.offsetHeight;
    if (containerAlert) {
        containerAlert.style.bottom = `${headerHeight}px`;
    }
}
updateContainerMargin();
window.addEventListener("resize", updateContainerMargin);

// User Have a Trip popup
const openPopupBtn = document.getElementById('openUserCurrentTripPopup');

if (openPopupBtn) {
    openPopupBtn.addEventListener('click', async () => {
        const popup = document.getElementById('UserCurrentTripPopup');
        if (popup) {
            popup.style.display = 'block';
        }
    });
}


const closePopupBtn = document.getElementById('closeUserCurrentTripPopup');

if (closePopupBtn) {
    closePopupBtn.addEventListener('click', async () => {
        const popup = document.getElementById('UserCurrentTripPopup');
        if (popup) {
            popup.style.display = 'none';
        }
    });
}


// Jalali Datapicker and set max days to 30
const today = new Date();
const maxDate = new Date(today);
maxDate.setDate(today.getDate() + 30);
const [jy, jm, jd] = gregorian_to_jalali(maxDate.getFullYear(), maxDate.getMonth() + 1, maxDate.getDate());
const maxDateString = `${jy}/${jm.toString().padStart(2, '0')}/${jd.toString().padStart(2, '0')}`;

document.getElementById("rideDate").setAttribute("data-jdp-max-date", maxDateString);

jalaliDatepicker.startWatch({
    separatorChars: {
        date: '/',
        time: ':',
        between: ' '
    },
    date: true,
    time: false,
    autoShow: true,
    autoHide: true,
    hasSecond: false,
    hideAfterChange: true,
    persianDigits: false,
    format: 'YYYY/MM/DD',
    zIndex: 1000,
    useDropDownYears: false,
    showSelectTimeBtnAlways: true,
    minDate: "today",
    maxDate: "attr"
});


// Add Neshan API to Here



async function myGlobalFunction() {
    const baseUrl = window.location.origin; 
    const response = await fetch(`${baseUrl}/neshan`, {
        method: "GET",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            "Accept": "application/json"
        }
    });

    if (!response.ok) {
        console.error("خطا در دریافت داده:", response.status, response.statusText);
        return null;
    }

    const data = await response.json();
    return data;
}


async function run() {
    const result = await myGlobalFunction();
    let API_KEY_WEB = result.API_KEY_WEB;
    let API_KEY_SERVICE = result.API_KEY_SERVICE;
    return result;
}

const result = await run();

let API_KEY_WEB = result.API_KEY_WEB;
let API_KEY_SERVICE = result.API_KEY_SERVICE;






// Add map
let map;
const defaultCenter = [35.6892, 51.3890];

function isMobileOrTablet() {
    return /Android|iPhone|iPad|iPod|Tablet/i.test(navigator.userAgent);
}

let initialCenter = defaultCenter;

function createMap(center) {
    map = new L.Map('map', {  
        key: API_KEY_WEB,
        maptype: 'dreamy',
        poi: true,
        traffic: false,
        center: center,
        zoom: 14
    });
}

function updateMapCenter(center) {
    if (map) {
        map.setView(center, 14); 
    }
}

createMap(initialCenter);

if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
        position => {
            const userCenter = [position.coords.latitude, position.coords.longitude];
            updateMapCenter(userCenter); 
        },
        error => {
            console.warn("Geolocation error:", error.message);
        }
    );
}


const locateBtn = document.getElementById("locate-user");

locateBtn.addEventListener("click", () => {
    navigator.geolocation.getCurrentPosition(
        (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;

            map.flyTo([lat, lng], 16, {
                duration: 0.9   
            });
        },
        () => {
        },
        {
            enableHighAccuracy: true,   
            maximumAge: 0,             
            timeout: 6000              
        }
    );
});



// Origin Icon
const originIcon = L.divIcon({
  html: `<div class="marker-wrapper">
    <svg width="43" height="60" viewBox="0 0 43 60" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M36 56C36 58.2091 29.0604 60 20.5 60C11.9396 60 5 58.2091 5 56C5 53.7909 11.9396 52 20.5 52C29.0604 52 36 53.7909 36 56Z" fill="#202020" fill-opacity="0.33"/>
      <path d="M41.5182 24.5117C39.0182 34.5117 27.5182 48.6784 21.0182 56.0117C15.3515 49.0117 3.14974 34.5117 0.51819 24.5117C-1.98182 15.0117 4.51817 -0.488318 21.0182 0.011682C37.5182 0.511682 44.0182 14.5118 41.5182 24.5117Z" fill="#FF5E00"/>
      <path d="M31 21.5C31 27.299 26.299 32 20.5 32C14.701 32 10 27.299 10 21.5C10 15.701 14.701 11 20.5 11C26.299 11 31 15.701 31 21.5Z" fill="white"/>
    </svg>
  </div>`,
  iconSize: [43, 60],
  iconAnchor: [21.5, 60],
  popupAnchor: [0, -60],
  className: ''
});

// Destination Icon
const destinationIcon = L.divIcon({
  html: `<div class="marker-wrapper">
    <svg width="43" height="60" viewBox="0 0 43 60" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M36 55.9999C36 58.209 29.0604 59.9999 20.5 59.9999C11.9396 59.9999 5 58.209 5 55.9999C5 53.7908 11.9396 51.9999 20.5 51.9999C29.0604 51.9999 36 53.7908 36 55.9999Z" fill="#202020" fill-opacity="0.33"/>
      <path d="M41.5182 24.5116C39.0182 34.5115 27.5182 48.6783 21.0182 56.0116C15.3515 49.0116 3.14974 34.5115 0.51819 24.5116C-1.98182 15.0115 4.51817 -0.48844 21.0182 0.0115599C37.5182 0.51156 44.0182 14.5117 41.5182 24.5116Z" fill="#00C7F4"/>
      <path d="M31 21.4999C31 27.2989 26.299 31.9999 20.5 31.9999C14.701 31.9999 10 27.2989 10 21.4999C10 15.7009 14.701 10.9999 20.5 10.9999C26.299 10.9999 31 15.7009 31 21.4999Z" fill="white"/>
    </svg>
  </div>`,
  iconSize: [43, 60],
  iconAnchor: [21.5, 60],
  popupAnchor: [0, -60],
  className: ''
});

// Map setups
let marker = L.marker(map.getCenter(), { icon: originIcon }).addTo(map);
let originMarkers = [];
let destinationMarkers = [];
let origins = [];
let destinations = [];
let currentStep = "origin";
let originCount = 1;
let destinationCount = 1;
let routeLayer = null;

map.on('move', () => marker.setLatLng(map.getCenter()));

async function getAddress(lat, lng) {
  const url = `https://api.neshan.org/v5/reverse?lat=${lat}&lng=${lng}`;
  try {
    const res = await fetch(url, { headers: { 'Api-Key': API_KEY_SERVICE } });
    const data = await res.json();
    return data.status === 'OK' ? data.formatted_address : 'آدرس نامشخص';
  } catch {
    return 'خطا در دریافت آدرس';
  }
}

// Show location list
async function updateLocationsList() {
  const listContainer = document.getElementById('locationsList');
  listContainer.innerHTML = '';

  let html = '<ul>';

  let originsData = [];
  let destinationsData = [];

  for (let i = 0; i < origins.length; i++) {
    const coords = origins[i];
    const address = await getAddress(coords.lat, coords.lng);

    html += `<li><span>مبدا ${i+1}</span>: ${address}</li>`;

    originsData.push({
      lat: coords.lat,
      lng: coords.lng,
      address: address
    });
  }

  for (let i = 0; i < destinations.length; i++) {
    const coords = destinations[i];
    const address = await getAddress(coords.lat, coords.lng);

    html += `<li><span>مقصد ${i+1}</span>: ${address}</li>`;

    destinationsData.push({
      lat: coords.lat,
      lng: coords.lng,
      address: address
    });
  }

  html += '</ul>';
  listContainer.innerHTML = html;

  document.getElementById('originsInput').value = JSON.stringify(originsData);
  document.getElementById('destinationsInput').value = JSON.stringify(destinationsData);
}


// Update buttons
function updateButtonsAndTitle() {
  const backBtn = document.getElementById('backBtn');
  const confirmBtn = document.getElementById('confirmBtn');
  backBtn.style.display = currentStep !== 'origin' || originCount > 1 ? 'block' : 'none';
  confirmBtn.style.display = currentStep === 'origin' && origins.length > 0 ? 'block' : 'none';
  document.getElementById('addBtn').innerText = currentStep === 'origin' ? 'افزودن مبدا' : 'افزودن مقصد';
  document.getElementById('calcBtn').style.display = currentStep === 'destination' && destinations.length > 0 ? 'block' : 'none';
}

// Add scale animation to Location icon
function animateMarkers(markers) {
  markers.forEach(m => {
    const el = m._icon.querySelector('.marker-wrapper');
    if(el){el.classList.add('marker-scale'); setTimeout(()=>el.classList.remove('marker-scale'),150);}
  });
}

// Is in special Locations
function isInSpecialArea(point) {
  for (const area of specialAreas) {
    const distance = map.distance(
      [area.center.lat, area.center.lng],
      [point.lat, point.lng]
    );
    if (distance <= area.radius) {
      return area.multiplier;
    }
  }
  return 1; // If not turn price to normal
}

// Draw path bettwen origin and destenation
async function drawRoute() {
  // Remove previous route if exists
  if (routeLayer) {
    map.removeLayer(routeLayer);
  }

  if (origins.length === 0 || destinations.length === 0) return;

  let points = [...origins, ...destinations];
  let routeCoordinates = [];

  // Get route for each segment
  for (let i = 0; i < points.length - 1; i++) {
    let from = points[i];
    let to = points[i+1];
    
    let url = `https://api.neshan.org/v4/direction?type=car&origin=${from.lat},${from.lng}&destination=${to.lat},${to.lng}`;
    try {
      let res = await fetch(url, {headers: {'Api-Key': API_KEY_SERVICE}});
      let data = await res.json();
      
      if (data.routes && data.routes[0]) {
        // Decode polyline string to coordinates
        let polyline = data.routes[0].overview_polyline.points;
        let decoded = L.PolylineUtil.decode(polyline);
        routeCoordinates = routeCoordinates.concat(decoded);
      }
    } catch (error) {
      console.error('Error fetching route:', error);
    }
  }

  // Draw the route on map
  if (routeCoordinates.length > 0) {
    routeLayer = L.polyline(routeCoordinates, {
      color: '#000000', // Color of path tracking
      weight: 5,
      opacity: 0.7,
      smoothFactor: 1
    }).addTo(map);
    
    // Fit map to route bounds
    map.fitBounds(routeLayer.getBounds());
  }
}

// Show special Locations in map
let areaLayers = [];

function drawSpecialAreas() {
  // Delete pre location
  areaLayers.forEach(layer => map.removeLayer(layer));
  areaLayers = [];
  
  // Draw new locations
  specialAreas.forEach(area => {
    let circle = L.circle([area.center.lat, area.center.lng], {
      radius: area.radius,
      color: '#ff000000',
      fillColor: '#f03', // Color of special locations
      fillOpacity: 0.2
    }).addTo(map);
    areaLayers.push(circle);
  });
}
drawSpecialAreas();

// Calculate Price function
async function calculatePrice() {
  let finalPrice = document.getElementById('finalPrice');
  let resultsDiv = document.getElementById('results'); 

  finalPrice.innerHTML = '<p>در حال محاسبه...</p>';
  
  let normalDistance = 0;
  let specialDistance = 0;
  let totalDuration = 0;
  let points = [...origins, ...destinations];

  // Calculate each route section
  for (let i = 0; i < points.length - 1; i++) {
    let from = points[i]; 
    let to = points[i+1];
    
    let url = `https://api.neshan.org/v4/direction?type=car&origin=${from.lat},${from.lng}&destination=${to.lat},${to.lng}`;

    try {
      let res = await fetch(url, {headers: {'Api-Key': API_KEY_SERVICE}});
      let data = await res.json();
      
      if (data.routes && data.routes[0]) {
        let polyline = data.routes[0].overview_polyline.points;
        let decoded = L.PolylineUtil.decode(polyline);
        
        for (let j = 0; j < decoded.length - 1; j++) {
          let p1 = { lat: decoded[j][0], lng: decoded[j][1] };
          let p2 = { lat: decoded[j+1][0], lng: decoded[j+1][1] };
          
          let dist = map.distance([p1.lat, p1.lng], [p2.lat, p2.lng]);

          let mid = {
            lat: (p1.lat + p2.lat) / 2,
            lng: (p1.lng + p2.lng) / 2
          };
          
          let multiplier = isInSpecialArea(mid);

          if (multiplier > 1) {
            specialDistance += dist / 1000;
          } else {
            normalDistance += dist / 1000;
          }
        }

        document.getElementById("normalDistanceInput").value = normalDistance.toFixed(2);
        document.getElementById("specialDistanceInput").value = specialDistance.toFixed(2);
        document.getElementById("totalDistanceInput").value = (normalDistance + specialDistance).toFixed(2);

        let leg = data.routes[0].legs[0];
        totalDuration += leg.duration.value / 60;
        document.getElementById('tripDuration').value = totalDuration.toFixed(1);
      }
    } catch (error) {
      console.error('Error calculating route:', error);
    }
  }


  const baseUrl = window.location.origin;
  let areaCoef = 1;

  try {
    let res = await fetch(`${baseUrl}/settings/tariffs`);
    let data = await res.json();
    areaCoef = Number(data.area_coef) || 1;
  } catch (err) {
    console.error("Error fetching area_coef:", err);
  }


  let carType = document.querySelector('input[name="car_type_id"]:checked');
  let pricePerKm = parseInt(carType.dataset.price);

  let normalPrice = normalDistance * pricePerKm;
  let specialPrice = specialDistance * pricePerKm * areaCoef;

  let totalPrice = normalPrice + specialPrice;

  // Round trip
  let tripType = document.getElementById('tripType').value;
  if (tripType === 'round') totalPrice *= 2;

  // Waiting time
  let waitingHours = parseInt(document.getElementById('waitingHours').value) || 0;
  let pricePerHour = parseInt(document.getElementById('waitingHours').dataset.price) || 0;
  totalPrice += waitingHours * pricePerHour;

  // Round to nearest 1000
  totalPrice = Math.ceil(totalPrice / 1000) * 1000;
  document.getElementById('hiddenTotalPrice').value = totalPrice;

  // Show results
  resultsDiv.innerHTML = `
    <p><strong>مسافت کل:</strong> ${(normalDistance + specialDistance).toFixed(2)} کیلومتر</p>
    <p><strong>زمان تخمینی:</strong> ${totalDuration.toFixed(1)} دقیقه</p>
  `;
  finalPrice.innerHTML = `<p><strong>هزینه کل:</strong> ${totalPrice.toLocaleString()} تومان</p>`;
}

// AJAX update on each change
document.getElementById('tripType').addEventListener('change', calculatePrice);
document.getElementById('waitingHours').addEventListener('change', calculatePrice);
document.querySelectorAll('input[name="car_type_id"]').forEach(radio => {
  radio.addEventListener('change', calculatePrice);
});

// Trip type
$(document).ready(function() {
  $('#tripType').select2({
    placeholder: "نوع سفر",
    minimumResultsForSearch: Infinity ,
  });
  
  $('#tripType').on('change', function() {
    calculatePrice();
  });
});

// Add locations ( origin and destination )
document.getElementById('addBtn').addEventListener('click', async () => {
  const pos = map.getCenter();
  if (currentStep === 'origin') {
    origins.push(pos);
    const newMarker = L.marker(pos, {icon: originIcon}).addTo(map).bindPopup(`مبدا ${originCount}`).openPopup();
    originMarkers.push(newMarker); 
    originCount++;
    animateMarkers(originMarkers);
  } else {
    destinations.push(pos);
    const newMarker = L.marker(pos, {icon: destinationIcon}).addTo(map).bindPopup(`مقصد ${destinationCount}`).openPopup();
    destinationMarkers.push(newMarker); 
    destinationCount++;
    animateMarkers(destinationMarkers);
  }
  updateButtonsAndTitle(); 
  await updateLocationsList();

  // Draw path after check point
  if ((currentStep === 'origin' && origins.length > 1) || 
      (currentStep === 'destination' && destinations.length > 0)) {
    await drawRoute();
  }
});

// Confirm Button
document.getElementById('confirmBtn').addEventListener('click', async () => {
  if (currentStep === 'origin' && origins.length > 0) {
    currentStep = 'destination'; 
    destinationCount = 1;
    marker.setIcon(destinationIcon);
    updateButtonsAndTitle(); 
    await updateLocationsList();
    animateMarkers(originMarkers);
    
    // Draw path if has at less one destination
    if (destinations.length > 0) {
      await drawRoute();
    }
  }
});

// Calculation button
document.getElementById('calcBtn').addEventListener('click', async () => {
  if (destinations.length === 0) {
    const pos = map.getCenter();
    destinations.push(pos);
    const newMarker = L.marker(pos, {icon: destinationIcon}).addTo(map).bindPopup(`مقصد ${destinationCount}`).openPopup();
    destinationMarkers.push(newMarker); 
    destinationCount++;
    updateButtonsAndTitle(); 
    await updateLocationsList();
  }
  animateMarkers(destinationMarkers);
  document.getElementById('popup').style.display = 'block';
  
  // Draw route and calculate price when popup opens
  // await drawRoute();
  await calculatePrice();
});

// Show FAQ popup
const faq = document.getElementById('faq');
document.getElementById('tripFAQ').addEventListener('click', async () => {
  faq.style.display = 'block';
});

document.getElementById('faqClose').addEventListener('click', async () => {
  faq.style.display = 'none';
});

window.addEventListener('click', (e)=> {
  if( e.target === faq) {
    faq.style.display = 'none';
  }
});

// Set Back button
document.getElementById('backBtn').addEventListener('click', async () => {
  if (currentStep === 'destination') {
    if (destinationCount > 1) { 
      destinations.pop(); 
      map.removeLayer(destinationMarkers.pop()); 
      destinationCount--; 
    } else { 
      currentStep = 'origin'; 
      destinations = []; 
      destinationMarkers.forEach(m => map.removeLayer(m)); 
      destinationMarkers = []; 
      destinationCount = 1; 
      marker.setIcon(originIcon);
    }
  } else if (currentStep === 'origin' && originCount > 1) { 
    origins.pop(); 
    map.removeLayer(originMarkers.pop()); 
    originCount--; 
  }
  
  // Redraw route if needed
  if (origins.length > 0 && destinations.length > 0) {
    await drawRoute();
  } else if (routeLayer) {
    map.removeLayer(routeLayer);
    routeLayer = null;
  }
  
  updateButtonsAndTitle(); 
  await updateLocationsList();
});


// Close button
window.closePopup = function() {
    const popup = document.getElementById('popup');
    if (popup) {
        popup.style.display = 'none';
    }
};


updateButtonsAndTitle();
updateLocationsList();
