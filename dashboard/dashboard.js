// Dashboard functionality for Campus Connect
document.addEventListener('DOMContentLoaded', function() {
    initializeDashboard();
    loadUserData();
    generateTimetable();
    loadSavedSchedule(); // Add this line
});

function initializeDashboard() {
    // Check authentication
    const isLoggedIn = localStorage.getItem('isLoggedIn');
    if (isLoggedIn !== 'true') {
        window.location.href = 'login.html';
        return;
    }

    // Set up view controls
    setupViewControls();
}

function loadUserData() {
    const userData = JSON.parse(localStorage.getItem('currentUser') || '{}');
    const userNameElement = document.getElementById('userName');
    
    if (userNameElement && userData.firstName) {
        userNameElement.textContent = userData.firstName;
    }
}

function generateTimetable() {
    const timetableElement = document.getElementById('timetable');
    if (!timetableElement) return;

    // Clear existing content
    timetableElement.innerHTML = '';

    // Time slots
    const timeSlots = [
        '8:00', '9:00', '10:00', '11:00', '12:00', 
        '13:00', '14:00', '15:00', '16:00', '17:00'
    ];

    // Days of the week
    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    // Create header
    const headerRow = document.createElement('div');
    headerRow.className = 'timetable-header';
    
    // Time column header
    const timeHeader = document.createElement('div');
    timeHeader.textContent = 'Time';
    timeHeader.style.gridColumn = '1';
    timeHeader.style.gridRow = '1';
    headerRow.appendChild(timeHeader);

    // Day headers
    days.forEach((day, index) => {
        const dayHeader = document.createElement('div');
        dayHeader.textContent = day;
        dayHeader.style.gridColumn = (index + 2).toString();
        dayHeader.style.gridRow = '1';
        headerRow.appendChild(dayHeader);
    });

    timetableElement.appendChild(headerRow);

    // Create time slots and day cells
    timeSlots.forEach((time, timeIndex) => {
        const row = timeIndex + 2; // +2 because header is row 1

        // Time slot
        const timeCell = document.createElement('div');
        timeCell.className = 'time-slot';
        timeCell.textContent = time;
        timeCell.style.gridColumn = '1';
        timeCell.style.gridRow = row.toString();
        timetableElement.appendChild(timeCell);

        // Day cells
        days.forEach((day, dayIndex) => {
            const dayCell = document.createElement('div');
            dayCell.className = 'day-slot';
            dayCell.setAttribute('data-time', time);
            dayCell.setAttribute('data-day', day);
            dayCell.style.gridColumn = (dayIndex + 2).toString();
            dayCell.style.gridRow = row.toString();

            // Load saved classes for this time slot
            loadSavedClasses(dayCell, time, day);

            timetableElement.appendChild(dayCell);
        });
    });
}

function loadSavedClasses(dayCell, time, day) {
    const savedSchedule = localStorage.getItem('savedSchedule');
    
    if (savedSchedule) {
        const scheduleData = JSON.parse(savedSchedule);
        
        // Find if there's a class at this specific time and day
        const classAtSlot = scheduleData.find(cls => 
            cls.time === time && cls.day === day
        );
        
        if (classAtSlot) {
            const classEvent = document.createElement('div');
            classEvent.className = 'class-event';
            classEvent.innerHTML = `
                <span class="course-name">${classAtSlot.code}</span>
                <span class="course-details">${classAtSlot.type} - ${classAtSlot.time}</span>
            `;
            classEvent.style.backgroundColor = classAtSlot.color;
            classEvent.style.borderLeftColor = classAtSlot.color;
            
            classEvent.addEventListener('click', () => {
                showClassDetails(classAtSlot);
            });

            dayCell.appendChild(classEvent);
        }
    }
}

function loadSavedSchedule() {
    const savedSchedule = localStorage.getItem('savedSchedule');
    
    if (savedSchedule) {
        const scheduleData = JSON.parse(savedSchedule);
        updateStats(scheduleData);
        loadUpcomingClasses(scheduleData);
    } else {
        updateStats([]);
        loadUpcomingClasses([]);
    }
}

