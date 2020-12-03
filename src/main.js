import Vue from 'vue'
import App from './App.vue'
import router from './plugins/router'
import vuetify from './plugins/vuetify'
import axios from 'axios'

Vue.config.productionTip = false

Vue.prototype.$api = axios.create({
	baseURL: 'http://localhost/absensi/api/',
	responseType: 'json',
	headers: {
		'Content-Type': 'application/x-www-form-urlencoded'
	}
})

new Vue({
	vuetify,
	router,
	render: h => h(App),
}).$mount('#app')
