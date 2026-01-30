{{-- Modal Overlay (matches original: semi-transparent overlay, centered white box, scale animation) --}}
<div id="modalOverlay" class="fixed inset-0 z-50 p-2 sm:p-4 transition-opacity duration-300" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;" data-modal-overlay>
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full transform transition-all duration-300 relative z-10" style="transform: scale(0.95);" onclick="event.stopPropagation()" id="modalContainer">
        <div id="modalContent" class="p-0">
            {{-- Modal content will be inserted here via JS --}}
        </div>
    </div>
</div>

{{-- Add Customer Modal Overlay (used by waiting-list and others) --}}
<div id="addCustomerModalOverlay" class="fixed inset-0 z-[60] p-4" style="background-color: rgba(0,0,0,0.5); display: none; align-items: center; justify-content: center;" onclick="typeof closeAddCustomerModal === 'function' && closeAddCustomerModal()">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto" onclick="event.stopPropagation()">
        <div id="addCustomerModalContent">
            {{-- Add Customer modal content will be inserted here --}}
        </div>
    </div>
</div>

{{-- Nested Modal Overlay --}}
<div id="nestedModalOverlay" class="fixed inset-0 z-[60] p-2 sm:p-4 transition-opacity duration-300" style="background-color: rgba(0,0,0,0.5); backdrop-filter: blur(4px); display: none; align-items: center; justify-content: center;" data-modal-overlay>
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-hidden transform transition-all duration-300 relative z-10" style="transform: scale(0.95);" onclick="event.stopPropagation()" id="nestedModalContainer">
        <div id="nestedModalContent" class="overflow-y-auto max-h-[90vh] p-0">
            {{-- Nested modal content will be inserted here via JS --}}
        </div>
    </div>
</div>

<script>
function openModal(content, size = 'default', closeOnOutsideClick = true) {
    const overlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalContainer = document.getElementById('modalContainer');

    if (!overlay || !modalContent || !modalContainer) return;

    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full transform transition-all duration-300 relative z-10';
    modalContainer.style.transform = 'scale(0.95)';
    modalContent.style.overflowY = '';
    modalContent.style.maxHeight = '';
    if (size === 'large') {
        modalContainer.classList.add('max-w-6xl');
        modalContainer.style.maxHeight = '95vh';
        modalContent.style.maxHeight = '95vh';
        modalContent.style.overflowY = 'auto';
    } else if (size === 'xl') {
        modalContainer.classList.add('max-w-7xl');
        modalContainer.style.maxHeight = '95vh';
        modalContent.style.maxHeight = '95vh';
        modalContent.style.overflowY = 'auto';
    } else if (size === 'medium') {
        modalContainer.classList.add('max-w-4xl');
    } else {
        modalContainer.classList.add('max-w-2xl');
    }

    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';
    overlay.onclick = closeOnOutsideClick ? closeModal : null;

    setTimeout(() => {
        modalContainer.style.transform = 'scale(1)';
    }, 10);
}

function closeModal() {
    const overlay = document.getElementById('modalOverlay');
    const modalContainer = document.getElementById('modalContainer');
    if (modalContainer) {
        modalContainer.style.transform = 'scale(0.95)';
    }
    setTimeout(() => {
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.style.display = 'none';
        }
        document.body.style.overflow = 'auto';
    }, 200);
}

function openNestedModal(content, size = 'default', closeOnOutsideClick = true) {
    const overlay = document.getElementById('nestedModalOverlay');
    const modalContent = document.getElementById('nestedModalContent');
    const modalContainer = document.getElementById('nestedModalContainer');

    if (!overlay || !modalContent || !modalContainer) return;

    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] overflow-hidden transform transition-all duration-300 relative z-10';
    modalContainer.style.transform = 'scale(0.95)';
    if (size === 'large') modalContainer.classList.add('max-w-6xl');
    else if (size === 'xl') modalContainer.classList.add('max-w-7xl');
    else if (size === 'medium') modalContainer.classList.add('max-w-4xl');
    else modalContainer.classList.add('max-w-2xl');

    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    overlay.onclick = closeOnOutsideClick ? closeNestedModal : null;

    setTimeout(() => {
        modalContainer.style.transform = 'scale(1)';
    }, 10);
}

function closeNestedModal() {
    const overlay = document.getElementById('nestedModalOverlay');
    const modalContainer = document.getElementById('nestedModalContainer');
    if (modalContainer) {
        modalContainer.style.transform = 'scale(0.95)';
    }
    setTimeout(() => {
        if (overlay) {
            overlay.classList.add('hidden');
            overlay.style.display = 'none';
        }
    }, 200);
}

// Helper functions for addCustomerModalOverlay (used by waiting-list)
window.openAddCustomerModal = function(content) {
    const overlay = document.getElementById('addCustomerModalOverlay');
    const modalContent = document.getElementById('addCustomerModalContent');
    if (!overlay || !modalContent) return;
    if (content) modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    overlay.style.display = 'flex';
    overlay.style.alignItems = 'center';
    overlay.style.justifyContent = 'center';
    document.body.style.overflow = 'hidden';
};

window.closeAddCustomerModal = function() {
    const overlay = document.getElementById('addCustomerModalOverlay');
    if (overlay) {
        overlay.classList.add('hidden');
        overlay.style.display = 'none';
    }
    document.body.style.overflow = 'auto';
};

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const addCustomerOverlay = document.getElementById('addCustomerModalOverlay');
        const nestedOverlay = document.getElementById('nestedModalOverlay');
        if (addCustomerOverlay && addCustomerOverlay.style.display !== 'none' && !addCustomerOverlay.classList.contains('hidden')) {
            window.closeAddCustomerModal();
        } else if (nestedOverlay && nestedOverlay.style.display !== 'none' && !nestedOverlay.classList.contains('hidden')) {
            closeNestedModal();
        } else {
            closeModal();
        }
    }
});

function showSuccessMessage(message) {
    const el = document.createElement('div');
    el.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-[70]';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(function() { el.remove(); }, 3000);
}

function showErrorMessage(message) {
    const el = document.createElement('div');
    el.className = 'fixed top-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-[70]';
    el.textContent = message;
    document.body.appendChild(el);
    setTimeout(function() { el.remove(); }, 3000);
}
</script>
