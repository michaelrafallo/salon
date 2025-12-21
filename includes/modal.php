<!-- Modal Overlay -->
<div id="modalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-2 sm:p-4 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] transform transition-all duration-300 scale-95" onclick="event.stopPropagation()" id="modalContainer">
        <div id="modalContent">
            <!-- Modal content will be inserted here -->
        </div>
    </div>
</div>

<!-- Nested Modal Overlay (for modals on top of other modals) -->
<div id="nestedModalOverlay" class="fixed inset-0 bg-black bg-opacity-50 z-[60] hidden flex items-center justify-center p-2 sm:p-4 backdrop-blur-sm transition-opacity duration-300">
    <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] transform transition-all duration-300 scale-95" onclick="event.stopPropagation()" id="nestedModalContainer">
        <div id="nestedModalContent">
            <!-- Nested modal content will be inserted here -->
        </div>
    </div>
</div>

<script>
function openModal(content, size = 'default', closeOnOutsideClick = true) {
    const overlay = document.getElementById('modalOverlay');
    const modalContent = document.getElementById('modalContent');
    const modalContainer = document.getElementById('modalContainer');
    
    // Reset classes
    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] transform transition-all duration-300 scale-95';
    
    // Apply size classes
    if (size === 'large') {
        modalContainer.classList.add('max-w-6xl');
    } else if (size === 'xl') {
        modalContainer.classList.add('max-w-7xl');
    } else if (size === 'medium') {
        modalContainer.classList.add('max-w-4xl');
    } else {
        modalContainer.classList.add('max-w-2xl');
    }
    
    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Set or remove onclick handler for outside click
    if (closeOnOutsideClick) {
        overlay.onclick = closeModal;
    } else {
        overlay.onclick = null;
    }
    
    // Animate modal in
    setTimeout(() => {
        modalContainer.classList.remove('scale-95');
        modalContainer.classList.add('scale-100');
    }, 10);
}

function closeModal() {
    const overlay = document.getElementById('modalOverlay');
    const modalContainer = document.getElementById('modalContainer');
    
    if (modalContainer) {
        modalContainer.classList.remove('scale-100');
        modalContainer.classList.add('scale-95');
    }
    
    setTimeout(() => {
        overlay.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }, 200);
}

// Open nested modal (appears on top of existing modal)
function openNestedModal(content, size = 'default', closeOnOutsideClick = true) {
    const overlay = document.getElementById('nestedModalOverlay');
    const modalContent = document.getElementById('nestedModalContent');
    const modalContainer = document.getElementById('nestedModalContainer');
    
    // Reset classes
    modalContainer.className = 'bg-white rounded-2xl shadow-2xl w-full max-h-[90vh] transform transition-all duration-300 scale-95';
    
    // Apply size classes
    if (size === 'large') {
        modalContainer.classList.add('max-w-6xl');
    } else if (size === 'xl') {
        modalContainer.classList.add('max-w-7xl');
    } else if (size === 'medium') {
        modalContainer.classList.add('max-w-4xl');
    } else {
        modalContainer.classList.add('max-w-2xl');
    }
    
    modalContent.innerHTML = content;
    overlay.classList.remove('hidden');
    
    // Set or remove onclick handler for outside click
    if (closeOnOutsideClick) {
        overlay.onclick = closeNestedModal;
    } else {
        overlay.onclick = null;
    }
    
    // Animate modal in
    setTimeout(() => {
        modalContainer.classList.remove('scale-95');
        modalContainer.classList.add('scale-100');
    }, 10);
}

function closeNestedModal() {
    const overlay = document.getElementById('nestedModalOverlay');
    const modalContainer = document.getElementById('nestedModalContainer');
    
    if (modalContainer) {
        modalContainer.classList.remove('scale-100');
        modalContainer.classList.add('scale-95');
    }
    
    setTimeout(() => {
        overlay.classList.add('hidden');
    }, 200);
}

// Close modal on Escape key (close nested modal first if open, then main modal)
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const nestedOverlay = document.getElementById('nestedModalOverlay');
        if (nestedOverlay && !nestedOverlay.classList.contains('hidden')) {
            closeNestedModal();
        } else {
            closeModal();
        }
    }
});
</script>

