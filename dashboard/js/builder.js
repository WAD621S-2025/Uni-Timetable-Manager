// Schedule Builder functionality for Modules
document.addEventListener('DOMContentLoaded', function() {
    initializeBuilder();
    loadUserData();
    loadUserModules();
    generateTimetableBuilder();
    loadSavedSchedule();
});

function initializeBuilder() {
    // Check authentication
    const isLoggedIn = localStorage.getItem('isLoggedIn');
    if (isLoggedIn !== 'true') {
        window.location.href = '../login/login.html';
        return;
    }

    // Initialize drag and drop
    initializeDragAndDrop();
    
    // Set up modal
    setupModal();
}

function loadUserData() {
    const userData = JSON.parse(localStorage.getItem('currentUser') || '{}');
    const userNameElement = document.getElementById('userName');
    
    if (userNameElement && userData.firstName) {
        userNameElement.textContent = userData.firstName;
    }
}

function loadUserModules() {
    const moduleList = document.getElementById('moduleList');
    const savedModules = localStorage.getItem('userModules');
    
    if (savedModules) {
        const modules = JSON.parse(savedModules);
        displayModules(modules);
    } else {
        moduleList.innerHTML = '<div class="no-modules">No modules added yet. Add your first module above.</div>';
    }
}

function displayModules(modules) {
    const moduleList = document.getElementById('moduleList');
    
    if (modules.length === 0) {
        moduleList.innerHTML = '<div class="no-modules">No modules added yet. Add your first module above.</div>';
        return;
    }
    
    moduleList.innerHTML = '';
    
    modules.forEach((module, index) => {
        const moduleItem = document.createElement('div');
        moduleItem.className = 'module-item';
        moduleItem.draggable = true;
        moduleItem.setAttribute('data-module-code', module.code);
        moduleItem.setAttribute('data-module-name', module.name);
        moduleItem.setAttribute('data-module-type', module.type);
        moduleItem.setAttribute('data-module-color', module.color || '#0074D9');
        
        moduleItem.innerHTML = `
            <span class="module-code">${module.code}</span>
            <span class="module-name">${module.name}</span>
            <span class="module-type">${module.type}</span>
            <button class="remove-module" onclick="removeModule(${index})">×</button>
        `;
        
        moduleItem.addEventListener('dragstart', handleDragStart);
        moduleList.appendChild(moduleItem);
    });
}

function addModule() {
    const codeInput = document.getElementById('moduleCode');
    const nameInput = document.getElementById('moduleName');
    const typeInput = document.getElementById('moduleType');
    
    const code = codeInput.value.trim().toUpperCase();
    const name = nameInput.value.trim();
    const type = typeInput.value;
    
    if (!code || !name) {
        alert('Please enter both module code and name');
        return;
    }
    
    // Get existing modules
    const savedModules = localStorage.getItem('userModules');
    const modules = savedModules ? JSON.parse(savedModules) : [];
    
    // Check if module already exists
    const existingModule = modules.find(m => m.code === code);
    if (existingModule) {
        alert('Module with this code already exists!');
        return;
    }
    
    // Add new module
    const colorPicker = document.getElementById('moduleColor');
    const newModule = {
        code: code,
        name: name,
        type: type,
        color: colorPicker.value
    };
    
    modules.push(newModule);
    
    // Save to localStorage
    localStorage.setItem('userModules', JSON.stringify(modules));
    
    // Update display
    displayModules(modules);
    
    // Clear inputs
    codeInput.value = '';
    nameInput.value = '';
    
    alert('Module added successfully!');
}

function removeModule(index) {
    if (confirm('Are you sure you want to remove this module?')) {
        const savedModules = localStorage.getItem('userModules');
        const modules = savedModules ? JSON.parse(savedModules) : [];
        
        // Get the module code before removing
        const moduleCode = modules[index]?.code;
        
        modules.splice(index, 1);
        
        // Save updated modules
        localStorage.setItem('userModules', JSON.stringify(modules));
        
        // Update display
        displayModules(modules);
        
        // Also remove any scheduled instances of this module
        removeScheduledModule(moduleCode);
    }
}