function updateStats(scheduleData) {
    // Update statistics based on saved schedule
    const totalModules = scheduleData.length;
    const weeklyHours = scheduleData.length; // Each scheduled item counts as 1 hour
    
    // Calculate next class
    const now = new Date();
    const currentTime = now.getHours() + ':' + (now.getMinutes() < 10 ? '0' : '') + now.getMinutes();
    
    let nextClass = '--:--';
    const today = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][now.getDay()];
    
    // Find the next class today
    const todayClasses = scheduleData.filter(cls => cls.day === today);
    const futureClasses = todayClasses.filter(cls => {
        const [classHour, classMinute] = cls.time.split(':').map(Number);
        const classTime = classHour * 60 + classMinute;
        const currentTimeInMinutes = now.getHours() * 60 + now.getMinutes();
        return classTime > currentTimeInMinutes;
    });
    
    if (futureClasses.length > 0) {
        nextClass = futureClasses[0].time;
    }

    document.getElementById('totalCourses').textContent = totalModules;
    document.getElementById('weeklyHours').textContent = weeklyHours;
    document.getElementById('completedClasses').textContent = todayClasses.length;
    document.getElementById('nextClass').textContent = nextClass;
}

function loadUpcomingClasses(scheduleData) {
    const upcomingList = document.getElementById('upcomingList');
    if (!upcomingList) return;

    // Get today's day
    const today = new Date();
    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    const todayName = days[today.getDay()];

    // Filter classes for today and tomorrow
    const todayClasses = scheduleData.filter(cls => cls.day === todayName);
    const tomorrowClasses = scheduleData.filter(cls => {
        const tomorrowIndex = (today.getDay() + 1) % 7;
        return cls.day === days[tomorrowIndex];
    });

    // Sort classes by time
    todayClasses.sort((a, b) => a.time.localeCompare(b.time));
    tomorrowClasses.sort((a, b) => a.time.localeCompare(b.time));

    upcomingList.innerHTML = '';

    // Add today's classes
    todayClasses.forEach(classInfo => {
        const item = document.createElement('div');
        item.className = 'upcoming-item';
        item.innerHTML = `
            <div class="upcoming-info">
                <h4>${classInfo.code} - ${classInfo.type}</h4>
                <p>Today at ${classInfo.time}</p>
            </div>
            <div class="upcoming-time">${classInfo.time}</div>
        `;
        upcomingList.appendChild(item);
    });

    // Add tomorrow's classes if no classes today
    if (todayClasses.length === 0 && tomorrowClasses.length > 0) {
        tomorrowClasses.forEach(classInfo => {
            const item = document.createElement('div');
            item.className = 'upcoming-item';
            item.innerHTML = `
                <div class="upcoming-info">
                    <h4>${classInfo.code} - ${classInfo.type}</h4>
                    <p>Tomorrow at ${classInfo.time}</p>
                </div>
                <div class="upcoming-time">${classInfo.time}</div>
            `;
            upcomingList.appendChild(item);
        });
    }

    // If no classes at all
    if (todayClasses.length === 0 && tomorrowClasses.length === 0) {
        const noClasses = document.createElement('div');
        noClasses.className = 'upcoming-item';
        noClasses.innerHTML = `
            <div class="upcoming-info">
                <h4>No upcoming classes</h4>
                <p>Add classes to your schedule in the Schedule Builder</p>
            </div>
        `;
        upcomingList.appendChild(noClasses);
    }
}

function showClassDetails(classInfo) {
    alert(`Module: ${classInfo.code}\nType: ${classInfo.type}\nTime: ${classInfo.time}\nDay: ${classInfo.day}`);
}

function setupViewControls() {
    const viewButtons = document.querySelectorAll('.view-controls .btn');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('btn-outline')) {
                // Remove active class from all outline buttons
                document.querySelectorAll('.view-controls .btn-outline').forEach(btn => {
                    btn.classList.remove('active');
                });
                // Add active class to clicked button
                this.classList.add('active');
                
                const view = this.getAttribute('data-view');
                switchView(view);
            }
        });
    });
}

function switchView(view) {
    // In a real application, this would switch between different timetable views
    console.log('Switching to', view, 'view');
    // For now, we'll just show an alert
    if (view === 'day') {
        alert('Day view would show detailed schedule for a single day');
    } else if (view === 'week') {
        alert('Week view shows the complete weekly schedule');
    }
}

function printTimetable() {
    window.print();
}

function exportTimetable() {
    const savedSchedule = localStorage.getItem('savedSchedule');
    let timetableData;
    
    if (savedSchedule) {
        timetableData = {
            title: 'Campus Connect Timetable',
            generated: new Date().toISOString(),
            schedule: JSON.parse(savedSchedule)
        };
    } else {
        timetableData = {
            title: 'Campus Connect Timetable',
            generated: new Date().toISOString(),
            schedule: []
        };
    }
    
    const dataStr = JSON.stringify(timetableData, null, 2);
    const dataBlob = new Blob([dataStr], { type: 'application/json' });
    
    const link = document.createElement('a');
    link.href = URL.createObjectURL(dataBlob);
    link.download = 'campus-connect-timetable.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    alert('Timetable exported successfully!');
}