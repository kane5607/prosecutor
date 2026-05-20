// ==========================================
// 1. MODAL LOGIC (Global & Specific)
// ==========================================
const fileCaseModal = document.getElementById("fileCaseModal");
const clearanceModal = document.getElementById("clearanceModal");
const viewModal = document.getElementById("viewModal");
const reminderModal = document.getElementById("reminderModal");

// Global click outside to close modals
window.addEventListener("click", function(event) {
    if (event.target == fileCaseModal) fileCaseModal.style.display = "none";
    if (event.target == clearanceModal) clearanceModal.style.display = "none";
    if (event.target == viewModal) viewModal.style.display = "none";
    if (event.target == reminderModal) closeReminderModal();
});

// File New Case Modal
const openBtn = document.querySelector(".btn-file");
const closeBtn = document.querySelector(".close-btn");

if (openBtn && fileCaseModal) {
    openBtn.onclick = function() {
        fileCaseModal.style.display = "block";
        document.body.style.overflow = "hidden";
    }
}
if (closeBtn && fileCaseModal) {
    closeBtn.onclick = function() {
        fileCaseModal.style.display = "none";
        document.body.style.overflow = "auto";
    }
}

// Clearance Modals
function openApplyModal() {
    if (clearanceModal) clearanceModal.style.display = "block";
}

function closeApplyModal() {
    if (clearanceModal) clearanceModal.style.display = "none";
}

function closeModal() {
    if (viewModal) viewModal.style.display = "none";
}

// ==========================================
// 2. CLEARANCE TABLE LOGIC
// ==========================================
function filterClearance() {
    var input = document.getElementById("clearanceSearch");
    if (!input) return; // Stop if not on clearance page

    var filter = input.value.toUpperCase();
    var select = document.getElementById("clearanceFilter");
    var columnIdx = select.value;
    var table = document.getElementById("clearanceTable");
    var tr = table.getElementsByTagName("tr");

    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName("td")[columnIdx];
        if (td) {
            var txtValue = td.textContent || td.innerText;
            tr[i].style.display = (txtValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
        }
    }
}

let currentActiveId = null;
function viewApplicant(name, age, address, type, id) {
    currentActiveId = id;
    document.getElementById("view-name").innerText = name;
    document.getElementById("view-age").innerText = age;
    document.getElementById("view-address").innerText = address;
    document.getElementById("view-type").innerText = type;
    if (viewModal) viewModal.style.display = "block";
}

function updateStatus() {
    const checkboxes = document.querySelectorAll('.req-check');
    const statusText = document.getElementById("modal-status");
    const tableStatus = document.getElementById("status-" + currentActiveId);
    
    let allChecked = Array.from(checkboxes).every(c => c.checked);

    if (allChecked) {
        statusText.innerText = "Complete";
        statusText.style.color = "green";
        tableStatus.innerText = "Complete";
        tableStatus.className = "status resolved"; 
    } else {
        statusText.innerText = "Not Complete";
        statusText.style.color = "red";
        tableStatus.innerText = "Not Complete";
        tableStatus.className = "status pending"; 
    }
}

// ==========================================
// 3. SETTINGS PAGE LOGIC (Clock, Profile, Calendar)
// ==========================================

// Digital Clock
function updateClock() {
    const clockElement = document.getElementById("digitalClock");
    if (!clockElement) return; // Stop if not on settings page

    const now = new Date();
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    clockElement.innerText = `${hours}:${minutes}:${seconds}`;
}
setInterval(updateClock, 1000);
updateClock();

// Profile Picture Preview
const fileInput = document.getElementById('fileInput');
const profileDisplay = document.getElementById('profileDisplay');
if (fileInput && profileDisplay) {
    fileInput.onchange = evt => {
        const [file] = fileInput.files;
        if (file) {
            profileDisplay.src = URL.createObjectURL(file);
        }
    }
}

// Calendar and Reminders
const daysContainer = document.getElementById("calendarDays");
const monthDisplay = document.getElementById("monthDisplay");
const prevBtn = document.getElementById("prevMonth");
const nextBtn = document.getElementById("nextMonth");
const reminderList = document.querySelector(".reminder-list");

let date = new Date();
let selectedFullDate = "";

if (daysContainer) {
    function renderCalendar() {
        daysContainer.innerHTML = "";
        const year = date.getFullYear();
        const month = date.getMonth();
        
        monthDisplay.innerText = new Intl.DateTimeFormat('en-US', { month: 'long', year: 'numeric' }).format(date);

        const firstDayIndex = new Date(year, month, 1).getDay();
        const lastDay = new Date(year, month + 1, 0).getDate();
        const prevLastDay = new Date(year, month, 0).getDate();

        for (let x = firstDayIndex; x > 0; x--) {
            const div = document.createElement("div");
            div.classList.add("calendar-day", "inactive");
            div.innerText = prevLastDay - x + 1;
            daysContainer.appendChild(div);
        }

        for (let i = 1; i <= lastDay; i++) {
            const div = document.createElement("div");
            div.classList.add("calendar-day");
            
            if (i === new Date().getDate() && month === new Date().getMonth() && year === new Date().getFullYear()) {
                div.classList.add("today");
            }
            div.innerText = i;
            div.onclick = () => {
                selectedFullDate = `${new Intl.DateTimeFormat('en-US', { month: 'short' }).format(date)} ${i}, ${year}`;
                openReminderModal(selectedFullDate);
            };
            daysContainer.appendChild(div);
        }
    }

    prevBtn.onclick = () => { date.setMonth(date.getMonth() - 1); renderCalendar(); };
    nextBtn.onclick = () => { date.setMonth(date.getMonth() + 1); renderCalendar(); };
    renderCalendar();
}

// Reminder Modal Logic
window.openReminderModal = function(dateText) {
    const rText = document.getElementById("selectedDateText");
    const rModal = document.getElementById("reminderModal");
    const rInput = document.getElementById("newReminderInput");
    
    if (rText && rModal && rInput) {
        rText.innerText = "For: " + dateText;
        rModal.style.display = "block";
        rInput.focus();
    }
};

window.closeReminderModal = function() {
    const modal = document.getElementById("reminderModal");
    const input = document.getElementById("newReminderInput");
    if (modal && input) {
        modal.style.display = "none";
        input.value = "";
    }
};

window.saveReminder = function() {
    const input = document.getElementById("newReminderInput");
    if (!input || !reminderList) return;

    const task = input.value;
    if (task.trim() !== "") {
        const li = document.createElement("li");
        li.innerHTML = `📌 <strong>${selectedFullDate}:</strong> ${task}`;
        reminderList.appendChild(li);
        closeReminderModal();
    }
};