function removeScheduledModule(moduleCode) {
    const scheduledClasses = document.querySelectorAll('.scheduled-class');
    scheduledClasses.forEach(classElement => {
        if (classElement.getAttribute('data-module-code') === moduleCode) {
            classElement.remove();
        }
    });
    updateScheduleSummary();
}

function generateTimetableBuilder() {
    const timetableBuilder = document.getElementById('timetableBuilder');
    
    const timeSlots = [
        '8:00', '9:00', '10:00', '11:00', '12:00', 
        '13:00', '14:00', '15:00', '16:00', '17:00'
    ];

    const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

    timetableBuilder.innerHTML = '';

    timeSlots.forEach(time => {
        // Time slot
        const timeSlot = document.createElement('div');
        timeSlot.className = 'time-slot-builder';
        timeSlot.textContent = time;
        timeSlot.setAttribute('data-time', time);
        timetableBuilder.appendChild(timeSlot);

        // Day slots for this time
        days.forEach(day => {
            const daySlot = document.createElement('div');
            daySlot.className = 'day-slot-builder';
            daySlot.setAttribute('data-time', time);
            daySlot.setAttribute('data-day', day);
            
            // Drag and drop events
            daySlot.addEventListener('dragover', handleDragOver);
            daySlot.addEventListener('dragenter', handleDragEnter);
            daySlot.addEventListener('dragleave', handleDragLeave);
            daySlot.addEventListener('drop', handleDrop);
            
            timetableBuilder.appendChild(daySlot);
        });
    });
}

function initializeDragAndDrop() {
    // Prevent default drag behaviors
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    
    document.addEventListener('drop', function(e) {
        e.preventDefault();
    });
}

function handleDragStart(e) {
    const moduleCode = e.target.getAttribute('data-module-code');
    const moduleName = e.target.getAttribute('data-module-name');
    const moduleType = e.target.getAttribute('data-module-type');
    const moduleColor = e.target.getAttribute('data-module-color');
    
    e.dataTransfer.setData('text/plain', JSON.stringify({
        code: moduleCode,
        name: moduleName,
        type: moduleType,
        color: moduleColor
    }));
    
    e.target.classList.add('dragging');
}

function handleDragOver(e) {
    e.preventDefault();
}

function handleDragEnter(e) {
    e.preventDefault();
    if (e.target.classList.contains('day-slot-builder')) {
        e.target.classList.add('drop-zone');
    }
}

function handleDragLeave(e) {
    if (e.target.classList.contains('day-slot-builder')) {
        e.target.classList.remove('drop-zone');
    }
}

function handleDrop(e) {
    e.preventDefault();
    
    if (e.target.classList.contains('day-slot-builder')) {
        e.target.classList.remove('drop-zone');
        
        const moduleData = JSON.parse(e.dataTransfer.getData('text/plain'));
        const time = e.target.getAttribute('data-time');
        const day = e.target.getAttribute('data-day');
        
        addModuleToSchedule(moduleData, time, day, e.target);
        
        // Remove dragging class from module item
        document.querySelectorAll('.module-item.dragging').forEach(item => {
            item.classList.remove('dragging');
        });
    }
}

