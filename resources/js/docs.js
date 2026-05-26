// Vibe UI - Documentation Interactivity
// Handles features dedicated to the development documentation pages

document.addEventListener('click', async (e) => {
    // Event Delegation for Copy Code buttons
    const copyBtn = e.target.closest('.copy-btn');
    if (!copyBtn) return;
    
    const container = copyBtn.closest('.code-container');
    if (!container) return;
    
    const codeElement = container.querySelector('code');
    if (!codeElement) return;

    const code = codeElement.innerText;
    
    try {
        await navigator.clipboard.writeText(code);
        
        // Visual Feedback
        const originalContent = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="bx bx-check text-green-500"></i> Copied!';
        copyBtn.classList.add('pointer-events-none');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalContent;
            copyBtn.classList.remove('pointer-events-none');
        }, 2000);
    } catch (err) {
        console.error('Failed to copy: ', err);
    }
});
