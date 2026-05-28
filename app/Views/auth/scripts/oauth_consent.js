(function() {
    const body = document.body;
    const isLoggedIn = body.getAttribute('data-logged-in') === 'true';
    if (!isLoggedIn) return;

    let accounts = JSON.parse(localStorage.getItem('sso_accounts') || '[]');
    let currentUsername = body.getAttribute('data-username');
    let currentFullName = body.getAttribute('data-full-name');
    let currentRole = body.getAttribute('data-role');
    let currentAvatar = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(currentFullName) + '&background=0ea5e9&color=fff&bold=true';
    
    let accountExists = accounts.some(function(acc) {
        return acc.username === currentUsername;
    });
    
    if (!accountExists) {
        accounts.push({
            username: currentUsername,
            full_name: currentFullName,
            role: currentRole,
            avatar: currentAvatar
        });
        localStorage.setItem('sso_accounts', JSON.stringify(accounts));
    }
})();
