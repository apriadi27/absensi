<template>
	<v-app>
		<v-snackbar :color="alert.color" v-model="alert.active" top right>
			{{ alert.text }}
		</v-snackbar>
		<router-view @getAlert="getAlert" />
	</v-app>
</template>

<script>
export default {
	data () {
		return {
			alert: {
				active: false,
				text: '',
				color: 'error'
			}
		}
	},
	methods: {
		getAlert (msg, type) {
			this.alert.active = true
			this.alert.text = msg
			this.alert.color = type
		}
	}
}
</script>
