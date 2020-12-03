<template>
	<div class="container">
		<v-btn color="primary" class="my-6" @click="modal('Absen')">Absen</v-btn>

		<v-card>
			<v-data-table
				:items="data"
				:loading="table.loading"
				:headers="table.header"
			>
				<template v-slot:item.actions="{ item }">
					<v-btn text color="warning" @click="modal('Edit', item)" class="mr-3">
						<v-icon>mdi-pencil</v-icon>
					</v-btn>
					<v-btn text color="error" @click="modal('Delete', item)">
						<v-icon>mdi-trash-can</v-icon>
					</v-btn>
				</template>
			</v-data-table>
		</v-card>

		<v-dialog v-model="dialog.active" width="500">
			<v-card>
				<v-card-title>{{ dialog.title }}</v-card-title>
				<v-form class="mx-6 py-6" ref="form">
					<div v-if="dialog.title == 'Absen'">
						<v-menu
							ref="menu"
							v-model="dialog.form.masukInput"
							:close-on-content-click="false"
							:nudge-right="40"
							transition="scale-transition"
							offset-y
							max-width="290px"
							min-width="290px"
						>
							<template v-slot:activator="{ on, attrs }">
								<v-text-field
									v-model="dialog.form.masuk"
									label="Masuk"
									readonly
									v-bind="attrs"
									v-on="on"
									outlined
									:rules="[
										v => !!v || 'Required'
									]"
								/>
							</template>
							<v-time-picker
								v-if="dialog.form.masukInput"
								v-model="dialog.form.masuk"
								full-width
								@click:minute="$refs.menu.save()"
								format="24hr"
								scrollable
							/>
						</v-menu>
					</div>
					<div v-else-if="dialog.title == 'Delete'" class="mt-n3 mb-6">Are you sure?</div>
					<div v-else class="mt-n3">
						<v-row>
							<v-col>
								<v-menu
									ref="menu"
									v-model="dialog.form.masukInput"
									:close-on-content-click="false"
									:nudge-right="40"
									transition="scale-transition"
									offset-y
									max-width="290px"
									min-width="290px"
								>
									<template v-slot:activator="{ on, attrs }">
										<v-text-field
											v-model="dialog.form.masuk"
											label="Masuk"
											readonly
											v-bind="attrs"
											v-on="on"
											outlined
											hide-details
										/>
									</template>
									<v-time-picker
										v-if="dialog.form.masukInput"
										v-model="dialog.form.masuk"
										full-width
										@click:minute="$refs.menu.save()"
										format="24hr"
										scrollable
									/>
								</v-menu>
							</v-col>
							<v-col>
								<v-menu
									ref="menu"
									v-model="dialog.form.pulangInput"
									:close-on-content-click="false"
									:nudge-right="40"
									transition="scale-transition"
									offset-y
									max-width="290px"
									min-width="290px"
								>
									<template v-slot:activator="{ on, attrs }">
										<v-text-field
											v-model="dialog.form.pulang"
											label="Pulang"
											readonly
											v-bind="attrs"
											v-on="on"
											outlined
											hide-details
										/>
									</template>
									<v-time-picker
										v-if="dialog.form.pulangInput"
										v-model="dialog.form.pulang"
										full-width
										@click:minute="$refs.menu.save()"
										format="24hr"
										scrollable
									/>
								</v-menu>
							</v-col>
						</v-row>
						<v-row>
							<v-col>
								<v-menu
									ref="menu"
									v-model="dialog.form.istirahatStartInput"
									:close-on-content-click="false"
									:nudge-right="40"
									transition="scale-transition"
									offset-y
									max-width="290px"
									min-width="290px"
								>
									<template v-slot:activator="{ on, attrs }">
										<v-text-field
											v-model="dialog.form.istirahatStart"
											label="Mulai istirahat"
											readonly
											v-bind="attrs"
											v-on="on"
											outlined
											hide-details
										/>
									</template>
									<v-time-picker
										v-if="dialog.form.istirahatStartInput"
										v-model="dialog.form.istirahatStart"
										full-width
										@click:minute="$refs.menu.save()"
										format="24hr"
										scrollable
									/>
								</v-menu>
							</v-col>
							<v-col>
								<v-menu
									ref="menu"
									v-model="dialog.form.istirahatEndInput"
									:close-on-content-click="false"
									:nudge-right="40"
									transition="scale-transition"
									offset-y
									max-width="290px"
									min-width="290px"
								>
									<template v-slot:activator="{ on, attrs }">
										<v-text-field
											v-model="dialog.form.istirahatEnd"
											label="Selesai istirahat"
											readonly
											v-bind="attrs"
											v-on="on"
											outlined
											hide-details
										/>
									</template>
									<v-time-picker
										v-if="dialog.form.istirahatEndInput"
										v-model="dialog.form.istirahatEnd"
										full-width
										@click:minute="$refs.menu.save()"
										format="24hr"
										scrollable
									/>
								</v-menu>
							</v-col>
						</v-row>
					</div>
					<v-textarea
						label="Keterangan"
						v-model="dialog.form.ket"
						outlined
						class="mt-3"
						v-if="dialog.title != 'Delete'"
					/>
					<div>
						<v-btn color="error" @click="dialog.active = false">Cancel</v-btn>
						<v-btn
							color="primary"
							@click="absen()"
							class="float-right"
							:loading="dialog.loading"
							v-if="dialog.title == 'Absen'"
						>
							Absen
						</v-btn>
						<v-btn
							color="error"
							@click="absen()"
							class="float-right"
							:loading="dialog.loading"
							v-if="dialog.title == 'Delete'"
						>
							Delete
						</v-btn>
						<v-btn
							color="warning"
							@click="absen()"
							class="float-right"
							:loading="dialog.loading"
							v-if="dialog.title == 'Edit'"
						>
							Edit
						</v-btn>
					</div>
				</v-form>
			</v-card>
		</v-dialog>
	</div>
