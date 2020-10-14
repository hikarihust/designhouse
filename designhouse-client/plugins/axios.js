export default function({ $axios, redirect }) {
  $axios.setToken('access_token')

  $axios.onResponse(config => {
    $axios.setHeader('Access-Control-Allow-Origin', 'http://designhouse.xyz')
  })

  $axios.setBaseURL(process.env.API_URL);
}