function addModuleToSchedule(moduleData, time, day, daySlot) {
    // Check if this time slot already has a module
    const existingClass = daySlot.querySelector('.scheduled-class');
    if (existingClass) {
        if (!confirm('This time slot already has a module. Replace it?')) {
            return;
        }
        existingClass.remove();
    }
    
    const colorPicker = document.getElementById('moduleColor');
    const moduleColor = colorPicker.value;
    
    const scheduledClass = document.createElement('div');
    scheduledClass.className = 'scheduled-class';
    scheduledClass.style.backgroundColor = moduleColor;
    scheduledClass.style.borderLeftColor = moduleColor;
    
    scheduledClass.innerHTML = `
        <span class="module-code">${moduleData.code}</span>
        <span class="module-time">${time}</span>
        <span class="module-type">${moduleData.type}</span>
        <button class="remove-btn" onclick="removeScheduledClass(this)">×</button>
    `;
    
    scheduledClass.setAttribute('data-module-code', moduleData.code);
    scheduledClass.setAttribute('data-module-name', moduleData.name);
    scheduledClass.setAttribute('data-module-type', moduleData.type);
    scheduledClass.setAttribute('data-time', time);
    scheduledClass.setAttribute('data-day', day);
    scheduledClass.setAttribute('data-color', moduleColor);
    
    daySlot.appendChild(scheduledClass);
    updateScheduleSummary();
}

function removeScheduledClass(button) {
    const scheduledClass = button.closest('.scheduled-class');
    if (scheduledClass) {
        scheduledClass.remove();
        updateScheduleSummary();
    }
}

function updateScheduleSummary() {
    const summaryList = document.getElementById('scheduleSummary');
    const scheduledClasses = document.querySelectorAll('.scheduled-class');
    
    summaryList.innerHTML = '';
    
    let totalHours = 0;
    const scheduleItems = [];
    
    scheduledClasses.forEach(classElement => {
        const code = classElement.getAttribute('data-module-code');
        const name = classElement.getAttribute('data-module-name');
        const type = classElement.getAttribute('data-module-type');
        const time = classElement.getAttribute('data-time');
        const day = classElement.getAttribute('data-day');
        const color = classElement.getAttribute('data-color');
        
        scheduleItems.push({ code, name, type, time, day, color });
        totalHours++;
        
        const summaryItem = document.createElement('div');
        summaryItem.className = 'summary-item';
        summaryItem.style.borderLeftColor = color;
        
        summaryItem.innerHTML = `
            <div class="module-info">
                <span class="code">${code} - ${type}</span>
                <span class="details">${day} at ${time}</span>
            </div>
            <button class="remove-summary" onclick="removeScheduledClassFromSummary('${code}', '${time}', '${day}')">Remove</button>
        `;
        
        summaryList.appendChild(summaryItem);
    });
    
    // Update statistics
    document.getElementById('totalModulesCount').textContent = scheduleItems.length;
    document.getElementById('weeklyHoursCount').textContent = totalHours;
    
    // Save to localStorage
    localStorage.setItem('savedSchedule', JSON.stringify(scheduleItems));
}

function removeScheduledClassFromSummary(code, time, day) {
    const classElement = document.querySelector(`.scheduled-class[data-module-code="${code}"][data-time="${time}"][data-day="${day}"]`);
    if (classElement) {
        classElement.remove();
        updateScheduleSummary();
    }
}

function clearSchedule() {
    if (confirm('Are you sure you want to clear your entire schedule?')) {
        document.querySelectorAll('.scheduled-class').forEach(classElement => {
            classElement.remove();
        });
        updateScheduleSummary();
    }
}

function checkConflicts() {
    const scheduledClasses = document.querySelectorAll('.scheduled-class');
    const conflicts = [];
    
    // Simple conflict detection: same time slot, different modules
    const timeSlots = {};
    
    scheduledClasses.forEach(classElement => {
        const time = classElement.getAttribute('data-time');
        const day = classElement.getAttribute('data-day');
        const code = classElement.getAttribute('data-module-code');
        
        const slotKey = `${day}-${time}`;
        
        if (!timeSlots[slotKey]) {
            timeSlots[slotKey] = [];
        }
        
        timeSlots[slotKey].push(code);
    });
    
    // Check for conflicts
    Object.keys(timeSlots).forEach(slotKey => {
        if (timeSlots[slotKey].length > 1) {
            conflicts.push({
                slot: slotKey,
                modules: timeSlots[slotKey]
            });
        }
    });
    
    // Update conflicts count
    document.getElementById('conflictsCount').textContent = conflicts.length;
    
    // Show conflicts in modal
    if (conflicts.length > 0) {
        showConflicts(conflicts);
    } else {
        alert(' No schedule conflicts detected!');
    }
}

