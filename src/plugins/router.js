import Vue from 'vue'
import VueRouter from 'vue-router'

Vue.use(VueRouter)

const routesHome = [
	{
		path: '/',
		component: () => import( /* webpackChunkName: "Index" */ '@/components/index')
	},
]

const routesEtc = [
	{
		path: '*',
		component: () => import( /* webpackChunkName: "NotFound" */ '@/components/notFound')
	}
]

const routes = [].concat(routesHome, routesEtc)

const router = new VueRouter({
	mode: 'history',
	base: process.env.BASE_URL,
	routes
})

export default router