</template>

<script>
export default {
	data () {
		return {
			data: [],
			table: {
				loading: true,
				header: [
					{
						text: 'Tanggal',
						value: 'date'
					},
					{
						text: 'Masuk',
						value: 'start',
						sortable: false
					},
					{
						text: 'Istirahat',
						value: 'istirahat',
						sortable: false
					},
					{
						text: 'Pulang',
						value: 'end',
						sortable: false
					},
					{
						text: 'Keterangan',
						value: 'ket',
						sortable: false
					},
					{
						text: 'Lama',
						value: 'lama',
						sortable: false
					},
					{
						value: 'actions',
						sortable: false
					}
				]
			},
			dialog: {
				active: false,
				form: {
					key: null,
					masuk: '',
					masukInput: false,
					istirahatStart: '',
					istirahatStartInput: false,
					istirahatEnd: '',
					istirahatEndInput: false,
					pulang: '',
					pulangInput: false,
					ket: ''
				},
				loading: false,
				title: ''
			}
		}
	},
	created () {
		this.getData()
	},
	methods: {
		getData () {
			let self = this
			this.$api.get('getData.php')
				.then(function (res) {
					(res.data.error) ? self.$emit('getAlert', res.data.msg, 'error') : self.data = res.data.data
				})
			this.table.loading = false
		},
		absen: async function () {
			if (this.$refs.form.validate()) {
				this.dialog.loading = true

				let fd = new FormData()
				let api
				switch (this.dialog.title) {
					case 'Absen':
						fd.append('start', this.dialog.form.masuk)
						fd.append('ket', this.dialog.form.ket)
						api = 'add.php'
						break
					case 'Delete':
						fd.append('key', this.dialog.form.key)
						api = 'delete.php'
						break
					case 'Edit':
						fd.append('key', this.dialog.form.key)
						fd.append('start', this.dialog.form.masuk)
						fd.append('end', this.dialog.form.pulang)
						fd.append('istirahatStart', this.dialog.form.istirahatStart)
						fd.append('istirahatEnd', this.dialog.form.istirahatEnd)
						fd.append('ket', this.dialog.form.ket)
						api = 'edit.php'
						break
				}

				let res = await this.$api.post(api, fd)
				if (res.data.error) {
					this.$emit('getAlert', res.data.msg, 'error')
				} else {
					this.$emit('getAlert', 'Success', 'success')
					this.getData()
				}
				this.dialog.loading = this.dialog.active = false
			}
		},
		modal (title, data) {
			this.dialog.active = true
			this.dialog.title = title
			if (data) {
				this.dialog.form.key = data.key
				if (title == 'Edit') {
					this.dialog.form.ket = data.ket
					this.dialog.form.masuk = data.start
					this.dialog.form.pulang = data.end
					this.dialog.form.istirahatStart = data.startIstirahat
					this.dialog.form.istirahatEnd = data.endIstirahat
				}
			}
			if (title == 'Absen') this.dialog.form.masuk = null
		}
	}
}
</script>