function showConflicts(conflicts) {
    const conflictList = document.getElementById('conflictList');
    const modal = document.getElementById('conflictModal');
    
    conflictList.innerHTML = '';
    
    conflicts.forEach(conflict => {
        const [day, time] = conflict.slot.split('-');
        const conflictItem = document.createElement('div');
        conflictItem.className = 'conflict-item';
        conflictItem.innerHTML = `
            <p><strong>${day} at ${time}:</strong></p>
            <p>Multiple modules scheduled: ${conflict.modules.join(', ')}</p>
        `;
        conflictList.appendChild(conflictItem);
    });
    
    modal.style.display = 'block';
}

function setupModal() {
    const modal = document.getElementById('conflictModal');
    const closeBtn = document.querySelector('.close');
    
    closeBtn.onclick = function() {
        modal.style.display = 'none';
    }
    
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
}

function closeConflictModal() {
    document.getElementById('conflictModal').style.display = 'none';
}

async function saveSchedule() {
    const scheduledClasses = document.querySelectorAll('.scheduled-class');
    
    if (scheduledClasses.length === 0) {
        alert('Please add some modules to your schedule before saving.');
        return;
    }
    
    const scheduleData = [];
    
    scheduledClasses.forEach(classElement => {
        const slot = classElement.closest('.day-slot-builder');
        scheduleData.push({
            code: classElement.getAttribute('data-module-code'),
            name: classElement.getAttribute('data-module-name'),
            type: classElement.getAttribute('data-module-type'),
            time: slot.getAttribute('data-time'),
            day: slot.getAttribute('data-day'),
            color: classElement.getAttribute('data-color') || '#0074D9'
        });
    });
    
    try {
        const response = await fetch('schedule-builder.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify(scheduleData)
        });
        
        // Also save to localStorage as backup
        localStorage.setItem('savedSchedule', JSON.stringify(scheduleData));
        
        alert(' Schedule saved successfully!');
    } catch (error) {
        console.error('Save failed:', error);
        // Fallback to localStorage
        localStorage.setItem('savedSchedule', JSON.stringify(scheduleData));
        alert(' Saved locally (server unavailable)');
    }
}

function loadSavedSchedule() {
    const savedSchedule = localStorage.getItem('savedSchedule');
    
    if (savedSchedule) {
        const scheduleData = JSON.parse(savedSchedule);
        
        scheduleData.forEach(module => {
            const daySlot = document.querySelector(`.day-slot-builder[data-time="${module.time}"][data-day="${module.day}"]`);
            if (daySlot) {
                const scheduledClass = document.createElement('div');
                scheduledClass.className = 'scheduled-class';
                scheduledClass.style.backgroundColor = module.color;
                scheduledClass.style.borderLeftColor = module.color;
                
                scheduledClass.innerHTML = `
                    <span class="module-code">${module.code}</span>
                    <span class="module-time">${module.time}</span>
                    <span class="module-type">${module.type}</span>
                    <button class="remove-btn" onclick="removeScheduledClass(this)">×</button>
                `;
                
                scheduledClass.setAttribute('data-module-code', module.code);
                scheduledClass.setAttribute('data-module-name', module.name);
                scheduledClass.setAttribute('data-module-type', module.type);
                scheduledClass.setAttribute('data-time', module.time);
                scheduledClass.setAttribute('data-day', module.day);
                scheduledClass.setAttribute('data-color', module.color);
                
                daySlot.appendChild(scheduledClass);
            }
        });
        
        updateScheduleSummary();
    }
}