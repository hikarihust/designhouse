export default function({ $axios, redirect, app  }) {

  $axios.interceptors.response.use(
    function(response) {
      return response;
    },
    function(error) {
      const code = parseInt(error.response && error.response.status);

      if ([401, 403].includes(code)) {
        app.$auth.logout();
      }

      return Promise.reject(error);
    }
  );

  $axios.setToken('access_token')

  $axios.onResponse(config => {
    $axios.setHeader('Access-Control-Allow-Origin', 'http://designhouse.xyz')
  })

  $axios.setBaseURL(process.env.API_URL);
}
