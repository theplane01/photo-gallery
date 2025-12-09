function toggleLike(photoId, isLiked) {
    console.log('Toggle like called:', {photoId, isLiked});
    
    const likeBtn = document.querySelector(`.like-btn-${photoId}`);
    const likeCount = document.querySelector(`.like-count-${photoId}`);
    const form = document.querySelector(`.like-form[data-photo-id="${photoId}"]`);
    
    if (!likeBtn || !likeCount || !form) {
        console.error('Elements not found!');
        return;
    }
    
    // Show loading state
    const originalHtml = likeBtn.innerHTML;
    likeBtn.innerHTML = '<i class="fas fa-spinner fa-spin text-sm"></i>';
    likeBtn.disabled = true;
    
    // Prepare form data
    const formData = new FormData(form);
    const action = isLiked ? 'unlike' : 'like';
    formData.set('action', action);
    
    console.log('Sending action:', action);
    
    // Send AJAX request
    fetch('../process/like_process.php', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            // Update like button appearance
            if (data.action === 'liked') {
                likeBtn.innerHTML = '<i class="fas fa-heart text-sm text-red-400"></i>';
                likeBtn.classList.add('text-red-400');
                likeBtn.classList.remove('text-white');
                
                // Update like count
                const currentCount = parseInt(likeCount.textContent) || 0;
                likeCount.textContent = currentCount + 1;
                
                // Update form action for next click
                form.querySelector('input[name="action"]').value = 'unlike';
            } else {
                likeBtn.innerHTML = '<i class="fas fa-heart text-sm text-white"></i>';
                likeBtn.classList.remove('text-red-400');
                likeBtn.classList.add('text-white');
                
                // Update like count
                const currentCount = parseInt(likeCount.textContent) || 0;
                likeCount.textContent = Math.max(0, currentCount - 1);
                
                // Update form action for next click
                form.querySelector('input[name="action"]').value = 'like';
            }
            
            console.log('Like updated successfully');
        } else {
            console.error('Error from server:', data.message);
            likeBtn.innerHTML = originalHtml;
        }
    })
    .catch(error => {
        console.error('Fetch error:', error);
        likeBtn.innerHTML = originalHtml;
    })
    .finally(() => {
        likeBtn.disabled = false;
    });
}