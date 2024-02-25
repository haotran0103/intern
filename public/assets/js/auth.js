function refreshToken() {
    return fetch('api/v1/auth/refresh', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Authorization': `Bearer ${sessionStorage.getItem('accessToken')}`
      },
      body: JSON.stringify({ refresh_token: sessionStorage.getItem('refreshToken') })
    })
    .then(response => response.json())
    .then(data => {
      // Store the new tokens
      sessionStorage.setItem('accessToken', data.access_token);
      sessionStorage.setItem('refreshToken', data.refresh_token);
      return data.access_token;
    });
  }
  
  function makeRequest(url, options) {
    return fetch(url, options)
      .then(response => {
        if (response.status === 401) {
          // Token has expired, try to refresh it
          return refreshToken().then(newToken => {
            // Retry the original request with the new token
            options.headers['Authorization'] = `Bearer ${newToken}`;
            return fetch(url, options);
          });
        } else {
          // Token is still valid, process the response
          return response.json();
        }
      });
  }
  